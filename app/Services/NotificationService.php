<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Contract;
use App\Models\DisciplinaryCase;
use App\Models\Employee;
use App\Models\ExitCase;
use App\Models\Payroll;
use App\Models\SelfService;

class NotificationService
{
    /**
     * Compute reliable, client-scoped notifications for the current client.
     */
    public function forCurrentClient(): array
    {
        $clientId = (int) session('current_client_id');

        if (!$clientId) {
            return ['count' => 0, 'items' => []];
        }

        $items = [];
        $today = now()->toDateString();

        $activeEmployees = Employee::where('client_id', $clientId)
            ->where('status', 'active')
            ->count();

        // Attendance status for today
        $markedToday = Attendance::where('client_id', $clientId)
            ->whereDate('attendance_date', $today)
            ->distinct('employee_id')
            ->count('employee_id');

        if ($activeEmployees > 0 && $markedToday === 0) {
            $items[] = $this->item('critical', 'clock', 'red', 'Attendance Not Submitted', 'No attendance has been recorded for today.', route('attendance.index'), now());
        } elseif ($activeEmployees > 0 && $markedToday < $activeEmployees) {
            $items[] = $this->item('warning', 'calendar', 'yellow', 'Attendance Incomplete', "{$markedToday} of {$activeEmployees} active employees have been marked today.", route('attendance.index'), now());
        }

        // Payroll for the current period
        $currentPayrollCount = Payroll::where('client_id', $clientId)
            ->where('payroll_period', now()->format('Y-m'))
            ->count();

        if ($activeEmployees > 0 && $currentPayrollCount === 0) {
            $items[] = $this->item('critical', 'credit-card', 'red', 'Payroll Not Processed', 'No payroll has been processed for ' . now()->format('F Y') . '.', route('payroll.index'), now());
        }

        // Active contracts expiring within 30 days
        $expiringContracts = Contract::where('client_id', $clientId)
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [$today, now()->addDays(30)->toDateString()])
            ->with('employee')
            ->get();

        foreach ($expiringContracts as $contract) {
            $daysLeft = (int) $contract->end_date->diffInDays(now());
            $employeeName = trim(($contract->employee->first_name ?? '') . ' ' . ($contract->employee->last_name ?? ''));

            $items[] = $this->item(
                $daysLeft <= 7 ? 'critical' : 'warning',
                'alert-circle',
                $daysLeft <= 7 ? 'red' : 'yellow',
                'Contract Expiring',
                ($employeeName ?: 'Employee') . ' - ' . $contract->formatted_contract_number . ' (' . $daysLeft . ' days remaining)',
                route('contracts.index'),
                $contract->end_date
            );
        }

        // Open disciplinary cases
        $activeCases = DisciplinaryCase::where('client_id', $clientId)
            ->where('status', '!=', 'resolved')
            ->get();

        foreach ($activeCases as $case) {
            $items[] = $this->item(
                'critical',
                'alert-triangle',
                'red',
                'Disciplinary Case Requires Attention',
                'Case #' . str_pad($case->id, 3, '0', STR_PAD_LEFT) . ' (' . ucfirst(str_replace('_', ' ', $case->status)) . ')',
                route('discipline.index'),
                $case->updated_at ?? $case->created_at
            );
        }

