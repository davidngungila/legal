<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DisciplinaryCase;
use App\Models\DisciplinaryWarning;
use App\Models\Employee;
use App\Models\Client;
use Carbon\Carbon;

class DisciplinaryController extends Controller
{
    public function index()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);
        $cases = DisciplinaryCase::with(['employee', 'outcome'])->where('client_id', $clientId)->latest()->paginate(20);
        $employees = Employee::where('client_id', $clientId)->get();
        $warnings = DisciplinaryWarning::with(['employee'])->where('client_id', $clientId)->where('is_active', true)->latest()->get();

        return view('discipline.index', compact('currentClient', 'cases', 'employees', 'warnings'));
    }

    public function store(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'case_type' => 'required|in:minor,major',
            'incident_date' => 'required|date',
            'incident_description' => 'required|string',
        ]);

        DisciplinaryCase::create([
            'client_id' => $clientId,
            'case_number' => 'DISC-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'employee_id' => $validated['employee_id'],
            'case_type' => $validated['case_type'],
            'incident_date' => $validated['incident_date'],
            'incident_description' => $validated['incident_description'],
            'reported_by' => auth()->id(),
            'status' => 'reported',
        ]);

        return back()->with('success', 'Disciplinary case opened!');
    }

    public function investigations()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);
        return view('discipline.investigations', ['currentClient' => $currentClient]);
    }

    public function hearings()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);
        return view('discipline.hearings', ['currentClient' => $currentClient]);
    }

    public function documents()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);
        return view('discipline.documents', ['currentClient' => $currentClient]);
    }
}
