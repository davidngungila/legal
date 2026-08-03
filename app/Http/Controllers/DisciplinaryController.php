<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DisciplinaryCase;
use App\Models\DisciplinaryWarning;
use App\Models\DisciplinaryHearing;
use App\Models\DisciplinaryDocument;
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
        $cases = DisciplinaryCase::with(['employee', 'reporter', 'hearing'])
            ->where('client_id', $clientId)
            ->whereIn('status', ['reported', 'investigating'])
            ->latest()
            ->paginate(20);

        $employees = Employee::where('client_id', $clientId)->get(['id', 'employee_id', 'first_name', 'last_name']);

        $stats = [
            'active' => $cases->where('status', 'investigating')->count(),
            'reported' => $cases->where('status', 'reported')->count(),
            'total' => $cases->total(),
        ];

        return view('discipline.investigations', compact('currentClient', 'cases', 'employees', 'stats'));
    }

    public function startInvestigation(Request $request, $id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $case = DisciplinaryCase::where('client_id', $clientId)->findOrFail($id);

        $validated = $request->validate([
            'investigator' => 'required|string|max:255',
            'investigation_started_at' => 'nullable|date',
        ]);

        $case->update([
            'investigator' => $validated['investigator'],
            'investigation_started_at' => $validated['investigation_started_at'] ?: now()->toDateString(),
            'status' => 'investigating',
        ]);

        return redirect()->route('discipline.investigations')->with('success', 'Investigation started for ' . $case->case_number . '.');
    }

    public function updateInvestigation(Request $request, $id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $case = DisciplinaryCase::where('client_id', $clientId)->findOrFail($id);

        $validated = $request->validate([
            'investigator' => 'required|string|max:255',
            'investigation_findings' => 'nullable|string',
            'recommendation' => 'nullable|string',
            'status' => 'required|in:reported,investigating,hearing,resolved',
        ]);

        $case->update($validated);

        return redirect()->route('discipline.investigations')->with('success', 'Investigation updated for ' . $case->case_number . '.');
    }

    public function scheduleHearing(Request $request, $id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $case = DisciplinaryCase::where('client_id', $clientId)->findOrFail($id);

        $validated = $request->validate([
            'hearing_date' => 'required|date',
            'hearing_time' => 'nullable',
            'venue' => 'nullable|string',
            'committee_members' => 'nullable|string',
            'employee_representative' => 'nullable|string',
            'proceedings_notes' => 'nullable|string',
        ]);

        DisciplinaryHearing::updateOrCreate(['case_id' => $case->id], $validated);
        $case->update(['status' => 'hearing']);

        return redirect()->route('discipline.investigations')->with('success', 'Hearing scheduled and case moved to hearings.');
    }

    public function hearings()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);
        $cases = DisciplinaryCase::with(['employee', 'hearing'])->where('client_id', $clientId)->where('status', 'hearing')->latest()->paginate(20);
        return view('discipline.hearings', compact('currentClient', 'cases'));
    }

    public function documents()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);
        $documents = DisciplinaryDocument::with(['disciplinaryCase.employee', 'servedBy'])->whereHas('disciplinaryCase', fn($q) => $q->where('client_id', $clientId))->latest()->paginate(20);
        return view('discipline.documents', compact('currentClient', 'documents'));
    }

    public function storeHearing(Request $request)
    {
        $validated = $request->validate([
            'case_id' => 'required|exists:disciplinary_cases,id',
            'hearing_date' => 'required|date',
            'hearing_time' => 'nullable',
            'venue' => 'nullable|string',
            'committee_members' => 'nullable|string',
            'employee_representative' => 'nullable|string',
            'proceedings_notes' => 'nullable|string',
        ]);

        DisciplinaryHearing::updateOrCreate(
            ['case_id' => $validated['case_id']],
            $validated
        );

        return back()->with('success', 'Hearing details saved!');
    }

    public function storeDocument(Request $request)
    {
        $validated = $request->validate([
            'case_id' => 'required|exists:disciplinary_cases,id',
            'doc_type' => 'required|string',
            'file_path' => 'nullable|string',
        ]);

        $validated['generated_at'] = now();
        $validated['served_by'] = auth()->id();

        DisciplinaryDocument::create($validated);
        return back()->with('success', 'Document added!');
    }

    public function updateStatus(Request $request, DisciplinaryCase $case)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $validated = $request->validate([
            'status' => 'required|in:reported,investigating,hearing,resolved'
        ]);

        $case->update([
            'status' => $validated['status']
        ]);

        return back()->with('success', 'Case status updated!');
    }

    public function update(Request $request, DisciplinaryCase $case)
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

        $case->update($validated);

        return back()->with('success', 'Case updated!');
    }

    public function destroy(DisciplinaryCase $case)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        if ($case->client_id != $clientId) {
            abort(403, 'Unauthorized access to case record.');
        }

        $case->delete();
        return back()->with('success', 'Case deleted!');
    }
}
