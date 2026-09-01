<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveType;
use App\Models\LeaveEntitlement;
use App\Models\LeaveRequest;
use App\Models\Employee;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);
        if (!$currentClient) {
            return redirect()->route('dashboard')->with('error', 'Selected client not found.');
        }

        // Get leave types - try with is_active filter first, fallback to all
        $leaveTypes = LeaveType::where('client_id', $clientId)->where('is_active', true)->get();
        if ($leaveTypes->isEmpty()) {
            // If no active leave types, try to get all leave types for this client
            $leaveTypes = LeaveType::where('client_id', $clientId)->get();
        }
        
        // If still no leave types, create default ones for this client
        if ($leaveTypes->isEmpty()) {
            $defaultLeaveTypes = [
                ['type_name' => 'Annual Leave', 'entitlement_days' => 21, 'accrual_rate' => 1.75, 'eligibility_months' => 3, 'cycle_months' => 12, 'is_paid' => true, 'pay_rate' => 100, 'is_active' => true],
                ['type_name' => 'Sick Leave', 'entitlement_days' => 10, 'accrual_rate' => 0.83, 'eligibility_months' => 1, 'cycle_months' => 12, 'is_paid' => true, 'pay_rate' => 100, 'is_active' => true],
                ['type_name' => 'Emergency Leave', 'entitlement_days' => 3, 'accrual_rate' => 0, 'eligibility_months' => 0, 'cycle_months' => 12, 'is_paid' => true, 'pay_rate' => 100, 'is_active' => true],
                ['type_name' => 'Maternity Leave', 'entitlement_days' => 84, 'accrual_rate' => 0, 'eligibility_months' => 6, 'cycle_months' => 12, 'is_paid' => true, 'pay_rate' => 100, 'is_active' => true],
                ['type_name' => 'Paternity Leave', 'entitlement_days' => 7, 'accrual_rate' => 0, 'eligibility_months' => 6, 'cycle_months' => 12, 'is_paid' => true, 'pay_rate' => 100, 'is_active' => true],
                ['type_name' => 'Compassionate Leave', 'entitlement_days' => 7, 'accrual_rate' => 0, 'eligibility_months' => 0, 'cycle_months' => 12, 'is_paid' => true, 'pay_rate' => 100, 'is_active' => true],
            ];
            
            foreach ($defaultLeaveTypes as $typeData) {
                LeaveType::create(array_merge($typeData, ['client_id' => $clientId]));
            }
            
            $leaveTypes = LeaveType::where('client_id', $clientId)->get();
        }
        
        $leaveRequests = LeaveRequest::with(['employee', 'leaveType'])->where('client_id', $clientId)->latest()->paginate(20);
        $employees = Employee::where('client_id', $clientId)->where('status', 'active')->get();

        $user = Auth::user();
        $isEmployee = !$user->hasRole('super_admin') && !$user->hasRole('admin') && !$user->hasRole('lead_hr_admin') && !$user->hasRole('hr_officer') && !$user->hasRole('line_manager');
        $currentEmployee = null;
        if ($isEmployee && $user->employee_id) {
            $currentEmployee = Employee::where('client_id', $clientId)->where('id', $user->employee_id)->first();
        }

        return view('leave.index', compact('currentClient', 'leaveTypes', 'leaveRequests', 'employees', 'isEmployee', 'currentEmployee'));
    }

    public function store(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        try {
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'leave_type_id' => 'required|exists:leave_types,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'reason' => 'nullable|string',
            ]);

            $user = Auth::user();
            $isEmployee = !$user->hasRole('super_admin') && !$user->hasRole('admin') && !$user->hasRole('lead_hr_admin') && !$user->hasRole('hr_officer') && !$user->hasRole('line_manager');
            if ($isEmployee) {
                $currentEmployee = Employee::where('client_id', $clientId)->where('id', $user->employee_id)->first();
                if ($currentEmployee && $validated['employee_id'] != $currentEmployee->id) {
                    return back()->with('error', 'You can only submit leave requests for yourself.');
                }
                if (!$currentEmployee) {
                    return back()->with('error', 'No employee profile linked to your account.');
                }
                $validated['employee_id'] = $currentEmployee->id;
            }

            $leaveType = LeaveType::findOrFail($validated['leave_type_id']);
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);
            
            // Calculate business days (excluding weekends)
            $days = $startDate->diffInDaysFiltered(function (Carbon $date) {
                return !$date->isWeekend();
            }, $endDate) + 1;

            // Check if employee has sufficient leave balance
            $employee = Employee::findOrFail($validated['employee_id']);
            $leaveEntitlement = \App\Models\LeaveEntitlement::where('employee_id', $validated['employee_id'])
                ->where('leave_type_id', $validated['leave_type_id'])
                ->where('client_id', $clientId)
                ->first();

            if ($leaveEntitlement) {
                $availableDays = $leaveEntitlement->balance_days ?? 0;
                if ($days > $availableDays) {
                    return back()->with('error', "Insufficient leave balance. Available: {$availableDays} days, Requested: {$days} days");
                }
            }

            LeaveRequest::create([
                'client_id' => $clientId,
                'employee_id' => $validated['employee_id'],
                'leave_type_id' => $validated['leave_type_id'],
                'leave_type' => $leaveType->type_name,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'days' => $days,
                'reason' => $validated['reason'],
                'status' => 'pending',
                'workflow_status' => 'pending',
                'applied_at' => now(),
                'applied_by' => auth()->id(),
            ]);

            // Log the leave request creation
            \App\Helpers\AuditLogger::log(
                'created',
                null,
                'Leave Management',
                "Leave request submitted for {$employee->first_name} {$employee->last_name}: {$leaveType->type_name} ({$days} days)"
            );

            return back()->with('success', 'Leave request submitted successfully!');
        } catch (\Exception $e) {
            \Log::error('Leave request submission error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to submit leave request: ' . $e->getMessage())->withInput();
        }
    }

    public function updateStatus(Request $request, LeaveRequest $leaveRequest)
    {
        $clientId = session('current_client_id');
        if (!$clientId || $leaveRequest->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,partial',
            'days_approved' => 'nullable|numeric|min:0',
            'comments' => 'nullable|string',
        ]);

        $leaveRequest->update([
            'status' => $validated['status'],
            'workflow_status' => $validated['status'],
            'days_approved' => $validated['days_approved'] ?? $leaveRequest->days,
        ]);

        return back()->with('success', 'Leave request updated!');
    }

    public function show($id)
    {
        $clientId = session('current_client_id');
        $leaveRequest = LeaveRequest::with(['employee', 'leaveType', 'approver'])->find($id);
        
        if (!$clientId || !$leaveRequest || $leaveRequest->client_id != $clientId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'request' => $leaveRequest
        ]);
    }

    public function approve(Request $request, $id)
    {
        $clientId = session('current_client_id');
        $leaveRequest = LeaveRequest::find($id);
        
        if (!$clientId || !$leaveRequest || $leaveRequest->client_id != $clientId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request.'
            ], 403);
        }

        if ($leaveRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending requests can be approved.'
            ], 400);
        }

        $leaveRequest->update([
            'status' => 'approved',
            'workflow_status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        \App\Helpers\AuditLogger::log(
            'approved',
            $leaveRequest,
            'Leave Management',
            "Approved leave request for {$leaveRequest->employee->first_name} {$leaveRequest->employee->last_name}: {$leaveRequest->leave_type}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Leave request approved successfully!'
        ]);
    }

    public function reject(Request $request, $id)
    {
        $clientId = session('current_client_id');
        $leaveRequest = LeaveRequest::find($id);
        
        if (!$clientId || !$leaveRequest || $leaveRequest->client_id != $clientId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request.'
            ], 403);
        }

        if ($leaveRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending requests can be rejected.'
            ], 400);
        }

        $validated = $request->validate([
            'reason' => 'required|string'
        ]);

        $leaveRequest->update([
            'status' => 'rejected',
            'workflow_status' => 'rejected',
            'rejection_reason' => $validated['reason'],
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        \App\Helpers\AuditLogger::log(
            'rejected',
            $leaveRequest,
            'Leave Management',
            "Rejected leave request for {$leaveRequest->employee->first_name} {$leaveRequest->employee->last_name}: {$leaveRequest->leave_type} - Reason: {$validated['reason']}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Leave request rejected successfully!'
        ]);
    }

    public function destroy($id)
    {
        $clientId = session('current_client_id');
        $leaveRequest = LeaveRequest::find($id);
        
        if (!$clientId || !$leaveRequest || $leaveRequest->client_id != $clientId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request.'
            ], 403);
        }

        $leaveRequest->delete();

        \App\Helpers\AuditLogger::log(
            'deleted',
            $leaveRequest,
            'Leave Management',
            "Deleted leave request for {$leaveRequest->employee->first_name} {$leaveRequest->employee->last_name}: {$leaveRequest->leave_type}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Leave request deleted successfully!'
        ]);
    }

    public function balances(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);
        
        $employees = Employee::where('client_id', $clientId)->where('status', 'active')->get();
        $selectedEmployee = $request->get('employee_id', null);
        
        if ($selectedEmployee) {
            $employee = Employee::where('client_id', $clientId)->findOrFail($selectedEmployee);
            $leaveEntitlements = LeaveEntitlement::with('leaveType')->where('employee_id', $selectedEmployee)->where('client_id', $clientId)->get();
        } else {
            $employee = null;
            $leaveEntitlements = [];
        }
        
        $leaveTypes = LeaveType::where('client_id', $clientId)->where('is_active', true)->get();
        
        return view('leave.balances', compact('currentClient', 'employees', 'selectedEmployee', 'employee', 'leaveEntitlements', 'leaveTypes'));
    }

    public function calendar(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);

        $query = LeaveRequest::with(['employee', 'leaveType'])
            ->where('client_id', $clientId);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->input('leave_type_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $leaveRequests = $query->orderBy('start_date')->get();

        $employees = Employee::where('client_id', $clientId)->where('status', 'active')->orderBy('first_name')->get();
        $leaveTypes = LeaveType::where('client_id', $clientId)->orderBy('type_name')->get();

        $events = $leaveRequests->map(function ($leaveRequest) {
            return [
                'id' => $leaveRequest->id,
                'employee_name' => trim(($leaveRequest->employee->first_name ?? '') . ' ' . ($leaveRequest->employee->last_name ?? '')),
                'employee_id' => $leaveRequest->employee->employee_id ?? '',
                'employee_code' => $leaveRequest->employee_id,
                'department' => $leaveRequest->employee->department ?? '',
                'leave_type' => $leaveRequest->leave_type ?? ($leaveRequest->leaveType->type_name ?? 'Leave'),
                'leave_type_id' => $leaveRequest->leave_type_id,
                'start' => $leaveRequest->start_date->format('Y-m-d'),
                'end' => $leaveRequest->end_date->format('Y-m-d'),
                'days' => $leaveRequest->days,
                'status' => $leaveRequest->status,
                'reason' => $leaveRequest->reason,
                'applied_at' => $leaveRequest->applied_at ? Carbon::parse($leaveRequest->applied_at)->format('Y-m-d') : null,
            ];
        })->values();

        return view('leave.calendar', compact('currentClient', 'employees', 'leaveTypes', 'events', 'leaveRequests'));
    }

    public function reports(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);

        $from = $request->filled('from') ? Carbon::parse($request->input('from')) : Carbon::now()->startOfYear();
        $to = $request->filled('to') ? Carbon::parse($request->input('to'))->endOfDay() : Carbon::now()->endOfYear();

        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        $query = LeaveRequest::with(['employee', 'leaveType'])
            ->where('client_id', $clientId)
            ->whereBetween('start_date', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->input('leave_type_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $requests = $query->orderBy('start_date')->get();

        $summary = [
            'total' => $requests->count(),
            'approved' => $requests->where('status', 'approved')->count(),
            'pending' => $requests->where('status', 'pending')->count(),
            'rejected' => $requests->where('status', 'rejected')->count(),
            'total_days' => (float) $requests->sum('days'),
            'approved_days' => (float) $requests->where('status', 'approved')->sum('days'),
        ];

        $byType = $requests
            ->groupBy(fn ($r) => $r->leave_type ?: ($r->leaveType->type_name ?? 'Unknown'))
            ->map(fn ($group) => [
                'count' => $group->count(),
                'days' => (float) $group->sum('days'),
                'approved_days' => (float) $group->where('status', 'approved')->sum('days'),
            ])
            ->sortByDesc('count');

        $monthly = [];
        $cursor = Carbon::parse($from)->startOfMonth();
        $endMonth = Carbon::parse($to)->startOfMonth();

        while ($cursor->lte($endMonth)) {
            $label = $cursor->format('M Y');
            $monthly[$label] = [
                'total' => 0,
                'approved' => 0,
                'pending' => 0,
                'rejected' => 0,
                'days' => 0,
            ];
            $cursor->addMonth();
        }

        foreach ($requests as $r) {
            $label = $r->start_date->format('M Y');
            if (isset($monthly[$label])) {
                $monthly[$label]['total']++;
                $monthly[$label]['days'] += (float) $r->days;
                if (isset($monthly[$label][$r->status])) {
                    $monthly[$label][$r->status]++;
                }
            }
        }

        $byDepartment = $requests
            ->groupBy(fn ($r) => $r->employee->department ?? 'Unassigned')
            ->map(fn ($group) => [
                'count' => $group->count(),
                'days' => (float) $group->sum('days'),
                'approved_days' => (float) $group->where('status', 'approved')->sum('days'),
            ])
            ->sortByDesc('count');

        $employeeSummary = $requests
            ->groupBy('employee_id')
            ->map(function ($group) {
                $employee = $group->first()->employee;

                $byType = $group
                    ->groupBy(fn ($r) => $r->leave_type ?: ($r->leaveType->type_name ?? 'Unknown'))
                    ->map(fn ($g) => [
                        'count' => $g->count(),
                        'days' => (float) $g->sum('days'),
                        'approved_days' => (float) $g->where('status', 'approved')->sum('days'),
                    ]);

                return [
                    'employee_code' => $employee->employee_id ?? '',
                    'name' => trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')),
                    'department' => $employee->department ?? '',
                    'total_requests' => $group->count(),
                    'total_days' => (float) $group->sum('days'),
                    'approved_requests' => $group->where('status', 'approved')->count(),
                    'approved_days' => (float) $group->where('status', 'approved')->sum('days'),
                    'pending_requests' => $group->where('status', 'pending')->count(),
                    'by_type' => $byType,
                ];
            })
            ->sortByDesc('total_days');

        $requestsData = $requests->map(function ($r) {
            return [
                'employee' => trim(($r->employee->first_name ?? '') . ' ' . ($r->employee->last_name ?? '')),
                'leave_type' => $r->leave_type,
                'start' => $r->start_date->format('Y-m-d'),
                'end' => $r->end_date->format('Y-m-d'),
                'days' => $r->days,
                'status' => $r->status,
                'reason' => $r->reason,
            ];
        })->values();

        $employees = Employee::where('client_id', $clientId)->where('status', 'active')->orderBy('first_name')->get();
        $leaveTypes = LeaveType::where('client_id', $clientId)->orderBy('type_name')->get();

        return view('leave.reports', compact(
            'currentClient',
            'employees',
            'leaveTypes',
            'requests',
            'requestsData',
            'from',
            'to',
            'summary',
            'byType',
            'monthly',
            'byDepartment',
            'employeeSummary'
        ));
    }
}
