<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SelfService;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Client;
use App\Models\User;
use App\Services\PayslipService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SelfServiceController extends Controller
{
    /**
     * Display the self-service dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $clientId = session('current_client_id');
        $canViewPayslips = $user->hasRole('super_admin') || $user->hasPermission('selfservice.payslip');
        
        // Get employee data for current user and client
        $employee = null;
        $recentRequests = collect(); // Initialize as empty collection
        $recentPayslips = collect(); // Initialize as empty collection
        
        if ($clientId) {
            // Find employee for current user in this client
            $employee = Employee::where('client_id', $clientId)
                ->where('email', $user->email)
                ->first();
            
            if ($employee) {
                // Get recent self-service requests
                $recentRequests = SelfService::where('client_id', $clientId)
                    ->where('employee_id', $employee->id)
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();

                // Only expose payslip data to users holding the payslip permission.
                if ($canViewPayslips) {
                    $recentPayslips = Payroll::where('client_id', $clientId)
                        ->where('employee_id', $employee->id)
                        ->orderBy('pay_date', 'desc')
                        ->take(3)
                        ->get();
                }
            }
        }
        
        return view('selfservice.index', compact('employee', 'recentRequests', 'recentPayslips', 'canViewPayslips'));
    }
    
    /**
     * Show leave request form.
     */
    public function leave()
    {
        $clientId = session('current_client_id');
        $employee = null;
        
        if ($clientId) {
            $user = Auth::user();
            $employee = Employee::where('client_id', $clientId)
                ->where('email', $user->email)
                ->with(['selfServiceRequests' => function($query) {
                    $query->where('request_type', 'leave')->orderBy('created_at', 'desc');
                }])
                ->first();
        }
        
        return view('selfservice.leave', compact('employee'));
    }
    
    /**
     * Store leave request.
     */
    public function storeLeave(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
        ]);
        
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $user = Auth::user();
        
        $employee = Employee::where('client_id', $clientId)
            ->where('email', $user->email)
            ->first();
        
        if (!$employee) {
            return back()->with('error', 'Employee record not found for current client.');
        }
        
        $daysRequested = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) + 1;
        
        SelfService::create([
            'client_id' => $clientId,
            'employee_id' => $employee->id,
            'request_type' => 'leave',
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_requested' => $daysRequested,
            'status' => 'pending',
            'request_date' => now(),
        ]);
        
        return back()->with('success', 'Leave request submitted successfully!');
    }
    
    /**
     * Show payslip download page.
     */
    public function payslip(Request $request, PayslipService $service)
    {
        $clientId = $clientId = session('current_client_id');
        $employee = $this->resolveEmployee($clientId);

        $payslips = collect();
        $breakdowns = [];
        $years = collect();
        $statuses = collect();
        $stats = [
            'count' => 0,
            'gross' => 0.0,
            'net' => 0.0,
            'deductions' => 0.0,
            'paye' => 0.0,
            'average_net' => 0.0,
            'latest_net' => 0.0,
            'latest_period' => null,
        ];

        if ($clientId && $employee) {
            $allPayslips = Payroll::where('client_id', $clientId)
                ->where('employee_id', $employee->id)
                ->orderBy('pay_date', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            $years = $allPayslips
                ->map(fn ($p) => substr((string) $p->payroll_period, 0, 4))
                ->filter(fn ($y) => preg_match('/^\d{4}$/', (string) $y))
                ->unique()
                ->sortDesc()
                ->values();

            $statuses = $allPayslips->pluck('status')->filter()->unique()->values();

            $year = $request->filled('year') && preg_match('/^\d{4}$/', (string) $request->year)
                ? (string) $request->year
                : null;
            $month = $request->filled('month') && preg_match('/^(0?[1-9]|1[0-2])$/', (string) $request->month)
                ? str_pad((string) $request->month, 2, '0', STR_PAD_LEFT)
                : null;
            $status = $request->filled('status') && $statuses->contains($request->status)
                ? (string) $request->status
                : null;

            $payslips = $allPayslips
                ->filter(function (Payroll $p) use ($year, $month, $status) {
                    if ($year && substr((string) $p->payroll_period, 0, 4) !== $year) {
                        return false;
                    }
                    if ($month && substr((string) $p->payroll_period, 5, 2) !== $month) {
                        return false;
                    }
                    if ($status && $p->status !== $status) {
                        return false;
                    }
                    return true;
                })
                ->values();

            foreach ($payslips as $payslip) {
                $breakdowns[$payslip->id] = $service->build($payslip, $employee, $clientId ? Client::find($clientId) : null);
            }

            $stats = [
                'count' => $payslips->count(),
                'gross' => (float) $payslips->sum('gross_pay'),
                'net' => (float) $payslips->sum('net_pay'),
                'deductions' => (float) $payslips->sum('total_deductions'),
                'paye' => (float) $payslips->sum('tax_deductions'),
                'average_net' => $payslips->count() ? round((float) $payslips->avg('net_pay'), 2) : 0.0,
                'latest_net' => (float) ($payslips->first()->net_pay ?? 0),
                'latest_period' => $payslips->first()->payroll_period ?? null,
            ];
        }

        $currentClient = $clientId ? Client::find($clientId) : null;

        return view('selfservice.payslip', [
            'employee' => $employee,
            'currentClient' => $currentClient,
            'payslips' => $payslips,
            'breakdowns' => $breakdowns,
            'years' => $years,
            'statuses' => $statuses,
            'stats' => $stats,
        ]);
    }

    /**
     * Display a single payslip as a printable document.
     */
    public function showPayslip(Request $request, Payroll $payroll, PayslipService $service)
    {
        [$employee, $client] = $this->authorizePayslip($payroll);

        $data = $service->build($payroll, $employee, $client);

        return view('selfservice.payslip-print', [
            'data' => $data,
            'employee' => $employee,
            'client' => $client,
            'labels' => $service->labels(),
            'forPdf' => false,
        ]);
    }

    /**
     * Download a single payslip as a PDF.
     */
    public function downloadPayslip(Request $request, Payroll $payroll, PayslipService $service)
    {
        [$employee, $client] = $this->authorizePayslip($payroll);

        $data = $service->build($payroll, $employee, $client);

        $pdf = Pdf::loadView('selfservice.payslip-print', [
            'data' => $data,
            'employee' => $employee,
            'client' => $client,
            'labels' => $service->labels(),
            'forPdf' => true,
        ])->setPaper('a4');

        $filename = 'Payslip_' . $employee->employee_id . '_' . $payroll->payroll_period . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Download every available payslip for the logged-in employee as one PDF.
     */
    public function downloadAllPayslips(Request $request, PayslipService $service)
    {
        $clientId = session('current_client_id');
        $employee = $this->resolveEmployee($clientId);

        if (!$employee) {
            return back()->with('error', 'Employee record not found for current client.');
        }

        $payrolls = Payroll::where('client_id', $clientId)
            ->where('employee_id', $employee->id)
            ->orderBy('pay_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        if ($payrolls->isEmpty()) {
            return back()->with('error', 'No payslips are available to download yet.');
        }

        $client = Client::find($clientId);
        $rows = $payrolls
            ->map(fn (Payroll $p) => $service->build($p, $employee, $client))
            ->all();

        $pdf = Pdf::loadView('selfservice.payslip-batch', [
            'rows' => $rows,
            'client' => $client,
            'employee' => $employee,
            'labels' => $service->labels(),
            'forPdf' => true,
        ])->setPaper('a4');

        $filename = 'Payslips_' . $employee->employee_id . '_' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Request payslip.
     */
    public function requestPayslip(Request $request)
    {
        $validated = $request->validate([
            'payroll_period' => ['required', 'regex:/^\d{4}-\d{2}$/'],
        ], [
            'payroll_period.regex' => 'Please select a valid payroll period.',
        ]);
        
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $employee = $this->resolveEmployee($clientId);
        
        if (!$employee) {
            return back()->with('error', 'Employee record not found for current client.');
        }

        $periodLabel = Carbon::createFromFormat('Y-m', $validated['payroll_period'])->format('F Y');
        
        SelfService::create([
            'client_id' => $clientId,
            'employee_id' => $employee->id,
            'request_type' => 'payslip',
            'title' => 'Payslip Request - ' . $periodLabel,
            'description' => 'Request for payslip for ' . $periodLabel,
            'status' => 'pending',
            'request_date' => now(),
        ]);
        
        return back()->with('success', 'Payslip request submitted successfully!');
    }
    
    /**
     * Show contract view page.
     */
    public function contract()
    {
        $clientId = session('current_client_id');
        $employee = null;
        
        if ($clientId) {
            $user = Auth::user();
            $employee = Employee::where('client_id', $clientId)
                ->where('email', $user->email)
                ->first();
        }
        
        return view('selfservice.contract', compact('employee'));
    }
    
    /**
     * Request contract copy.
     */
    public function requestContract(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);
        
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $user = Auth::user();
        
        $employee = Employee::where('client_id', $clientId)
            ->where('email', $user->email)
            ->first();
        
        if (!$employee) {
            return back()->with('error', 'Employee record not found for current client.');
        }
        
        SelfService::create([
            'client_id' => $clientId,
            'employee_id' => $employee->id,
            'request_type' => 'contract',
            'title' => 'Employment Contract Copy',
            'description' => 'Request for copy of employment contract: ' . $request->reason,
            'status' => 'pending',
            'request_date' => now(),
        ]);
        
        return back()->with('success', 'Contract request submitted successfully!');
    }
    
    /**
     * Show complaint form.
     */
    public function complaint()
    {
        $clientId = session('current_client_id');
        $employee = null;
        
        if ($clientId) {
            $user = Auth::user();
            $employee = Employee::where('client_id', $clientId)
                ->where('email', $user->email)
                ->with(['selfServiceRequests' => function($query) {
                    $query->where('request_type', 'complaint')->orderBy('created_at', 'desc');
                }])
                ->first();
        }
        
        return view('selfservice.complaint', compact('employee'));
    }
    
    /**
     * Store complaint.
     */
    public function storeComplaint(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $user = Auth::user();
        
        $employee = Employee::where('client_id', $clientId)
            ->where('email', $user->email)
            ->first();
        
        if (!$employee) {
            return back()->with('error', 'Employee record not found for current client.');
        }
        
        SelfService::create([
            'client_id' => $clientId,
            'employee_id' => $employee->id,
            'request_type' => 'complaint',
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending',
            'request_date' => now(),
        ]);
        
        return back()->with('success', 'Complaint submitted successfully!');
    }
    
    /**
     * Show profile update form.
     */
    public function profile()
    {
        $user = Auth::user()->load('roles');
        $clientId = session('current_client_id');
        $employee = null;
        
        if ($clientId) {
            $employee = Employee::where('client_id', $clientId)
                ->where('email', $user->email)
                ->first();
        }
        
        return view('selfservice.profile', compact('user', 'employee'));
    }
    
    /**
     * Update profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        // Update user profile
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
        ]);
        
        // Update employee record if exists
        $clientId = session('current_client_id');
        if ($clientId) {
            $employee = Employee::where('client_id', $clientId)
                ->where('email', $user->email)
                ->first();
            
            if ($employee) {
                $employee->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'address' => $request->address,
                    'city' => $request->city,
                    'country' => $request->country,
                    'postal_code' => $request->postal_code,
                    'emergency_contact_name' => $request->emergency_contact_name,
                    'emergency_contact_phone' => $request->emergency_contact_phone,
                ]);
            }
        }
        
        return back()->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Show expense claim form.
     */
    public function expense()
    {
        $clientId = session('current_client_id');
        $employee = null;
        
        if ($clientId) {
            $user = Auth::user();
            $employee = Employee::where('client_id', $clientId)
                ->where('email', $user->email)
                ->first();
        }
        
        return view('selfservice.expense', compact('employee'));
    }
    
    /**
     * Store expense claim.
     */
    public function storeExpense(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);
        
        $clientId = session('current_client_id');
        $user = Auth::user();
        
        $employee = Employee::where('client_id', $clientId)
            ->where('email', $user->email)
            ->first();
        
        if (!$employee) {
            return back()->with('error', 'Employee record not found for current client.');
        }
        
        SelfService::create([
            'client_id' => $clientId,
            'employee_id' => $employee->id,
            'request_type' => 'expense_claim',
            'title' => $request->title,
            'description' => $request->description,
            'amount' => $request->amount,
            'status' => 'pending',
            'request_date' => now(),
        ]);
        
        return back()->with('success', 'Expense claim submitted successfully!');
    }

    /**
     * Resolve the employee linked to the authenticated user for a client.
     */
    private function resolveEmployee(?int $clientId): ?Employee
    {
        if (!$clientId) {
            return null;
        }

        $user = Auth::user();
        if (!$user) {
            return null;
        }

        return Employee::where('client_id', $clientId)
            ->where('email', $user->email)
            ->first();
    }

    /**
     * Ensure a payroll record belongs to the logged-in employee. Aborts with a
     * 404 for anything else so payslips can never leak across tenants/users.
     *
     * @return array{0: Employee, 1: ?Client}
     */
    private function authorizePayslip(Payroll $payroll): array
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            abort(403, 'Please select a client first.');
        }

        $employee = $this->resolveEmployee((int) $clientId);
        if (!$employee) {
            abort(404, 'Employee record not found for current client.');
        }

        if ((int) $payroll->client_id !== (int) $clientId
            || (int) $payroll->employee_id !== (int) $employee->id) {
            abort(404, 'Payslip not found.');
        }

        return [$employee, Client::find($clientId)];
    }
}