        // Pending self-service requests awaiting approval
        $pendingRequests = SelfService::where('client_id', $clientId)
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subDays(3))
            ->get();

        foreach ($pendingRequests as $request) {
            $items[] = $this->item(
                'warning',
                'file-text',
                'yellow',
                'Pending HR Approval',
                ucfirst($request->request_type) . ' request #' . str_pad($request->id, 3, '0', STR_PAD_LEFT),
                route('selfservice.index'),
                $request->created_at
            );
        }

        // Exit cases awaiting clearance
        $pendingExits = ExitCase::where('client_id', $clientId)
            ->whereIn('status', ['initiated', 'pending_clearance'])
            ->get();

        foreach ($pendingExits as $exit) {
            $items[] = $this->item(
                'warning',
                'log-out',
                'blue',
                'Exit Pending Clearance',
                'Exit case #' . str_pad($exit->id, 3, '0', STR_PAD_LEFT) . ' (' . ucfirst(str_replace('_', ' ', $exit->status)) . ')',
                route('exit.index'),
                $exit->updated_at ?? $exit->created_at
            );
        }

        // Probation reviews due within 30 days
        $probationEnding = Employee::where('client_id', $clientId)
            ->where('status', 'active')
            ->whereNotNull('probation_end_date')
            ->whereBetween('probation_end_date', [$today, now()->addDays(30)->toDateString()])
            ->get();

        foreach ($probationEnding as $employee) {
            $daysLeft = (int) $employee->probation_end_date->diffInDays(now());

            $items[] = $this->item(
                'info',
                'user-check',
                'blue',
                'Probation Review Due',
                $employee->full_name . '\'s probation ends in ' . $daysLeft . ' day(s).',
                route('employees.show', $employee->id),
                $employee->probation_end_date
            );
        }

        // Statutory compliance deadlines (NSSF, TRA, WCF)
        $upcomingDeadlines = \DB::table('statutory_compliance_deadlines')
            ->where('client_id', $clientId)
            ->where('status', 'pending')
            ->whereBetween('due_date', [$today, now()->addDays(7)->toDateString()])
            ->get();

        foreach ($upcomingDeadlines as $deadline) {
            $daysLeft = (int) \Carbon\Carbon::parse($deadline->due_date)->diffInDays(now());
            $items[] = $this->item(
                $daysLeft <= 2 ? 'critical' : 'warning',
                'file-check',
                $daysLeft <= 2 ? 'red' : 'yellow',
                'Statutory Filing Due',
                "{$deadline->authority} {$deadline->filing_type} due in {$daysLeft} day(s)",
                route('compliance.index'),
                $deadline->due_date
            );
        }

        // Overdue statutory filings
        $overdueDeadlines = \DB::table('statutory_compliance_deadlines')
            ->where('client_id', $clientId)
            ->where('status', 'pending')
            ->where('due_date', '<', $today)
            ->get();

        foreach ($overdueDeadlines as $deadline) {
            $daysOverdue = (int) now()->diffInDays(\Carbon\Carbon::parse($deadline->due_date));
            $items[] = $this->item(
                'critical',
                'alert-circle',
                'red',
                'Statutory Filing Overdue',
                "{$deadline->authority} {$deadline->filing_type} overdue by {$daysOverdue} day(s)",
                route('compliance.index'),
                $deadline->due_date
            );
        }

        // Performance reviews overdue
        $overdueReviews = \App\Models\PerformanceReview::where('client_id', $clientId)
            ->where('status', 'pending')
            ->where('review_date', '<', $today)
            ->with('employee')
            ->get();

        foreach ($overdueReviews as $review) {
            $daysOverdue = (int) now()->diffInDays(\Carbon\Carbon::parse($review->review_date));
            $employeeName = $review->employee ? trim(($review->employee->first_name ?? '') . ' ' . ($review->employee->last_name ?? '')) : 'Employee';
            $items[] = $this->item(
                'warning',
                'clipboard',
                'yellow',
                'Performance Review Overdue',
                "{$employeeName}'s review overdue by {$daysOverdue} day(s)",
                route('performance.index'),
                $review->review_date
            );
        }

        // Leave requests pending approval
        $pendingLeave = \App\Models\LeaveRequest::where('client_id', $clientId)
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subDays(2))
            ->with('employee')
            ->get();

        foreach ($pendingLeave as $leave) {
            $employeeName = $leave->employee ? trim(($leave->employee->first_name ?? '') . ' ' . ($leave->employee->last_name ?? '')) : 'Employee';
            $items[] = $this->item(
                'warning',
                'calendar',
                'yellow',
                'Leave Request Pending',
                "{$employeeName}'s leave request pending approval",
                route('leave.index'),
                $leave->created_at
            );
        }

        // Attendance violations
        $openViolations = \App\Models\AttendanceViolation::where('client_id', $clientId)
            ->where('status', 'open')
            ->where('action_triggered', false)
            ->with('employee')
            ->get();

        foreach ($openViolations as $violation) {
            $employeeName = $violation->employee ? trim(($violation->employee->first_name ?? '') . ' ' . ($violation->employee->last_name ?? '')) : 'Employee';
            $items[] = $this->item(
                'warning',
                'alert-triangle',
                'yellow',
                'Attendance Violation',
                "{$employeeName}: {$violation->violation_type}",
                route('attendance.index'),
                $violation->violation_date
            );
        }

        // Active PIPs
        $activePips = \App\Models\PerformanceImprovementPlan::where('client_id', $clientId)
            ->where('status', 'active')
            ->with('employee')
            ->get();

        foreach ($activePips as $pip) {
            $employeeName = $pip->employee ? trim(($pip->employee->first_name ?? '') . ' ' . ($pip->employee->last_name ?? '')) : 'Employee';
            $daysRemaining = (int) \Carbon\Carbon::parse($pip->end_date)->diffInDays(now());
            $items[] = $this->item(
                'info',
                'trending-up',
                'blue',
                'Active PIP',
                "{$employeeName}'s PIP ends in {$daysRemaining} day(s)",
                route('performance.index'),
                $pip->end_date
            );
        }

        usort($items, function ($a, $b) {
            $severityRank = ['critical' => 3, 'warning' => 2, 'info' => 1];
            return ($severityRank[$b['severity']] ?? 0) <=> ($severityRank[$a['severity']] ?? 0);
        });

        return ['count' => count($items), 'items' => $items];
    }

    private function item(string $severity, string $icon, string $color, string $title, string $message, string $link, $time): array
    {
        return [
            'severity' => $severity,
            'icon' => $icon,
            'color' => $color,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'time' => $time instanceof \DateTimeInterface ? $time->diffForHumans() : \Carbon\Carbon::parse($time)->diffForHumans(),
        ];
    }
}
