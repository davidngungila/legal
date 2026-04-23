<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Client;

class OnboardingController
{
    /**
     * Display onboarding index
     */
    public function index()
    {
        $clientId = session('current_client_id');
        $employees = Employee::where('client_id', $clientId)
            ->where('status', 'probation')
            ->orderBy('hire_date', 'desc')
            ->paginate(20);
        
        return view('onboarding.index', compact('employees'));
    }

    /**
     * Start onboarding process
     */
    public function startOnboarding(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        
        // Update onboarding status
        $employee->update([
            'onboarding_status' => 'in_progress',
            'onboarding_started_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Onboarding process started successfully!'
        ]);
    }

    /**
     * Complete onboarding
     */
    public function completeOnboarding(Request $request, $employeeId)
    {
        $request->validate([
            'completion_notes' => 'nullable|string',
        ]);

        $employee = Employee::findOrFail($employeeId);
        
        // Update onboarding completion
        $employee->update([
            'status' => 'active',
            'onboarding_status' => 'completed',
            'onboarding_completed_at' => now(),
            'onboarding_notes' => $request->completion_notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Onboarding completed successfully!'
        ]);
    }

    /**
     * Get onboarding progress
     */
    public function getProgress($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        
        $progress = [
            'status' => $employee->onboarding_status ?? 'not_started',
            'started_at' => $employee->onboarding_started_at,
            'completed_at' => $employee->onboarding_completed_at,
            'completion_percentage' => $this->calculateCompletionPercentage($employee),
        ];

        return response()->json($progress);
    }

    /**
     * Calculate onboarding completion percentage
     */
    private function calculateCompletionPercentage($employee)
    {
        $steps = [
            'contract_signed' => $employee->contract_signed ?? false,
            'documents_submitted' => $employee->documents_submitted ?? false,
            'training_completed' => $employee->training_completed ?? false,
            'equipment_issued' => $employee->equipment_issued ?? false,
            'system_access_granted' => $employee->system_access_granted ?? false,
        ];

        $completedSteps = collect($steps)->filter()->count();
        $totalSteps = count($steps);

        return $totalSteps > 0 ? ($completedSteps / $totalSteps) * 100 : 0;
    }
}
