<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogger;
use App\Models\Client;
use App\Models\Employee;
use App\Models\TrainingEnrollment;
use App\Models\TrainingProgram;
use App\Models\TrainingSession;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TrainingController extends Controller
{
    public function index(Request $request)
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);

        $query = TrainingProgram::withCount(['sessions', 'enrollments'])
            ->where('client_id', $clientId);

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%')
                    ->orWhere('provider', 'like', '%' . $search . '%');
            });
        }

        $programs = $query->orderBy('name')->paginate(9);

        $allEnrollments = TrainingEnrollment::where('client_id', $clientId)->get();
        $allSessions = TrainingSession::where('client_id', $clientId)->get();
        $activeEmployees = Employee::where('client_id', $clientId)->where('status', 'active')->count();

        $completedEnrollments = $allEnrollments->where('status', 'completed');

        $stats = [
            'active_programs' => TrainingProgram::where('client_id', $clientId)->where('status', 'active')->count(),
            'total_enrollments' => $allEnrollments->count(),
            'participation_rate' => $activeEmployees > 0 ? round($allEnrollments->pluck('employee_id')->unique()->count() / $activeEmployees * 100, 1) : 0,
            'completion_rate' => $allEnrollments->count() > 0 ? round($completedEnrollments->count() / $allEnrollments->count() * 100, 1) : 0,
            'training_hours' => round($allSessions->where('status', 'completed')->sum(function ($s) {
                return $s->start_at && $s->end_at ? $s->start_at->diffInHours($s->end_at) : 0;
            }), 1),
        ];

        $categories = TrainingProgram::where('client_id', $clientId)
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->select('category')->distinct()->orderBy('category')->pluck('category')->values()->all();

        return view('training.index', compact('currentClient', 'programs', 'stats', 'categories'));
    }

    public function store(Request $request)
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'provider' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'duration_hours' => 'nullable|numeric|min:0',
            'is_certification' => 'nullable|boolean',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $program = TrainingProgram::create([
                'client_id' => $clientId,
                'name' => $request->name,
                'code' => $request->code,
                'category' => $request->category,
                'provider' => $request->provider,
                'description' => $request->description,
                'cost' => $request->cost ?: 0,
                'currency' => $request->currency ?: 'TZS',
                'duration_hours' => $request->duration_hours ?: 0,
                'is_certification' => $request->has('is_certification'),
                'status' => $request->status ?: 'active',
                'created_by' => auth()->id(),
            ]);

            AuditLogger::log(
                'training_program.created',
                $program,
                'Training',
                "Training program created: {$program->name}"
            );

            return redirect()->route('training.index')->with('success', 'Training program created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create training program: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, TrainingProgram $program)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $program->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'provider' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'duration_hours' => 'nullable|numeric|min:0',
            'is_certification' => 'nullable|boolean',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $old = $program->toArray();
            $program->update([
                'name' => $request->name,
                'code' => $request->code,
                'category' => $request->category,
                'provider' => $request->provider,
                'description' => $request->description,
                'cost' => $request->cost ?: 0,
                'currency' => $request->currency ?: 'TZS',
                'duration_hours' => $request->duration_hours ?: 0,
                'is_certification' => $request->has('is_certification'),
                'status' => $request->status ?: 'active',
                'updated_by' => auth()->id(),
            ]);

            AuditLogger::log(
                'training_program.updated',
                $program,
                'Training',
                "Training program updated: {$program->name}",
                $old,
                $program->toArray()
            );

            return redirect()->route('training.index')->with('success', 'Training program updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update training program: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(TrainingProgram $program)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $program->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        try {
            AuditLogger::log(
                'training_program.deleted',
                $program,
                'Training',
                "Training program deleted: {$program->name}"
            );
            $program->delete();

            return redirect()->route('training.index')->with('success', 'Training program deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete training program: ' . $e->getMessage());
        }
    }

    public function show(TrainingProgram $program)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $program->client_id != $clientId) {
            return redirect()->route('training.index')->with('error', 'Invalid request.');
        }

        $currentClient = Client::find($clientId);
        $program->load(['sessions.enrollments.employee', 'sessions.plan']);

        $employees = Employee::where('client_id', $clientId)->where('status', 'active')->orderBy('first_name')->get();
        $plans = \App\Models\TrainingPlan::where('client_id', $clientId)->orderBy('name')->get();

        return view('training.programs.show', compact('currentClient', 'program', 'employees', 'plans'));
    }

    public function completions(Request $request)
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);

        $query = TrainingEnrollment::with(['employee', 'session.program'])
            ->where('client_id', $clientId)
            ->where('status', 'completed');

        if ($request->filled('search')) {
            $search = trim((string) $request->get('search'));
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('employee_id', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('program_id')) {
            $query->whereHas('session', fn ($q) => $q->where('program_id', $request->get('program_id')));
        }

        $completions = $query->orderByDesc('completed_at')->paginate(15);

        $programs = TrainingProgram::where('client_id', $clientId)->orderBy('name')->get();

        $stats = [
            'total' => TrainingEnrollment::where('client_id', $clientId)->where('status', 'completed')->count(),
            'passed' => TrainingEnrollment::where('client_id', $clientId)->where('status', 'completed')->where('passed', true)->count(),
            'certified' => TrainingEnrollment::where('client_id', $clientId)->where('status', 'completed')->whereNotNull('completion_certificate_path')->count(),
            'avg_score' => round(TrainingEnrollment::where('client_id', $clientId)->where('status', 'completed')->whereNotNull('assessment_score')->avg('assessment_score') ?? 0, 1),
        ];

        return view('training.completions', compact('currentClient', 'completions', 'programs', 'stats'));
    }

    public function certificate(TrainingEnrollment $enrollment)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $enrollment->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        try {
            if ($enrollment->status !== 'completed') {
                return back()->with('error', 'Training is not completed yet.');
            }

            $enrollment->load(['employee', 'session.program']);

            AuditLogger::log(
                'training.certificate_generated',
                $enrollment,
                'Training',
                "Certificate generated for employee #{$enrollment->employee_id} - {$enrollment->session?->title}"
            );

            $pdf = Pdf::loadView('training.certificate', [
                'enrollment' => $enrollment,
                'employee' => $enrollment->employee,
                'program' => $enrollment->session?->program,
                'session' => $enrollment->session,
                'clientName' => session('current_client') ? session('current_client')->name : 'Orvion HRIS',
            ]);

            return $pdf->download('training-certificate-' . ($enrollment->employee?->employee_id ?? $enrollment->employee_id) . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Training certificate generation failed: ' . $e->getMessage());

            return back()->with('error', 'Failed to generate certificate: ' . $e->getMessage());
        }
    }
}
