<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class CompensationController extends Controller
{
    public function index(Request $request)
    {
        $clientId = session('current_client_id');
        $hasBenefitsColumn = Schema::hasColumn('employees', 'benefits');

        $query = Employee::query();
        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('employee_id', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('department', 'like', '%' . $search . '%')
                    ->orWhere('position', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->get('department'));
        }

        if ($request->filled('min_salary')) {
            $query->where('salary', '>=', (float) $request->get('min_salary'));
        }

        if ($request->filled('max_salary')) {
            $query->where('salary', '<=', (float) $request->get('max_salary'));
        }

        $employees = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $lastPayrollByEmployeeId = [];
        $pageEmployeeIds = $employees->getCollection()->pluck('id')->all();
        if (!empty($pageEmployeeIds)) {
            $latestPayrolls = Payroll::where('client_id', $clientId)
                ->whereIn('employee_id', $pageEmployeeIds)
                ->orderBy('pay_date', 'desc')
                ->get(['employee_id', 'payroll_period', 'net_pay', 'gross_pay', 'pay_date']);

            foreach ($latestPayrolls as $p) {
                if (!isset($lastPayrollByEmployeeId[$p->employee_id])) {
                    $lastPayrollByEmployeeId[$p->employee_id] = $p;
                }
            }
        }

        $pageEmployees = $employees->getCollection()->map(function ($e) {
            return [
                'id' => $e->id,
                'employee_id' => $e->employee_id,
                'first_name' => $e->first_name,
                'last_name' => $e->last_name,
                'email' => $e->email,
                'department' => $e->department,
                'position' => $e->position,
                'salary' => $e->salary,
                'currency' => $e->currency,
                'payment_frequency' => $e->payment_frequency,
                'benefits' => is_array($e->benefits) ? $e->benefits : [],
            ];
        })->values();

        $statsQuery = Employee::query();
        if ($clientId) {
            $statsQuery->where('client_id', $clientId);
        }

        $totalEmployees = (clone $statsQuery)->count();
        $avgSalary = (clone $statsQuery)->whereNotNull('salary')->avg('salary') ?? 0;
        $totalSalaryTzs = (clone $statsQuery)->where('currency', 'TZS')->sum('salary');
        $withBenefits = $hasBenefitsColumn ? (clone $statsQuery)->whereNotNull('benefits')->count() : 0;

        $departments = (clone $statsQuery)
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->select('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department')
            ->values()
            ->all();

        $stats = [
            'total_employees' => $totalEmployees,
            'avg_salary' => $avgSalary,
            'total_salary_tzs' => $totalSalaryTzs,
            'employees_with_benefits' => $withBenefits,
        ];

        return view('compensation.index', compact('employees', 'departments', 'stats', 'pageEmployees', 'lastPayrollByEmployeeId'));
    }

    public function employees(Request $request)
    {
        $clientId = session('current_client_id');
        $hasBenefitsColumn = Schema::hasColumn('employees', 'benefits');

        $query = Employee::query();
        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->get('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('first_name', 'like', '%' . $q . '%')
                    ->orWhere('last_name', 'like', '%' . $q . '%')
                    ->orWhere('employee_id', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%');
            });
        }

        $columns = [
            'id',
            'employee_id',
            'first_name',
            'last_name',
            'email',
            'department',
            'position',
            'salary',
            'currency',
            'payment_frequency',
        ];
        if ($hasBenefitsColumn) {
            $columns[] = 'benefits';
        }

        $employees = $query->orderBy('created_at', 'desc')
            ->limit(50)
            ->get($columns);

        return response()->json([
            'success' => true,
            'data' => $employees,
        ]);
    }

    public function updateEmployee(Request $request, Employee $employee)
    {
        $clientId = session('current_client_id');
        if ($clientId && (int) $employee->client_id !== (int) $clientId) {
            return response()->json(['success' => false, 'message' => 'Employee not found.'], 404);
        }

        $hasBenefitsColumn = Schema::hasColumn('employees', 'benefits');

        $validated = $request->validate([
            'salary' => 'nullable|numeric|min:0',
            'currency' => ['nullable', 'string', 'max:3', Rule::in(['TZS', 'USD', 'EUR', 'GBP'])],
            'payment_frequency' => ['nullable', 'string', Rule::in(['monthly', 'bi-weekly', 'weekly'])],
            'benefits' => $hasBenefitsColumn ? 'nullable|array' : 'nullable',
            'benefits.*' => $hasBenefitsColumn ? 'string|max:100' : 'nullable',
        ]);

        $employee->fill([
            'salary' => $validated['salary'] ?? $employee->salary,
            'currency' => $validated['currency'] ?? ($employee->currency ?: 'TZS'),
            'payment_frequency' => $validated['payment_frequency'] ?? ($employee->payment_frequency ?: 'monthly'),
        ]);

        if ($hasBenefitsColumn) {
            $employee->benefits = array_values(array_filter($validated['benefits'] ?? ($employee->benefits ?? []), fn ($v) => $v !== null && $v !== ''));
        }

        $employee->save();

        return response()->json([
            'success' => true,
            'message' => 'Compensation updated successfully.',
            'data' => $employee->only([
                'id',
                'employee_id',
                'first_name',
                'last_name',
                'department',
                'position',
                'salary',
                'currency',
                'payment_frequency',
                'benefits',
            ]),
        ]);
    }

    public function export(Request $request)
    {
        $clientId = session('current_client_id');
        $hasBenefitsColumn = Schema::hasColumn('employees', 'benefits');

        $query = Employee::query();
        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('employee_id', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('department', 'like', '%' . $search . '%')
                    ->orWhere('position', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->get('department'));
        }

        if ($request->filled('min_salary')) {
            $query->where('salary', '>=', (float) $request->get('min_salary'));
        }

        if ($request->filled('max_salary')) {
            $query->where('salary', '<=', (float) $request->get('max_salary'));
        }

        $columns = [
            'employee_id',
            'first_name',
            'last_name',
            'email',
            'department',
            'position',
            'salary',
            'currency',
            'payment_frequency',
        ];
        if ($hasBenefitsColumn) {
            $columns[] = 'benefits';
        }

        $employees = $query->orderBy('created_at', 'desc')->get($columns);

        $headers = [
            'Employee ID',
            'First Name',
            'Last Name',
            'Email',
            'Department',
            'Position',
            'Salary',
            'Currency',
            'Payment Frequency',
            'Benefits',
        ];

        $lines = [];
        $lines[] = $this->toCsvLine($headers);

        foreach ($employees as $employee) {
            $benefits = $hasBenefitsColumn && is_array($employee->benefits) ? implode('|', $employee->benefits) : '';
            $lines[] = $this->toCsvLine([
                $employee->employee_id,
                $employee->first_name,
                $employee->last_name,
                $employee->email,
                $employee->department,
                $employee->position,
                $employee->salary,
                $employee->currency,
                $employee->payment_frequency,
                $benefits,
            ]);
        }

        $csv = implode("\r\n", $lines) . "\r\n";

        $fileName = 'compensation_export_' . now()->format('Ymd_His') . '.csv';

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    private function toCsvLine(array $fields): string
    {
        $escaped = array_map(function ($value) {
            $value = $value === null ? '' : (string) $value;
            $value = str_replace('"', '""', $value);
            return '"' . $value . '"';
        }, $fields);

        return implode(',', $escaped);
    }
}
