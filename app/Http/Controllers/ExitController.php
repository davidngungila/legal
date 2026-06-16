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
    public function index()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);
        $exitCases = ExitCase::with(['employee'])->where('client_id', $clientId)->latest()->paginate(20);
        $employees = Employee::where('client_id', $clientId)->where('status', 'active')->get();

        return view('exit.index', compact('currentClient', 'exitCases', 'employees'));
    }

    public function store(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'exit_type' => 'required|in:resignation,misconduct_termination,retrenchment,mutual_separation,retirement,death_in_service',
            'exit_date' => 'nullable|date',
            'reason' => 'nullable|string',
        ]);

        $exitCase = ExitCase::create([
            'client_id' => $clientId,
            'exit_number' => 'EXIT-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'employee_id' => $validated['employee_id'],
            'exit_type' => $validated['exit_type'],
            'exit_date' => $validated['exit_date'],
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

        return back()->with('success', 'Exit case initiated successfully!');
    }
}
