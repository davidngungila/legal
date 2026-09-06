<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Attendance;
use App\Models\SelfService;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Department;
use App\Models\DisciplinaryCase;
use App\Models\ExitCase;
use App\Models\InductionTraining;
use App\Models\SocialRecord;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\EmployeeRegistration;
use App\Models\EmployeeDocument;
use App\Models\JobVacancy;
use App\Models\HrCompetencyInterview;
use App\Models\TechnicalInterview;
use App\Models\LeaveRequest;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard.
     *
     * The dashboard is role-aware. Employees see a personal self-service view,
     * line managers see their team, and management roles see the full HR
     * operations overview scoped by the permissions their role holds.
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

        $user = Auth::user();
        $dashboardType = $this->resolveDashboardType($user);

        if ($dashboardType === 'employee') {
            return $this->renderEmployeeDashboard($user, $currentClient);
        }

        if ($dashboardType === 'manager') {
            return $this->renderManagerDashboard($user, $currentClient);
        }

        return $this->renderManagementDashboard($currentClient, $user);
    }

    /**
     * Determine which dashboard persona the authenticated user should see.
     */
    private function resolveDashboardType($user): string
    {
        if ($user->hasRole('employee') && ! $this->isManagementUser($user)) {
            return 'employee';
        }

        if ($user->hasRole('line_manager') && ! $this->isManagementUser($user)) {
            return 'manager';
        }

        return 'management';
    }

    /**
     * Determine whether the user holds any management-level role.
     */
    private function isManagementUser($user): bool
    {
        return $user->hasRole('super_admin')
            || $user->hasRole('admin')
            || $user->hasRole('lead_hr_admin')
            || $user->hasRole('hr_officer')
            || $user->hasRole('finance_payroll_officer')
            || $user->hasRole('external_auditor');
    }

    /**
     * Render the full management/HR operations dashboard.
     */
    private function renderManagementDashboard(Client $currentClient, $user)
    {
        // All numbers are computed strictly for the selected client
        $stats = $this->getClientStats($currentClient->id);
        $charts = $this->getChartData($currentClient->id);
        $compliance = $this->getComplianceStats($currentClient->id, $stats['employees'], $currentClient);
        $events = $this->getUpcomingEvents($currentClient->id, $stats['employees']);
        $recentActivities = $this->getRecentActivities($currentClient->id);
        $alerts = $this->getAlerts($currentClient->id);
        $quickActions = $this->getQuickActions($currentClient->id, $stats);

        return view('dashboard', compact(
            'stats', 'charts', 'compliance', 'events',
            'recentActivities', 'alerts', 'quickActions', 'currentClient'
        ))->with('dashboardType', 'management');
    }

    /**
     * Render the employee self-service dashboard.
     */
    private function renderEmployeeDashboard($user, Client $currentClient)
    {
        $data = $this->getEmployeeDashboardData($user, $currentClient);

        return view('dashboard', $data + [
            'currentClient' => $currentClient,
            'dashboardType' => 'employee',
        ]);
    }

    /**
     * Render the line-manager team dashboard.
     */
    private function renderManagerDashboard($user, Client $currentClient)
    {
        $data = $this->getManagerDashboardData($user, $currentClient);

        return view('dashboard', $data + [
            'currentClient' => $currentClient,
            'dashboardType' => 'manager',
        ]);
    }

    /**
     * Gather all personal data for the employee self-service dashboard.
     */
    private function getEmployeeDashboardData($user, Client $currentClient): array
    {
        $clientId = $currentClient->id;

        $employee = Employee::where('client_id', $clientId)
            ->where('email', $user->email)
            ->first();

        if (! $employee) {
            return [
                'dashboardType' => 'employee',
                'employee' => null,
                'profile' => null,
                'leave_balance' => 0,
                'leave_requests' => collect(),
                'attendance_summary' => $this->emptyAttendanceSummary(),
                'month_attendance' => collect(),
                'payslips' => collect(),
                'recent_requests' => collect(),
                'personal_alerts' => [],
                'contract' => null,
                'performance_reviews' => collect(),
                'trainings' => collect(),
                'quickActions' => $this->getEmployeeQuickActions(),
            ];
        }

        $now = now();

        // Leave balance (applied annual-allowance / self-service days requested)
        $approvedLeaveDays = SelfService::where('client_id', $clientId)
            ->where('employee_id', $employee->id)
            ->where('request_type', 'leave')
            ->where('status', 'approved')
            ->sum('days_requested');

        $leaveBalance = max(0, (float) ($employee->leave_balance ?: 28) - (float) $approvedLeaveDays);

        $leaveRequests = $employee->selfServiceRequests()
            ->where('request_type', 'leave')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Attendance for the current calendar month
        $monthStart = $now->copy()->startOfMonth()->toDateString();
        $monthAttendance = Attendance::where('client_id', $clientId)
            ->where('employee_id', $employee->id)
            ->where('attendance_date', '>=', $monthStart)
            ->orderBy('attendance_date', 'desc')
            ->get();

        $attendanceSummary = [
            'present' => $monthAttendance->where('status', 'present')->count(),
            'late' => $monthAttendance->where('status', 'late')->count(),
            'absent' => $monthAttendance->where('status', 'absent')->count(),
            'half_day' => $monthAttendance->where('status', 'half_day')->count(),
            'on_leave' => $monthAttendance->where('status', 'on_leave')->count(),
            'total_hours' => (float) $monthAttendance->sum('total_hours'),
            'overtime_hours' => (float) $monthAttendance->sum('overtime_hours'),
        ];

        // Payslips / payroll history
        $payslips = Payroll::where('client_id', $clientId)
            ->where('employee_id', $employee->id)
            ->orderByDesc('payroll_period')
            ->take(6)
            ->get();

        $recentRequests = $employee->selfServiceRequests()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $activeContract = $employee->contracts()
            ->whereIn('status', ['active', 'probation'])
            ->orderByDesc('start_date')
            ->first();

        $performanceReviews = $employee->performanceReviews()
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $trainings = $employee->inductionTrainings()
            ->orderByDesc('training_date')
            ->take(3)
            ->get();

        $alerts = $this->getPersonalAlerts($employee, $currentClient);

        return [
            'dashboardType' => 'employee',
            'employee' => $employee,
            'profile' => $employee,
            'leave_balance' => $leaveBalance,
            'leave_requests' => $leaveRequests,
            'attendance_summary' => $attendanceSummary,
            'month_attendance' => $monthAttendance,
            'payslips' => $payslips,
            'recent_requests' => $recentRequests,
            'personal_alerts' => $alerts,
            'contract' => $activeContract,
            'performance_reviews' => $performanceReviews,
            'trainings' => $trainings,
            'quickActions' => $this->getEmployeeQuickActions(),
        ];
    }

    /**
     * Gather team data for the line-manager dashboard.
     */
    private function getManagerDashboardData($user, Client $currentClient): array
    {
        $clientId = $currentClient->id;

        $me = Employee::where('client_id', $clientId)
            ->where('email', $user->email)
            ->first();

        $team = collect();

        if ($me) {
            $team = Employee::where('client_id', $clientId)
                ->where('manager_id', $me->id)
                ->get();
        }

        $today = now()->toDateString();

        $teamIds = $team->pluck('id');

        $presentToday = $teamIds->isNotEmpty()
            ? Attendance::where('client_id', $clientId)->whereIn('employee_id', $teamIds)->whereDate('attendance_date', $today)->where('status', 'present')->count()
            : 0;
        $lateToday = $teamIds->isNotEmpty()
            ? Attendance::where('client_id', $clientId)->whereIn('employee_id', $teamIds)->whereDate('attendance_date', $today)->where('status', 'late')->count()
            : 0;
        $absentToday = $teamIds->isNotEmpty()
            ? Attendance::where('client_id', $clientId)->whereIn('employee_id', $teamIds)->whereDate('attendance_date', $today)->where('status', 'absent')->count()
            : 0;

        $pendingApprovals = SelfService::where('client_id', $clientId)
            ->whereIn('employee_id', $teamIds)
            ->where('status', 'pending')
            ->where('request_type', 'leave')
            ->count();

        $monthStart = now()->startOfMonth()->toDateString();
        $teamHours = $teamIds->isNotEmpty()
            ? (float) Attendance::where('client_id', $clientId)->whereIn('employee_id', $teamIds)->where('attendance_date', '>=', $monthStart)->sum('total_hours')
            : 0;
        $teamOvertime = $teamIds->isNotEmpty()
            ? (float) Attendance::where('client_id', $clientId)->whereIn('employee_id', $teamIds)->where('attendance_date', '>=', $monthStart)->sum('overtime_hours')
            : 0;

        $teamLeave = LeaveRequest::where('client_id', $clientId)
            ->whereIn('employee_id', $teamIds)
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $teamAttendanceToday = $team->map(function ($member) use ($today) {
            $record = Attendance::where('client_id', $member->client_id)
                ->where('employee_id', $member->id)
                ->whereDate('attendance_date', $today)
                ->first();

            return [
                'employee' => $member,
                'status' => $record?->status ?? 'not_marked',
                'clock_in' => $record?->clock_in,
                'hours' => $record?->total_hours,
            ];
        })->values();

        return [
            'dashboardType' => 'manager',
            'manager' => $me,
            'team' => $team,
            'team_count' => $team->count(),
            'team_active' => $team->where('status', 'active')->count(),
            'present_today' => $presentToday,
            'late_today' => $lateToday,
            'absent_today' => $absentToday,
            'team_hours' => $teamHours,
            'team_overtime' => $teamOvertime,
            'pending_approvals' => $pendingApprovals,
            'team_leave_requests' => $teamLeave,
            'team_attendance_today' => $teamAttendanceToday,
        ];
    }

    /**
     * Personal alerts relevant to a single employee.
     */
    private function getPersonalAlerts(Employee $employee, Client $currentClient): array
    {
        $alerts = [];
        $today = now();

        // Expiring active contract
        $contract = $employee->contracts()
            ->whereIn('status', ['active', 'probation'])
            ->whereNotNull('end_date')
            ->where('end_date', '>=', $today->toDateString())
            ->where('end_date', '<=', $today->copy()->addDays(30)->toDateString())
            ->first();

        if ($contract) {
            $daysLeft = (int) $today->diffInDays($contract->end_date);
            $alerts[] = [
                'title' => 'Contract Expiring Soon',
                'description' => 'Your employment contract expires in ' . $daysLeft . ' days ('
                    . $contract->end_date->format('d M Y') . ').',
                'severity' => $daysLeft <= 7 ? 'critical' : 'warning',
                'icon' => 'file-text',
                'color' => $daysLeft <= 7 ? 'red' : 'yellow',
                'link' => route('selfservice.contract'),
                'action_label' => 'View Contract',
            ];
        }

        // Pending self-service requests from the employee
        $pendingCount = $employee->selfServiceRequests()
            ->where('status', 'pending')
            ->count();

        if ($pendingCount > 0) {
            $alerts[] = [
                'title' => 'Pending Requests',
                'description' => 'You have ' . $pendingCount . ' pending request'
                    . ($pendingCount > 1 ? 's' : '') . ' waiting for review.',
                'severity' => 'warning',
                'icon' => 'clock',
                'color' => 'yellow',
                'link' => route('selfservice.index'),
                'action_label' => 'Track Requests',
            ];
        }

        // Probation ending soon
        if ($employee->isOnProbation() && $employee->probation_end_date >= $today
            && $employee->probation_end_date <= $today->copy()->addDays(30)) {
            $daysLeft = (int) $today->diffInDays($employee->probation_end_date);
            $alerts[] = [
                'title' => 'Probation Review Upcoming',
                'description' => 'Your probation period ends in ' . $daysLeft . ' day'
                    . ($daysLeft > 1 ? 's' : '') . ' (' . $employee->probation_end_date->format('d M Y') . ').',
                'severity' => 'info',
                'icon' => 'award',
                'color' => 'blue',
                'link' => route('selfservice.profile'),
                'action_label' => 'View Profile',
            ];
        }

        // Missing statutory IDs
        $missing = [];

        if (empty($employee->nssf_number)) {
            $missing[] = 'NSSF';
        }
        if (empty($employee->tin_number)) {
            $missing[] = 'TIN';
        }
        if (empty($employee->nhif_number)) {
            $missing[] = 'NHIF';
        }

        if (! empty($missing)) {
            $alerts[] = [
                'title' => 'Update Statutory Details',
                'description' => 'Your records are missing: ' . implode(', ', $missing) . '. Please provide details to HR.',
                'severity' => 'info',
                'icon' => 'shield',
                'color' => 'blue',
                'link' => route('selfservice.profile'),
                'action_label' => 'Update Profile',
            ];
        }

        // On leave today flag
        if ($employee->status === 'on_leave') {
            $alerts[] = [
                'title' => 'You are On Leave',
                'description' => 'Your employee record is currently marked as on leave.',
                'severity' => 'info',
                'icon' => 'calendar',
                'color' => 'green',
                'link' => route('selfservice.leave'),
                'action_label' => 'View Leave',
            ];
        }

        $severityRank = ['critical' => 2, 'warning' => 1, 'info' => 0];

        usort($alerts, function ($a, $b) use ($severityRank) {
            return ($severityRank[$b['severity']] ?? 0) <=> ($severityRank[$a['severity']] ?? 0);
        });

        return array_slice($alerts, 0, 5);
    }

    /**
     * Empty attendance summary structure.
     */
    private function emptyAttendanceSummary(): array
    {
        return [
            'present' => 0,
            'late' => 0,
            'absent' => 0,
            'half_day' => 0,
            'on_leave' => 0,
            'total_hours' => 0,
            'overtime_hours' => 0,
        ];
    }

    /**
     * Quick actions for the employee self-service dashboard.
     */
    private function getEmployeeQuickActions(): array
    {
        return [
            [
                'label' => 'Request Leave',
                'description' => 'Submit a new leave request.',
                'href' => route('selfservice.leave'),
                'icon' => 'calendar',
                'color' => 'indigo',
                'badge' => 'Leave',
            ],
            [
                'label' => 'View Payslips',
                'description' => 'Review your recent salary slips.',
                'href' => route('selfservice.payslip'),
                'icon' => 'credit-card',
                'color' => 'green',
                'badge' => 'Payroll',
            ],
            [
                'label' => 'Update Profile',
                'description' => 'Keep your personal details up to date.',
                'href' => route('selfservice.profile'),
                'icon' => 'user',
                'color' => 'blue',
                'badge' => 'Profile',
            ],
            [
                'label' => 'Request Contract',
                'description' => 'Request a copy of your employment contract.',
                'href' => route('selfservice.contract'),
                'icon' => 'file-text',
                'color' => 'purple',
                'badge' => 'Contract',
            ],
            [
                'label' => 'File Complaint',
                'description' => 'Submit a confidential complaint.',
                'href' => route('selfservice.complaint'),
                'icon' => 'alert-triangle',
                'color' => 'red',
                'badge' => 'Support',
            ],
            [
                'label' => 'Expense Claim',
                'description' => 'Submit an expense claim for reimbursement.',
                'href' => route('selfservice.expense'),
                'icon' => 'dollar-sign',
                'color' => 'yellow',
                'badge' => 'Finance',
            ],
        ];
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
        $today = now();
        $employees = Employee::where('client_id', $clientId)->get();
        $totalEmployees = $employees->count();

        // Employee breakdown
        $activeEmployees = $employees->where('status', 'active')->count();
        $onLeaveEmployees = $employees->where('status', 'on_leave')->count();
        $probationEmployees = $employees->filter(fn ($e) => $e->status === 'probation' || $e->isOnProbation())->count();
        $terminatedEmployees = $employees->where('status', 'terminated')->count();
        $newHires = $employees->where('hire_date', '>=', $today->copy()->subMonth())->count();

        // Attendance stats
        $todayAttendance = Attendance::where('client_id', $clientId)
            ->where('attendance_date', $today->toDateString())
            ->get();
        $presentToday = $todayAttendance->where('status', 'present')->count();
        $absentToday = $todayAttendance->where('status', 'absent')->count();
        $lateToday = $todayAttendance->where('status', 'late')->count();
        $attendanceRate = $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100, 1) : 0;

        // Monthly attendance volume
        $monthStart = $today->copy()->startOfMonth()->toDateString();
        $monthAttendance = Attendance::where('client_id', $clientId)
            ->where('attendance_date', '>=', $monthStart)
            ->get();
        $monthlyOvertimeHours = round((float) $monthAttendance->sum('overtime_hours'), 1);
        $monthlyTotalHours = round((float) $monthAttendance->sum('total_hours'), 1);

        // Payroll stats
        $payrollPeriod = $today->format('Y-m');
        $currentMonthPayroll = Payroll::where('client_id', $clientId)
            ->where('payroll_period', $payrollPeriod)
            ->sum('net_pay');
        $monthlyPayrollCount = Payroll::where('client_id', $clientId)
            ->where('payroll_period', $payrollPeriod)
            ->count();

        // Active disciplinary cases (any status other than resolved)
        $activeCases = DisciplinaryCase::where('client_id', $clientId)
            ->whereNotIn('status', ['resolved'])
            ->count();

        // Pending self-service requests
        $pendingRequests = SelfService::where('client_id', $clientId)
            ->where('status', 'pending')
            ->count();

        // Turnover: exits in the last 12 months relative to total headcount
        $yearAgo = $today->copy()->subMonths(12);
        $exitCases = ExitCase::where('client_id', $clientId)
            ->where('status', 'completed')
            ->where('exit_date', '>=', $yearAgo->toDateString())
            ->count();
        $terminatedYear = $employees->where('status', 'terminated')
            ->filter(fn ($e) => $e->termination_date && $e->termination_date >= $yearAgo)
            ->count();
        $exitsLastYear = max($exitCases, $terminatedYear);
        $turnoverRate = ($totalEmployees + $exitsLastYear) > 0
            ? round(($exitsLastYear / ($totalEmployees + $exitsLastYear)) * 100, 1)
            : 0;

        // Organization structure
        $departmentsCount = Department::where('client_id', $clientId)
            ->where('is_active', true)
            ->count();
        if ($departmentsCount === 0) {
            $departmentsCount = $employees->pluck('department')->filter()->unique()->count();
        }

        $locations = $employees->pluck('city')->filter()->unique()->values();
        $locationsCount = $locations->count();

        return [
            'employees' => $employees,
            'total_employees' => $totalEmployees,
            'active_employees' => $activeEmployees,
            'on_leave_employees' => $onLeaveEmployees,
            'probation_employees' => $probationEmployees,
            'terminated_employees' => $terminatedEmployees,
            'new_hires' => $newHires,
            'present_today' => $presentToday,
            'absent_today' => $absentToday,
            'late_today' => $lateToday,
            'attendance_rate' => $attendanceRate,
            'monthly_overtime_hours' => $monthlyOvertimeHours,
            'monthly_total_hours' => $monthlyTotalHours,
            'monthly_payroll' => $currentMonthPayroll,
            'monthly_payroll_count' => $monthlyPayrollCount,
            'monthly_payroll_formatted' => $this->formatMoney($currentMonthPayroll),
            'active_cases' => $activeCases,
            'pending_requests' => $pendingRequests,
            'turnover_rate' => $turnoverRate,
            'departments_count' => $departmentsCount,
            'locations_count' => $locationsCount,
        ];
    }

    /**
     * Chart data computed from real client records.
     */
    private function getChartData($clientId): array
    {
        // Employee distribution by employment type
        $employees = Employee::where('client_id', $clientId)->get();
        $types = [
            'full_time' => ['label' => 'Full Time', 'color' => '#6366f1'],
            'part_time' => ['label' => 'Part Time', 'color' => '#10b981'],
            'contract' => ['label' => 'Contract', 'color' => '#f59e0b'],
            'intern' => ['label' => 'Intern', 'color' => '#8b5cf6'],
        ];
        $distributionLabels = [];
        $distributionData = [];
        $distributionColors = [];

        foreach ($types as $key => $meta) {
            $count = $employees->where('employment_type', $key)->count();
            if ($count > 0) {
                $distributionLabels[] = $meta['label'];
                $distributionData[] = $count;
                $distributionColors[] = $meta['color'];
            }
        }

        // Last 6 months attendance trend
        $attendanceLabels = [];
        $attendanceRates = [];
        $attendancePresent = [];
        $attendanceAbsent = [];
        $attendanceLate = [];

        // Last 6 months payroll trend
        $payrollLabels = [];
        $payrollTotals = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonthsNoOverflow($i);
            $label = $month->format('M Y');

            $attendanceLabels[] = $label;
            $rows = Attendance::where('client_id', $clientId)
                ->whereBetween('attendance_date', [
                    $month->copy()->startOfMonth()->toDateString(),
                    $month->copy()->endOfMonth()->toDateString(),
                ])
                ->get();
            $present = $rows->where('status', 'present')->count();
            $absent = $rows->where('status', 'absent')->count();
            $late = $rows->where('status', 'late')->count();
            $tracked = $present + $absent + $late;

            $attendancePresent[] = $present;
            $attendanceAbsent[] = $absent;
            $attendanceLate[] = $late;
            $attendanceRates[] = $tracked > 0 ? round((($present + $late) / $tracked) * 100, 1) : 0;

            $payrollLabels[] = $label;
            $payrollTotals[] = (float) Payroll::where('client_id', $clientId)
                ->where('payroll_period', $month->format('Y-m'))
                ->sum('net_pay');
        }

        return [
            'distribution' => [
                'labels' => $distributionLabels,
                'data' => $distributionData,
                'colors' => $distributionColors,
            ],
            'attendance' => [
                'labels' => $attendanceLabels,
                'rates' => $attendanceRates,
                'present' => $attendancePresent,
                'absent' => $attendanceAbsent,
                'late' => $attendanceLate,
            ],
            'payroll' => [
                'labels' => $payrollLabels,
                'totals' => $payrollTotals,
            ],
        ];
    }

    /**
     * Compliance percentages derived from real records for the client.
     */
    private function getComplianceStats($clientId, $employees, $client): array
    {
        $active = $employees->where('status', 'active');
        $count = $active->count();

        $labour = $count
            ? round($active->filter(fn ($e) => $e->tin_number && $e->nhif_number)->count() / $count * 100)
            : 100;

        $nssf = $count
            ? round($active->filter(fn ($e) => $e->nssf_number)->count() / $count * 100)
            : 100;

        if (!empty($client->wcf_employer_number)) {
            $wcf = 100;
        } else {
            $wcfRecords = SocialRecord::where('client_id', $clientId)
                ->whereNotNull('wcf_number')
                ->where('wcf_number', '!=', '')
                ->count();
            $wcf = $count ? round(min($wcfRecords, $count) / $count * 100) : 100;
        }

        $data = $count
            ? round($active->filter(fn ($e) => $e->email && $e->phone)->count() / $count * 100)
            : 100;

        $avg = (int) round(($labour + $nssf + $wcf + $data) / 4);
        $status = $avg >= 90 ? 'Compliant' : ($avg >= 70 ? 'Partially Compliant' : 'Needs Attention');

        $lastAudit = \App\Models\Audit::where('module', 'like', '%ompliance%')
            ->latest('created_at')
            ->value('created_at')
            ?? $client->updated_at
            ?? $client->created_at;

        return [
            'labour' => $labour,
            'nssf' => $nssf,
            'wcf' => $wcf,
            'data' => $data,
            'average' => $avg,
            'status' => $status,
            'last_audit' => $lastAudit ? \Carbon\Carbon::parse($lastAudit)->format('d M Y') : null,
        ];
    }

    /**
     * Upcoming deadlines derived from real records for the client.
     */
    private function getUpcomingEvents($clientId, $employees): array
    {
        $today = now();

        $contractRenewals = Contract::where('client_id', $clientId)
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [
                $today->toDateString(),
                $today->copy()->addDays(30)->toDateString(),
            ])
            ->count();

        $trainings = InductionTraining::where('client_id', $clientId)
            ->where('status', 'scheduled')
            ->whereBetween('training_date', [
                $today->toDateString(),
                $today->copy()->addDays(30)->toDateString(),
            ])
            ->count();

        $activeEmployees = $employees->where('status', 'active');
        $statutoryGaps = $activeEmployees->filter(function ($e) {
            return empty($e->nssf_number) || empty($e->tin_number) || empty($e->nhif_number);
        })->count();

        $probationEnding = $activeEmployees->filter(fn ($e) => $e->isOnProbation())
            ->filter(fn ($e) => $e->probation_end_date >= $today && $e->probation_end_date <= $today->copy()->addDays(30))
            ->count();

        return [
            'contract_renewals' => $contractRenewals,
            'trainings' => $trainings,
            'statutory_gaps' => $statutoryGaps,
            'probation_ending' => $probationEnding,
        ];
    }

    private function formatMoney($amount): string
    {
        $amount = (float) $amount;

        if ($amount >= 1000000000) {
            return 'TZS ' . number_format($amount / 1000000000, 2) . 'B';
        }

        if ($amount >= 1000000) {
            return 'TZS ' . number_format($amount / 1000000, 2) . 'M';
        }

        if ($amount >= 1000) {
            return 'TZS ' . number_format($amount / 1000, 1) . 'K';
        }

        return 'TZS ' . number_format($amount, 0);
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
                'permission' => 'attendance.view',
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
                'permission' => 'attendance.view',
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
                'permission' => 'payroll.view',
            ];
        }
        
        // Get active contracts expiring soon
        $expiringContracts = Contract::where('client_id', $clientId)
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->with('employee')
            ->get();

        foreach ($expiringContracts as $contract) {
            $daysLeft = (int) $contract->end_date->diffInDays(now());
            $employeeName = trim(($contract->employee->first_name ?? '') . ' ' . ($contract->employee->last_name ?? ''));
            $alerts[] = [
                'type' => 'contract_expiry',
                'title' => 'Contract Expiring',
                'description' => ($employeeName ?: 'Employee') . ' - ' . $contract->formatted_contract_number . ' (' . $daysLeft . ' days remaining)',
                'severity' => $daysLeft <= 7 ? 'critical' : 'warning',
                'icon' => 'alert-circle',
                'color' => $daysLeft <= 7 ? 'red' : 'yellow',
                'link' => route('contracts.index'),
                'action_label' => 'Review Contract',
                'permission' => 'employment_contract.view',
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
                'permission' => 'selfservice.view',
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
                'permission' => 'employees.create',
            ],
            [
                'label' => 'Record Attendance',
                'description' => 'Capture attendance for today before payroll processing.',
                'href' => route('attendance.index', ['date' => now()->toDateString()]),
                'icon' => 'calendar',
                'color' => 'purple',
                'badge' => $stats['present_today'] . '/' . max(1, $stats['total_employees']),
                'permission' => 'attendance.manage',
            ],
            [
                'label' => 'Review Leave Requests',
                'description' => 'Check pending employee requests and approvals.',
                'href' => route('selfservice.index'),
                'icon' => 'clipboard',
                'color' => 'yellow',
                'badge' => $pendingRequests . ' pending',
                'permission' => 'selfservice.view',
            ],
            [
                'label' => 'Process Payroll',
                'description' => 'Generate, import, or update payroll for the current period.',
                'href' => route('payroll.index'),
                'icon' => 'credit-card',
                'color' => 'green',
                'badge' => now()->format('M Y'),
                'permission' => 'payroll.manage',
            ],
            [
                'label' => 'Create Case File',
                'description' => 'Open the case management workspace.',
                'href' => route('casemanagement.index'),
                'icon' => 'folder-plus',
                'color' => 'red',
                'badge' => 'Legal',
                'permission' => 'casemanagement.view',
            ],
            [
                'label' => 'Compliance Reports',
                'description' => 'Review statutory filings and compliance output.',
                'href' => route('compliance.index'),
                'icon' => 'trending-up',
                'color' => 'indigo',
                'badge' => 'Reports',
                'permission' => 'compliance.view',
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
