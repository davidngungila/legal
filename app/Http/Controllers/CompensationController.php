<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\SalaryStructure;
use App\Models\MeritReview;
use App\Models\Allowance;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
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

    public function salaryStructures()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = \App\Models\Client::find($clientId);

        $structures = SalaryStructure::where('client_id', $clientId)
            ->orderBy('name')
            ->get();

        $positions = Employee::where('client_id', $clientId)
            ->whereNotNull('position')
            ->where('position', '!=', '')
            ->select('position')
            ->distinct()
            ->orderBy('position')
            ->pluck('position')
            ->values()
            ->all();

        $stats = [
            'total' => $structures->count(),
            'active' => $structures->where('is_active', true)->count(),
            'avg_mid' => $structures->where('is_active', true)->avg('mid_salary') ?? 0,
            'positions' => count($positions),
        ];

        return view('compensation.salary-structures', compact('currentClient', 'structures', 'positions', 'stats'));
    }

    public function storeSalaryStructure(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'min_salary' => 'required|numeric|min:0',
            'mid_salary' => 'required|numeric|min:0',
            'max_salary' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            SalaryStructure::create([
                'client_id' => $clientId,
                'name' => $request->name,
                'position' => $request->position,
                'min_salary' => $request->min_salary,
                'mid_salary' => $request->mid_salary,
                'max_salary' => $request->max_salary,
                'currency' => $request->currency ?: 'TZS',
                'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('compensation.salary-structures')->with('success', 'Salary structure created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create salary structure: ' . $e->getMessage())->withInput();
        }
    }

    public function updateSalaryStructure(Request $request, $id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $structure = SalaryStructure::where('client_id', $clientId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'min_salary' => 'required|numeric|min:0',
            'mid_salary' => 'required|numeric|min:0',
            'max_salary' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $structure->update([
                'name' => $request->name,
                'position' => $request->position,
                'min_salary' => $request->min_salary,
                'mid_salary' => $request->mid_salary,
                'max_salary' => $request->max_salary,
                'currency' => $request->currency ?: 'TZS',
                'is_active' => $request->has('is_active') ? (bool) $request->is_active : false,
                'updated_by' => auth()->id(),
            ]);

            return redirect()->route('compensation.salary-structures')->with('success', 'Salary structure updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update salary structure: ' . $e->getMessage())->withInput();
        }
    }

    public function destroySalaryStructure($id)
    {
        $clientId = session('current_client_id');
        $structure = SalaryStructure::where('client_id', $clientId)->findOrFail($id);
        $structure->delete();

        return redirect()->route('compensation.salary-structures')->with('success', 'Salary structure deleted successfully!');
    }

    public function meritReview()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = \App\Models\Client::find($clientId);

        $reviews = MeritReview::with('employee')
            ->where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->get();

        $employees = Employee::where('client_id', $clientId)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'employee_id', 'first_name', 'last_name', 'salary', 'position', 'department']);

        $stats = [
            'total' => $reviews->count(),
            'approved' => $reviews->where('status', 'approved')->count(),
            'pending' => $reviews->where('status', 'draft')->count(),
            'avg_increment' => $reviews->where('status', 'approved')->avg('increment_percent') ?? 0,
        ];

        return view('compensation.merit-review', compact('currentClient', 'reviews', 'employees', 'stats'));
    }

    public function storeMeritReview(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'review_period' => 'nullable|string|max:50',
            'rating' => 'required|integer|min:1|max:5',
            'new_salary' => 'required|numeric|min:0',
            'reviewer_notes' => 'nullable|string',
            'status' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $employee = Employee::where('client_id', $clientId)->findOrFail($request->employee_id);
            $oldSalary = (float) ($employee->salary ?? 0);
            $newSalary = (float) $request->new_salary;
            $increment = $newSalary - $oldSalary;
            $incrementPercent = $oldSalary > 0 ? round(($increment / $oldSalary) * 100, 2) : 0;

            MeritReview::create([
                'client_id' => $clientId,
                'employee_id' => $employee->id,
                'review_period' => $request->review_period ?: now()->format('Y'),
                'rating' => $request->rating,
                'old_salary' => $oldSalary,
                'new_salary' => $newSalary,
                'increment_amount' => $increment,
                'increment_percent' => $incrementPercent,
                'reviewer_notes' => $request->reviewer_notes,
                'status' => $request->status ?: 'draft',
                'reviewed_by' => auth()->id(),
                'review_date' => now()->toDateString(),
            ]);

            return redirect()->route('compensation.merit-review')->with('success', 'Merit review created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create merit review: ' . $e->getMessage())->withInput();
        }
    }

    public function updateMeritReview(Request $request, $id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $review = MeritReview::where('client_id', $clientId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'review_period' => 'nullable|string|max:50',
            'rating' => 'required|integer|min:1|max:5',
            'new_salary' => 'required|numeric|min:0',
            'reviewer_notes' => 'nullable|string',
            'status' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $employee = Employee::where('client_id', $clientId)->findOrFail($request->employee_id);
            $oldSalary = (float) ($employee->salary ?? 0);
            $newSalary = (float) $request->new_salary;
            $increment = $newSalary - $oldSalary;
            $incrementPercent = $oldSalary > 0 ? round(($increment / $oldSalary) * 100, 2) : 0;

            $review->update([
                'employee_id' => $employee->id,
                'review_period' => $request->review_period ?: now()->format('Y'),
                'rating' => $request->rating,
                'old_salary' => $oldSalary,
                'new_salary' => $newSalary,
                'increment_amount' => $increment,
                'increment_percent' => $incrementPercent,
                'reviewer_notes' => $request->reviewer_notes,
                'status' => $request->status ?: 'draft',
                'reviewed_by' => auth()->id(),
                'review_date' => now()->toDateString(),
            ]);

            return redirect()->route('compensation.merit-review')->with('success', 'Merit review updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update merit review: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyMeritReview($id)
    {
        $clientId = session('current_client_id');
        $review = MeritReview::where('client_id', $clientId)->findOrFail($id);
        $review->delete();

        return redirect()->route('compensation.merit-review')->with('success', 'Merit review deleted successfully!');
    }

    public function allowances()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = \App\Models\Client::find($clientId);

        $allowances = Allowance::with('employee')
            ->where('client_id', $clientId)
            ->orderBy('name')
            ->get();

        $employees = Employee::where('client_id', $clientId)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'employee_id', 'first_name', 'last_name']);

        $monthlyCost = $allowances->sum(function ($a) {
            if ((bool) $a->is_active === false) {
                return 0;
            }
            $value = $a->type === 'percentage' ? (float) $a->percentage : (float) $a->amount;
            return $value;
        });

        $stats = [
            'total' => $allowances->count(),
            'active' => $allowances->where('is_active', true)->count(),
            'monthly_cost' => $monthlyCost,
            'taxable' => $allowances->where('is_taxable', true)->count(),
        ];

        return view('compensation.allowances', compact('currentClient', 'allowances', 'employees', 'stats'));
    }

    public function storeAllowance(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'nullable|exists:employees,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,percentage',
            'amount' => 'required_if:type,fixed|nullable|numeric|min:0',
            'percentage' => 'required_if:type,percentage|nullable|numeric|min:0|max:100',
            'currency' => 'nullable|string|max:3',
            'frequency' => 'nullable|string|max:20',
            'is_taxable' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'effective_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            Allowance::create([
                'client_id' => $clientId,
                'employee_id' => $request->employee_id ?: null,
                'name' => $request->name,
                'type' => $request->type,
                'amount' => $request->type === 'fixed' ? $request->amount : 0,
                'percentage' => $request->type === 'percentage' ? $request->percentage : 0,
                'currency' => $request->currency ?: 'TZS',
                'frequency' => $request->frequency ?: 'monthly',
                'is_taxable' => $request->boolean('is_taxable'),
                'is_active' => $request->boolean('is_active'),
                'effective_date' => $request->effective_date,
                'description' => $request->description,
            ]);

            return redirect()->route('compensation.allowances')->with('success', 'Allowance created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create allowance: ' . $e->getMessage())->withInput();
        }
    }

    public function updateAllowance(Request $request, $id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $allowance = Allowance::where('client_id', $clientId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'nullable|exists:employees,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,percentage',
            'amount' => 'required_if:type,fixed|nullable|numeric|min:0',
            'percentage' => 'required_if:type,percentage|nullable|numeric|min:0|max:100',
            'currency' => 'nullable|string|max:3',
            'frequency' => 'nullable|string|max:20',
            'is_taxable' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'effective_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $allowance->update([
                'employee_id' => $request->employee_id ?: null,
                'name' => $request->name,
                'type' => $request->type,
                'amount' => $request->type === 'fixed' ? $request->amount : 0,
                'percentage' => $request->type === 'percentage' ? $request->percentage : 0,
                'currency' => $request->currency ?: 'TZS',
                'frequency' => $request->frequency ?: 'monthly',
                'is_taxable' => $request->boolean('is_taxable'),
                'is_active' => $request->boolean('is_active'),
                'effective_date' => $request->effective_date,
                'description' => $request->description,
            ]);

            return redirect()->route('compensation.allowances')->with('success', 'Allowance updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update allowance: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyAllowance($id)
    {
        $clientId = session('current_client_id');
        $allowance = Allowance::where('client_id', $clientId)->findOrFail($id);
        $allowance->delete();

        return redirect()->route('compensation.allowances')->with('success', 'Allowance deleted successfully!');
    }

    public function loans()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = \App\Models\Client::find($clientId);

        $loans = Loan::with('employee')
            ->where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->get();

        $employees = Employee::where('client_id', $clientId)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'employee_id', 'first_name', 'last_name']);

        $stats = [
            'total' => $loans->count(),
            'active' => $loans->where('status', 'active')->count(),
            'settled' => $loans->where('status', 'settled')->count(),
            'outstanding' => $loans->whereIn('status', ['active', 'overdue'])->sum('remaining_balance'),
        ];

        return view('compensation.loans', compact('currentClient', 'loans', 'employees', 'stats'));
    }

    public function storeLoan(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'loan_type' => 'required|string|max:50',
            'principal_amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'installment_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $employee = Employee::where('client_id', $clientId)->findOrFail($request->employee_id);
            $principal = (float) $request->principal_amount;
            $interestRate = (float) $request->interest_rate;
            $totalRepayable = $principal + ($principal * $interestRate / 100);

            Loan::create([
                'client_id' => $clientId,
                'employee_id' => $employee->id,
                'loan_type' => $request->loan_type,
                'principal_amount' => $principal,
                'interest_rate' => $interestRate,
                'installment_amount' => $request->installment_amount,
                'total_repayable' => $totalRepayable,
                'remaining_balance' => $totalRepayable,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status ?: 'active',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'notes' => $request->notes,
            ]);

            return redirect()->route('compensation.loans')->with('success', 'Loan created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create loan: ' . $e->getMessage())->withInput();
        }
    }

    public function updateLoan(Request $request, $id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $loan = Loan::where('client_id', $clientId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'loan_type' => 'required|string|max:50',
            'principal_amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'installment_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $employee = Employee::where('client_id', $clientId)->findOrFail($request->employee_id);
            $principal = (float) $request->principal_amount;
            $interestRate = (float) $request->interest_rate;
            $totalRepayable = $principal + ($principal * $interestRate / 100);

            $loan->update([
                'employee_id' => $employee->id,
                'loan_type' => $request->loan_type,
                'principal_amount' => $principal,
                'interest_rate' => $interestRate,
                'installment_amount' => $request->installment_amount,
                'total_repayable' => $totalRepayable,
                'remaining_balance' => $request->filled('remaining_balance') ? $request->remaining_balance : $totalRepayable,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status ?: 'active',
                'notes' => $request->notes,
            ]);

            return redirect()->route('compensation.loans')->with('success', 'Loan updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update loan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyLoan($id)
    {
        $clientId = session('current_client_id');
        $loan = Loan::where('client_id', $clientId)->findOrFail($id);
        $loan->delete();

        return redirect()->route('compensation.loans')->with('success', 'Loan deleted successfully!');
    }
}
