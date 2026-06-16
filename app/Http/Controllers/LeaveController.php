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

        $leaveTypes = LeaveType::where('client_id', $clientId)->where('is_active', true)->get();
        $leaveRequests = LeaveRequest::with(['employee', 'leaveType'])->where('client_id', $clientId)->latest()->paginate(20);
        $employees = Employee::where('client_id', $clientId)->get();

        return view('leave.index', compact('currentClient', 'leaveTypes', 'leaveRequests', 'employees'));
    }

    public function store(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $leaveType = LeaveType::findOrFail($validated['leave_type_id']);
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $days = $startDate->diffInDaysFiltered(function (Carbon $date) {
            return !$date->isWeekend();
        }, $endDate) + 1;

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
        ]);

        return back()->with('success', 'Leave request submitted successfully!');
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
}
