<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Attendance;
use App\Models\SelfService;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $currentClient = view()->shared('currentClient');
        
        if (!$currentClient) {
            return redirect()->route('clients.index')
                ->with('error', 'Please select a client first.');
        }

        // Get statistics for current client
        $stats = $this->getClientStats($currentClient->id);
        
        // Get recent activities for current client
        $recentActivities = $this->getRecentActivities($currentClient->id);
        
        // Get alerts for current client
        $alerts = $this->getAlerts($currentClient->id);
        
        // Get client information (use currentClient directly)
        $client = $currentClient;

        return view('dashboard', compact('stats', 'recentActivities', 'alerts', 'currentClient'));
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
                'description' => $request->title,
                'time' => $request->created_at->diffForHumans(),
                'status' => $request->status,
                'icon' => $this->getRequestIcon($request->request_type),
                'color' => $this->getRequestColor($request->status),
            ];
        }

        // Get recent employee hires
        $recentHires = Employee::where('client_id', $clientId)
            ->orderBy('hire_date', 'desc')
            ->take(3)
            ->get();

        foreach ($recentHires as $employee) {
            $activities[] = [
                'type' => 'hire',
                'title' => 'New Employee Onboarded',
                'description' => $employee->first_name . ' ' . $employee->last_name,
                'time' => $employee->hire_date->diffForHumans(),
                'status' => 'completed',
                'icon' => 'user-plus',
                'color' => 'green',
            ];
        }

        // Sort by time
        usort($activities, function ($a, $b) {
            return $b['time'] <=> $a['time'];
        });

        return array_slice($activities, 0, 8);
    }

    /**
     * Get alerts for the current client.
     */
    private function getAlerts($clientId)
    {
        $alerts = [];
        
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
            ];
        }

        return array_slice($alerts, 0, 5);
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
