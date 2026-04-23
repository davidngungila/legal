<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    /**
     * Display the onboarding page.
     */
    public function index()
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return redirect()->route('clients.index')
                ->with('error', 'Please select a client first.');
        }

        $client = Client::find($currentClient['id']);
        
        if (!$client) {
            return redirect()->route('clients.index')
                ->with('error', 'Client not found.');
        }

        // Get onboarding statistics
        $stats = $this->getOnboardingStats($currentClient['id']);
        
        // Get employees currently in onboarding
        $onboardingEmployees = Employee::where('client_id', $currentClient['id'])
            ->where('status', 'probation')
            ->with(['selfServiceRequests' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->get();

        return view('onboarding.index', compact('client', 'stats', 'onboardingEmployees'));
    }

    /**
     * Get onboarding statistics for the current client.
     */
    private function getOnboardingStats($clientId)
    {
        $employees = Employee::where('client_id', $clientId);
        
        // Active onboarding (employees in probation)
        $activeOnboarding = $employees->where('status', 'probation')->count();
        
        // Completed this month (employees who finished probation this month)
        $completedThisMonth = $employees->where('status', 'active')
            ->whereMonth('hire_date', now()->month)
            ->whereYear('hire_date', now()->year)
            ->count();
        
        // New hires this month
        $newHiresThisMonth = $employees
            ->whereMonth('hire_date', now()->month)
            ->whereYear('hire_date', now()->year)
            ->count();
        
        // Average onboarding completion rate
        $totalEmployees = $employees->count();
        $activeEmployees = $employees->where('status', 'active')->count();
        $completionRate = $totalEmployees > 0 ? round(($activeEmployees / $totalEmployees) * 100, 1) : 0;
        
        // Pending documentation
        $pendingDocumentation = $employees->where('status', 'probation')
            ->where(function($query) {
                $query->whereNull('contract_end_date')
                      ->orWhereNull('emergency_contact');
            })
            ->count();

        return [
            'active_onboarding' => $activeOnboarding,
            'completed_this_month' => $completedThisMonth,
            'new_hires_this_month' => $newHiresThisMonth,
            'completion_rate' => $completionRate,
            'pending_documentation' => $pendingDocumentation,
            'total_employees' => $totalEmployees,
        ];
    }

    /**
     * Start onboarding process for a new employee.
     */
    public function startOnboarding(Request $request)
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
            'contract_type' => 'required|string|in:permanent,contract,intern',
        ]);

        $employee = Employee::create(array_merge($validated, [
            'client_id' => $currentClient['id'],
            'employee_id' => 'EMP-' . str_pad(Employee::where('client_id', $currentClient['id'])->count() + 1, 4, '0', STR_PAD_LEFT),
            'status' => 'probation',
            'created_by' => Auth::id(),
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Onboarding process started successfully!',
            'employee' => $employee
        ]);
    }

    /**
     * Complete onboarding for an employee.
     */
    public function completeOnboarding(Request $request, $employeeId)
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $employee = Employee::where('client_id', $currentClient['id'])
            ->findOrFail($employeeId);

        $validated = $request->validate([
            'contract_end_date' => 'required|date',
            'emergency_contact' => 'required|string|max:255',
            'emergency_phone' => 'required|string|max:20',
            'bank_name' => 'required|string|max:255',
            'bank_account' => 'required|string|max:255',
        ]);

        $employee->update(array_merge($validated, [
            'status' => 'active',
            'updated_by' => Auth::id(),
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Onboarding completed successfully!',
            'employee' => $employee
        ]);
    }

    /**
     * Get onboarding progress for an employee.
     */
    public function getProgress($employeeId)
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $employee = Employee::where('client_id', $currentClient['id'])
            ->with(['selfServiceRequests' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->findOrFail($employeeId);

        $progress = $this->calculateOnboardingProgress($employee);

        return response()->json([
            'employee' => $employee,
            'progress' => $progress,
        ]);
    }

    /**
     * Calculate onboarding progress percentage.
     */
    private function calculateOnboardingProgress($employee)
    {
        $steps = [
            'personal_info' => $employee->first_name && $employee->last_name && $employee->email ? 20 : 0,
            'contact_info' => $employee->phone && $employee->address ? 15 : 0,
            'job_details' => $employee->position && $employee->department && $employee->salary ? 15 : 0,
            'contract_info' => $employee->contract_type && $employee->hire_date ? 15 : 0,
            'emergency_contact' => $employee->emergency_contact && $employee->emergency_phone ? 15 : 0,
            'bank_details' => $employee->bank_name && $employee->bank_account ? 10 : 0,
            'documentation' => $employee->documents_completed ? 10 : 0,
        ];

        return [
            'total' => array_sum($steps),
            'steps' => $steps,
            'percentage' => array_sum($steps),
        ];
    }
}
