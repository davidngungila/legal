<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExitCase;
use App\Models\ExitChecklist;
use App\Models\ExitSettlement;
use App\Models\Employee;
use App\Models\Client;
use Carbon\Carbon;

class ExitController extends Controller
{
    protected $exitTypes = [
        'resignation' => 'Resignation',
        'misconduct_termination' => 'Termination (Misconduct)',
        'retrenchment' => 'Retrenchment',
        'mutual_separation' => 'Mutual Separation',
        'retirement' => 'Retirement',
        'death_in_service' => 'Death in Service',
        'capacity' => 'Capacity',
    ];

    protected $statuses = [
        'initiated' => 'Initiated',
        'pending_clearance' => 'Pending Clearance',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public function index()
    {
        $this->authorize('discipline.view');
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);
        $exitCases = ExitCase::with(['employee', 'checklists', 'settlement'])
            ->where('client_id', $clientId)
            ->latest()
            ->paginate(20);
        $employees = Employee::where('client_id', $clientId)
            ->where('status', 'active')
            ->whereNotIn('id', ExitCase::where('client_id', $clientId)->where('status', '!=', 'cancelled')->pluck('employee_id'))
            ->get();

        $stats = [
            'total' => $exitCases->total(),
            'initiated' => $exitCases->where('status', 'initiated')->count(),
            'pending_clearance' => $exitCases->where('status', 'pending_clearance')->count(),
            'completed' => $exitCases->where('status', 'completed')->count(),
        ];

        return view('exit.index', compact('currentClient', 'exitCases', 'employees', 'stats'));
    }

    public function store(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'exit_type' => 'required|in:' . implode(',', array_keys($this->exitTypes)),
            'exit_date' => 'nullable|date',
            'notice_date' => 'nullable|date',
            'reason' => 'nullable|string',
        ]);

        $existing = ExitCase::where('client_id', $clientId)
            ->where('employee_id', $validated['employee_id'])
            ->where('status', '!=', 'cancelled')
            ->first();
        if ($existing) {
            return back()->with('error', 'An exit process already exists for this employee.');
        }

        $exitNumber = 'EXIT-' . date('Y') . '-' . str_pad(ExitCase::where('client_id', $clientId)->count() + 1, 4, '0', STR_PAD_LEFT);

        $exitCase = ExitCase::create([
            'client_id' => $clientId,
            'exit_number' => $exitNumber,
            'employee_id' => $validated['employee_id'],
            'exit_type' => $validated['exit_type'],
            'exit_date' => $validated['exit_date'],
            'notice_date' => $validated['notice_date'],
            'reason' => $validated['reason'],
            'status' => 'initiated',
            'initiated_by' => auth()->id(),
        ]);

        // Create default checklist items
        $checklistItems = [
            'Return company laptop',
            'Return access card/keys',
            'Complete handover notes',
            'Revoke system access',
            'Final payslip generation',
            'Certificate of service',
        ];

        foreach ($checklistItems as $item) {
            ExitChecklist::create([
                'exit_case_id' => $exitCase->id,
                'item_name' => $item,
                'category' => 'General',
                'completed' => false,
            ]);
        }

        return redirect()->route('exit.index')->with('success', 'Exit case ' . $exitNumber . ' initiated successfully!');
    }

    public function update(Request $request, $id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $exitCase = ExitCase::where('client_id', $clientId)->findOrFail($id);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'exit_type' => 'required|in:' . implode(',', array_keys($this->exitTypes)),
            'exit_date' => 'nullable|date',
            'notice_date' => 'nullable|date',
            'reason' => 'nullable|string',
            'status' => 'required|in:' . implode(',', array_keys($this->statuses)),
        ]);

        $exitCase->update($validated);

        return redirect()->route('exit.index')->with('success', 'Exit case ' . $exitCase->exit_number . ' updated successfully!');
    }

    public function destroy($id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $exitCase = ExitCase::where('client_id', $clientId)->findOrFail($id);
        $exitCase->delete();

        return redirect()->route('exit.index')->with('success', 'Exit case deleted successfully!');
    }

    public function updateStatus(Request $request, $id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $exitCase = ExitCase::where('client_id', $clientId)->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys($this->statuses)),
        ]);

        if ($validated['status'] === 'completed' && $exitCase->status !== 'completed') {
            $employee = $exitCase->employee;
            if ($employee) {
                $employee->update(['status' => 'inactive']);
            }
        }

        $exitCase->update(['status' => $validated['status']]);

        return redirect()->route('exit.index')->with('success', 'Exit case status updated to ' . $this->statuses[$validated['status']] . '.');
    }

    public function storeChecklist(Request $request, $id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $exitCase = ExitCase::where('client_id', $clientId)->findOrFail($id);

        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
        ]);

        ExitChecklist::create([
            'exit_case_id' => $exitCase->id,
            'item_name' => $validated['item_name'],
            'category' => $validated['category'] ?: 'General',
            'completed' => false,
        ]);

        return redirect()->route('exit.index')->with('success', 'Checklist item added.');
    }

    public function toggleChecklist($id, $checklistId)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $exitCase = ExitCase::where('client_id', $clientId)->findOrFail($id);
        $item = $exitCase->checklists()->findOrFail($checklistId);

        $item->completed = !$item->completed;
        if ($item->completed) {
            $item->completed_by = auth()->id();
            $item->completed_at = now();
        } else {
            $item->completed_by = null;
            $item->completed_at = null;
        }
        $item->save();

        // Auto-advance to pending clearance when all items complete
        if ($exitCase->status === 'initiated' && $exitCase->checklists()->where('completed', false)->count() === 0) {
            $exitCase->update(['status' => 'pending_clearance']);
        }

        return redirect()->route('exit.index')->with('success', 'Checklist item ' . ($item->completed ? 'completed' : 'reopened') . '.');
    }

    public function destroyChecklist($id, $checklistId)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $exitCase = ExitCase::where('client_id', $clientId)->findOrFail($id);
        $exitCase->checklists()->findOrFail($checklistId)->delete();

        return redirect()->route('exit.index')->with('success', 'Checklist item removed.');
    }

    public function storeSettlement(Request $request, $id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $exitCase = ExitCase::where('client_id', $clientId)->findOrFail($id);

        $validated = $request->validate([
            'final_salary' => 'nullable|numeric|min:0',
            'leave_pay' => 'nullable|numeric|min:0',
            'notice_pay' => 'nullable|numeric|min:0',
            'bonus_pay' => 'nullable|numeric|min:0',
            'other_payments' => 'nullable|numeric|min:0',
        ]);

        $total = (float) ($validated['final_salary'] ?? 0)
            + (float) ($validated['leave_pay'] ?? 0)
            + (float) ($validated['notice_pay'] ?? 0)
            + (float) ($validated['bonus_pay'] ?? 0)
            + (float) ($validated['other_payments'] ?? 0);

        if ($exitCase->settlement) {
            $exitCase->settlement()->update(array_merge($validated, [
                'total_settlement' => $total,
            ]));
        } else {
            ExitSettlement::create(array_merge($validated, [
                'exit_case_id' => $exitCase->id,
                'total_settlement' => $total,
                'status' => 'pending',
            ]));
        }

        return redirect()->route('exit.index')->with('success', 'Exit settlement saved successfully.');
    }

    public function markSettlementPaid($id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $exitCase = ExitCase::where('client_id', $clientId)->findOrFail($id);
        $exitCase->settlement()->update(['status' => 'paid']);

        return redirect()->route('exit.index')->with('success', 'Settlement marked as paid.');
    }
}
