<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Attendance;
use App\Models\SelfService;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\EmployeeRegistration;
use App\Models\EmployeeDocument;
use App\Models\JobVacancy;
use App\Models\HrCompetencyInterview;
use App\Models\TechnicalInterview;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard.
     */
    public function index()
    {
        // Get current client directly from session to ensure synchronization
        $clientId = session('current_client_id');
        
        if (!$clientId) {
            return redirect()->route('clients.index')
                ->with('error', 'Please select a client first.');
        }
        
        $currentClient = Client::find($clientId);
        
        if (!$currentClient) {
            return redirect()->route('clients.index')
                ->with('error', 'Selected client not found.');
        }
        
        // Share with views to ensure consistency
        view()->share('currentClient', $currentClient);

        // Get statistics for current client
        $stats = $this->getClientStats($currentClient->id);
        
        // Get recent activities for current client
        $recentActivities = $this->getRecentActivities($currentClient->id);
        
        // Get alerts for current client
        $alerts = $this->getAlerts($currentClient->id);
        
        $quickActions = $this->getQuickActions($currentClient->id, $stats);

        return view('dashboard', compact('stats', 'recentActivities', 'alerts', 'quickActions', 'currentClient'));
    }

    /**
     * Display the HRIS dashboard.
     */
    public function hrisDashboard()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('clients.index')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);
        return view('hris.dashboard', compact('currentClient'));
    }

    /**
     * Get statistics for the HRIS dashboard.
     */
    public function getHrisStats()
    {
        $clientId = session('current_client_id');
        
        if (!$clientId) {
            return response()->json(['success' => false, 'message' => 'No client selected']);
        }

        // The models will automatically filter by client_id because of the BelongsToCurrentClient trait
        // as long as the session('current_client_id') is set, which it is in the middleware.

        return response()->json([
            'success' => true,
            'stats' => [
                'totalEmployees' => Employee::count(),
                'activeContracts' => Contract::where('status', 'active')->count(),
                'pendingApprovals' => EmployeeRegistration::where('status', 'submitted')->count(),
                'totalDocuments' => EmployeeDocument::count(),
                'userRegCount' => User::count(), // Users are filtered by client in their model too
                'clientRegCount' => ClientRegistration::count(),
                'jobVacancyCount' => JobVacancy::where('status', 'hr_approved')->count(),
                'hrInterviewCount' => HrCompetencyInterview::where('status', 'submitted')->count(),
                'techInterviewCount' => TechnicalInterview::where('status', 'submitted')->count(),
                'employeeRegCount' => EmployeeRegistration::count(),
                'documentCount' => EmployeeDocument::count(),
                'socialRecordsCount' => Employee::whereNotNull('nssf_number')->count(), // Simplified
                'trainingCount' => Employee::count(), // Placeholder
                'personnelIdCount' => Employee::count(), // Placeholder
                'contractMgmtCount' => Contract::count(),
                'employmentContractCount' => Contract::count(),
                'workflowCount' => EmployeeRegistration::where('status', 'submitted')->count() // Simplified
            ]
        ]);
    }

    /**
     * Get statistics for the current client.
     */
    private function getClientStats($clientId)
    {
        $employees = Employee::where('client_id', $clientId);
        $totalEmployees = $employees->count();
        
        // Employee breakdown
        $activeEmployees = $employees->where('status', 'active')->count();
        $onLeaveEmployees = $employees->where('status', 'on_leave')->count();
        $probationEmployees = $employees->where('status', 'probation')->count();
        $newHires = $employees->where('hire_date', '>=', now()->subMonth())->count();
        
        // Attendance stats
        $todayAttendance = Attendance::where('client_id', $clientId)
            ->where('attendance_date', now()->format('Y-m-d'))
            ->get();
        $presentToday = $todayAttendance->where('status', 'present')->count();
        $absentToday = $todayAttendance->where('status', 'absent')->count();
        $lateToday = $todayAttendance->where('status', 'late')->count();
        $attendanceRate = $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100, 1) : 0;
        
        // Payroll stats
        $currentMonthPayroll = Payroll::where('client_id', $clientId)
            ->where('payroll_period', now()->format('Y-m'))
            ->sum('net_pay');
        
        // Self-service requests
        $activeCases = SelfService::where('client_id', $clientId)
            ->where('status', 'pending')
            ->count();

        return [
            'total_employees' => $totalEmployees,
            'active_employees' => $activeEmployees,
            'on_leave_employees' => $onLeaveEmployees,
            'probation_employees' => $probationEmployees,
            'new_hires' => $newHires,
            'present_today' => $presentToday,
            'absent_today' => $absentToday,
            'late_today' => $lateToday,
            'attendance_rate' => $attendanceRate,
            'monthly_payroll' => $currentMonthPayroll,
            'active_cases' => $activeCases,
        ];
    }

    /**
     * Get recent activities for the current client.
     */
    private function getRecentActivities($clientId)
    {
        $activities = [];
        
        // Get recent self-service requests
        $recentRequests = SelfService::where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        foreach ($recentRequests as $request) {
            $activities[] = [
                'type' => 'request',
                'title' => $this->getRequestTitle($request),
                'description' => $request->title ?: ucfirst($request->request_type) . ' request submitted',
                'time' => $request->created_at->diffForHumans(),
                'status' => $request->status,
                'icon' => $this->getRequestIcon($request->request_type),
                'color' => $this->getRequestColor($request->status),
                'sort_at' => optional($request->created_at)->timestamp ?? 0,
                'link' => route('selfservice.index'),
            ];
        }

        // Get recent employee hires
        $recentHires = Employee::where('client_id', $clientId)
            ->orderBy('hire_date', 'desc')
            ->take(3)
            ->get();

        foreach ($recentHires as $employee) {
            $activityDate = $employee->hire_date ?? $employee->created_at;
            $activities[] = [
                'type' => 'hire',
                'title' => 'New Employee Onboarded',
                'description' => trim($employee->first_name . ' ' . $employee->last_name) . ' joined ' . ($employee->department ?: 'the team'),
                'time' => $activityDate ? $activityDate->diffForHumans() : 'Recently',
                'status' => 'completed',
                'icon' => 'user-plus',
                'color' => 'green',
                'sort_at' => optional($activityDate)->timestamp ?? 0,
                'link' => route('employees.index'),
            ];
        }

        $recentPayrolls = Payroll::where('client_id', $clientId)
            ->with('employee')
            ->orderByDesc('updated_at')
            ->take(3)
            ->get();

        foreach ($recentPayrolls as $payroll) {
            $employeeName = trim(($payroll->employee?->first_name ?? '') . ' ' . ($payroll->employee?->last_name ?? ''));
            $activities[] = [
                'type' => 'payroll',
                'title' => 'Payroll Record Updated',
                'description' => ($employeeName ?: 'Employee #' . $payroll->employee_id) . ' - ' . $this->formatPayrollPeriod($payroll->payroll_period),
                'time' => optional($payroll->updated_at ?? $payroll->created_at)->diffForHumans() ?? 'Recently',
                'status' => $payroll->status,
                'icon' => 'credit-card',
                'color' => $payroll->status === 'paid' ? 'green' : 'blue',
                'sort_at' => optional($payroll->updated_at ?? $payroll->created_at)->timestamp ?? 0,
                'link' => route('payroll.index'),
            ];
        }

        // Sort by actual timestamp instead of human-readable label
        usort($activities, function ($a, $b) {
            return ($b['sort_at'] ?? 0) <=> ($a['sort_at'] ?? 0);
        });

        return array_map(function ($activity) {
            unset($activity['sort_at']);
            return $activity;
        }, array_slice($activities, 0, 8));
    }

    /**
     * Get alerts for the current client.
     */
    private function getAlerts($clientId)
    {
        $alerts = [];
        $today = now()->toDateString();

        $activeEmployees = Employee::where('client_id', $clientId)
            ->where('status', 'active')
            ->count();

        $markedToday = Attendance::where('client_id', $clientId)
            ->whereDate('attendance_date', $today)
            ->distinct('employee_id')
            ->count('employee_id');

        if ($activeEmployees > 0 && $markedToday === 0) {
            $alerts[] = [
                'type' => 'attendance_missing',
                'title' => 'Attendance Not Submitted',
                'description' => 'No attendance has been recorded for today.',
                'severity' => 'critical',
                'icon' => 'clock',
                'color' => 'red',
                'link' => route('attendance.index', ['date' => $today]),
                'action_label' => 'Record Attendance',
            ];
        } elseif ($activeEmployees > 0 && $markedToday < $activeEmployees) {
            $alerts[] = [
                'type' => 'attendance_incomplete',
                'title' => 'Attendance Incomplete',
                'description' => $markedToday . ' of ' . $activeEmployees . ' active employees have been marked today.',
                'severity' => 'warning',
                'icon' => 'calendar',
                'color' => 'yellow',
                'link' => route('attendance.index', ['date' => $today]),
                'action_label' => 'Complete Attendance',
            ];
        }

        $currentPayrollCount = Payroll::where('client_id', $clientId)
            ->where('payroll_period', now()->format('Y-m'))
            ->count();

        if ($activeEmployees > 0 && $currentPayrollCount === 0) {
            $alerts[] = [
                'type' => 'payroll_missing',
                'title' => 'Payroll Not Processed',
                'description' => 'No payroll has been processed for ' . now()->format('F Y') . '.',
                'severity' => 'critical',
                'icon' => 'credit-card',
                'color' => 'red',
                'link' => route('payroll.index'),
                'action_label' => 'Process Payroll',
            ];
        }
        
        // Get employees with contracts expiring soon
        $expiringContracts = Employee::where('client_id', $clientId)
            ->where('termination_date', '<=', now()->addDays(30))
            ->where('termination_date', '>=', now())
            ->get();

        foreach ($expiringContracts as $employee) {
            $daysLeft = now()->diffInDays($employee->termination_date);
            $alerts[] = [
                'type' => 'contract_expiry',
                'title' => 'Contract Expiring',
                'description' => $employee->first_name . ' ' . $employee->last_name . ' - ' . $daysLeft . ' days remaining',
                'severity' => $daysLeft <= 7 ? 'critical' : 'warning',
                'icon' => 'alert-circle',
                'color' => $daysLeft <= 7 ? 'red' : 'yellow',
                'link' => route('employees.index'),
                'action_label' => 'Review Employee',
            ];
        }

        // Get pending self-service requests that need attention
        $pendingRequests = SelfService::where('client_id', $clientId)
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subDays(3))
            ->get();

        foreach ($pendingRequests as $request) {
            $alerts[] = [
                'type' => 'pending_request',
                'title' => 'Pending HR Approval',
                'description' => ucfirst($request->request_type) . ' request #' . str_pad($request->id, 3, '0', STR_PAD_LEFT),
                'severity' => 'warning',
                'icon' => 'alert-triangle',
                'color' => 'yellow',
                'link' => route('selfservice.index'),
                'action_label' => 'Review Request',
            ];
        }

        usort($alerts, function ($a, $b) {
            $severityRank = ['critical' => 2, 'warning' => 1, 'info' => 0];
            return ($severityRank[$b['severity']] ?? 0) <=> ($severityRank[$a['severity']] ?? 0);
        });

        return array_slice($alerts, 0, 5);
    }

    private function getQuickActions($clientId, array $stats): array
    {
        $pendingRequests = SelfService::where('client_id', $clientId)
            ->where('status', 'pending')
            ->count();

        return [
            [
                'label' => 'Add New Employee',
                'description' => 'Create and onboard a new employee profile.',
                'href' => route('employees.create'),
                'icon' => 'user-plus',
                'color' => 'blue',
                'badge' => 'HR',
            ],
            [
                'label' => 'Record Attendance',
                'description' => 'Capture attendance for today before payroll processing.',
                'href' => route('attendance.index', ['date' => now()->toDateString()]),
                'icon' => 'calendar',
                'color' => 'purple',
                'badge' => $stats['present_today'] . '/' . max(1, $stats['total_employees']),
            ],
            [
                'label' => 'Review Leave Requests',
                'description' => 'Check pending employee requests and approvals.',
                'href' => route('selfservice.index'),
                'icon' => 'clipboard',
                'color' => 'yellow',
                'badge' => $pendingRequests . ' pending',
            ],
            [
                'label' => 'Process Payroll',
                'description' => 'Generate, import, or update payroll for the current period.',
                'href' => route('payroll.index'),
                'icon' => 'credit-card',
                'color' => 'green',
                'badge' => now()->format('M Y'),
            ],
            [
                'label' => 'Create Case File',
                'description' => 'Open the case management workspace.',
                'href' => route('casemanagement.index'),
                'icon' => 'folder-plus',
                'color' => 'red',
                'badge' => 'Legal',
            ],
            [
                'label' => 'Compliance Reports',
                'description' => 'Review statutory filings and compliance output.',
                'href' => route('compliance.index'),
                'icon' => 'trending-up',
                'color' => 'indigo',
                'badge' => 'Reports',
            ],
        ];
    }

    private function formatPayrollPeriod(?string $period): string
    {
        if (!$period) {
            return 'Current Period';
        }

        try {
            return Carbon::createFromFormat('Y-m', $period)->format('F Y');
        } catch (\Throwable $e) {
            return $period;
        }
    }

    /**
     * Get request title based on type.
     */
    private function getRequestTitle($request)
    {
        $titles = [
            'leave' => 'Leave Request',
            'payslip' => 'Payslip Request',
            'contract' => 'Contract Request',
            'complaint' => 'Complaint Filed',
            'expense' => 'Expense Claim',
        ];

        return $titles[$request->request_type] ?? 'Request';
    }

    /**
     * Get request icon based on type.
     */
    private function getRequestIcon($type)
    {
        $icons = [
            'leave' => 'calendar',
            'payslip' => 'credit-card',
            'contract' => 'file-text',
            'complaint' => 'alert-circle',
            'expense' => 'dollar-sign',
        ];

        return $icons[$type] ?? 'file';
    }

    /**
     * Get request color based on status.
     */
    private function getRequestColor($status)
    {
        $colors = [
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'processed' => 'green',
            'in_progress' => 'blue',
        ];

        return $colors[$status] ?? 'gray';
    }
}
