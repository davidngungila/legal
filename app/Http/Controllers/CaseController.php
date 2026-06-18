<?php

namespace App\Http\Controllers;

use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\Employee;
use App\Models\LegalCase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CaseController extends Controller
{
    public function index(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('clients.index')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);
        if (!$currentClient) {
            return redirect()->route('clients.index')->with('error', 'Selected client not found.');
        }

        $query = LegalCase::with(['employee', 'assignedUser', 'creator'])
            ->where('client_id', $clientId);

        if ($request->filled('type')) {
            $query->where('case_type', $request->string('type')->toString());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->string('priority')->toString());
        }

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());
            $query->where(function ($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($employeeQuery) use ($search) {
                        $employeeQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('employee_id', 'like', "%{$search}%");
                    });
            });
        }

        $cases = $query->orderByRaw("
                CASE priority
                    WHEN 'high' THEN 1
                    WHEN 'medium' THEN 2
                    ELSE 3
                END
            ")
            ->orderByDesc('opened_date')
            ->get();

        $stats = [
            'active' => LegalCase::where('client_id', $clientId)->whereNotIn('status', ['resolved', 'closed'])->count(),
            'pending_review' => LegalCase::where('client_id', $clientId)->whereIn('status', ['pending', 'review', 'documentation'])->count(),
            'resolved_this_month' => LegalCase::where('client_id', $clientId)
                ->whereIn('status', ['resolved', 'closed'])
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->count(),
            'high_priority' => LegalCase::where('client_id', $clientId)->where('priority', 'high')->whereNotIn('status', ['resolved', 'closed'])->count(),
        ];

        $categories = collect(['disciplinary', 'grievance', 'complaint', 'legal'])
            ->map(function ($type) use ($clientId) {
                return [
                    'type' => ucfirst($type),
                    'slug' => $type,
                    'count' => LegalCase::where('client_id', $clientId)->where('case_type', $type)->count(),
                    'color' => match ($type) {
                        'disciplinary' => 'red',
                        'grievance' => 'yellow',
                        'complaint' => 'orange',
                        default => 'purple',
                    },
                    'icon' => match ($type) {
                        'disciplinary' => 'alert-triangle',
                        'grievance' => 'message-square',
                        'complaint' => 'flag',
                        default => 'scale',
                    },
                ];
            });

        $recentActivities = CaseActivity::with(['legalCase.employee', 'user'])
            ->whereHas('legalCase', fn ($q) => $q->where('client_id', $clientId))
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        $employees = Employee::where('client_id', $clientId)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $assignees = User::where('current_client_id', $clientId)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        // Get templates from session or use default
        $templates = session('case_templates', collect([
            ['name' => 'Disciplinary Notice', 'type' => 'disciplinary', 'uses' => 45, 'status' => 'Active', 'subject' => 'Disciplinary hearing notice'],
            ['name' => 'Grievance Form', 'type' => 'grievance', 'uses' => 32, 'status' => 'Active', 'subject' => 'Employee grievance intake'],
            ['name' => 'Warning Letter', 'type' => 'disciplinary', 'uses' => 28, 'status' => 'Active', 'subject' => 'Formal written warning'],
            ['name' => 'Termination Notice', 'type' => 'legal', 'uses' => 15, 'status' => 'Active', 'subject' => 'Contract termination review'],
            ['name' => 'Complaint Form', 'type' => 'complaint', 'uses' => 22, 'status' => 'Active', 'subject' => 'Workplace complaint review'],
            ['name' => 'Settlement Agreement', 'type' => 'legal', 'uses' => 8, 'status' => 'Active', 'subject' => 'Dispute settlement preparation'],
        ]));

        $casesJson = $cases->map(function (LegalCase $case) {
            return [
                'id' => $case->id,
                'case_number' => $case->case_number,
                'employee_id' => $case->employee_id,
                'employee_name' => trim(($case->employee?->first_name ?? '') . ' ' . ($case->employee?->last_name ?? '')) ?: 'Unassigned Employee',
                'employee_code' => $case->employee?->employee_id,
                'case_type' => $case->case_type,
                'subject' => $case->subject,
                'description' => $case->description,
                'opened_date' => optional($case->opened_date)->format('Y-m-d'),
                'due_date' => optional($case->due_date)->format('Y-m-d'),
                'priority' => $case->priority,
                'status' => $case->status,
                'assigned_to' => $case->assigned_to,
                'assigned_to_name' => trim(($case->assignedUser?->first_name ?? '') . ' ' . ($case->assignedUser?->last_name ?? '')),
                'resolution_notes' => $case->resolution_notes,
                'updated_at' => optional($case->updated_at)->diffForHumans(),
            ];
        })->values();

        return view('casemanagement.index', compact(
            'currentClient',
            'cases',
            'stats',
            'categories',
            'recentActivities',
            'employees',
            'assignees',
            'templates',
            'casesJson'
        ));
    }

    public function store(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('clients.index')->with('error', 'Please select a client first.');
        }

        $validated = $this->validateCase($request);

        DB::transaction(function () use ($validated, $clientId) {
            $case = LegalCase::create([
                'client_id' => $clientId,
                'employee_id' => $validated['employee_id'] ?: null,
                'case_number' => $this->generateCaseNumber($clientId),
                'case_type' => $validated['case_type'],
                'subject' => $validated['subject'],
                'description' => $validated['description'],
                'opened_date' => $validated['opened_date'],
                'due_date' => $validated['due_date'] ?: null,
                'priority' => $validated['priority'],
                'status' => $validated['status'],
                'assigned_to' => $validated['assigned_to'] ?: Auth::id(),
                'created_by' => Auth::id(),
                'resolution_notes' => $validated['resolution_notes'] ?: null,
            ]);

            $this->logActivity($case, 'created', 'Case created by ' . (Auth::user()?->first_name ?? 'System'));
        });

        return redirect()->route('casemanagement.index')->with('success', 'Case created successfully.');
    }

    public function update(Request $request, LegalCase $case)
    {
        $clientId = session('current_client_id');
        if (!$clientId || (int) $case->client_id !== (int) $clientId) {
            return redirect()->route('casemanagement.index')->with('error', 'Case not found for the selected client.');
        }

        $validated = $this->validateCase($request);

        DB::transaction(function () use ($validated, $case) {
            $oldValues = $case->only(['case_type', 'subject', 'priority', 'status', 'assigned_to', 'due_date']);
            $previousStatus = $case->status;

            $case->update([
                'employee_id' => $validated['employee_id'] ?: null,
                'case_type' => $validated['case_type'],
                'subject' => $validated['subject'],
                'description' => $validated['description'],
                'opened_date' => $validated['opened_date'],
                'due_date' => $validated['due_date'] ?: null,
                'priority' => $validated['priority'],
                'status' => $validated['status'],
                'assigned_to' => $validated['assigned_to'] ?: null,
                'resolution_notes' => $validated['resolution_notes'] ?: null,
            ]);

            $this->logActivity(
                $case,
                'updated',
                'Case details updated',
                $oldValues,
                $case->only(['case_type', 'subject', 'priority', 'status', 'assigned_to', 'due_date'])
            );

            if ($previousStatus !== $case->status) {
                $this->logActivity(
                    $case,
                    'status_changed',
                    'Status changed from ' . ucfirst((string) $previousStatus) . ' to ' . ucfirst((string) $case->status),
                    ['status' => $previousStatus],
                    ['status' => $case->status]
                );
            }
        });

        return redirect()->route('casemanagement.index')->with('success', 'Case updated successfully.');
    }

    public function export(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('clients.index')->with('error', 'Please select a client first.');
        }

        $cases = LegalCase::with('employee')
            ->where('client_id', $clientId)
            ->orderByDesc('opened_date')
            ->get();

        $filename = 'case_management_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($cases) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Case Number', 'Employee', 'Employee ID', 'Type', 'Subject', 'Opened Date', 'Due Date', 'Priority', 'Status', 'Assigned To']);

            foreach ($cases as $case) {
                fputcsv($handle, [
                    $case->case_number,
                    trim(($case->employee?->first_name ?? '') . ' ' . ($case->employee?->last_name ?? '')),
                    $case->employee?->employee_id,
                    ucfirst((string) $case->case_type),
                    $case->subject,
                    optional($case->opened_date)->format('Y-m-d'),
                    optional($case->due_date)->format('Y-m-d'),
                    ucfirst((string) $case->priority),
                    ucfirst(str_replace('_', ' ', (string) $case->status)),
                    trim(($case->assignedUser?->first_name ?? '') . ' ' . ($case->assignedUser?->last_name ?? '')),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function validateCase(Request $request): array
    {
        return $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'case_type' => 'required|in:disciplinary,grievance,complaint,legal',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'opened_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:opened_date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,review,under_investigation,documentation,resolution,resolved,closed',
            'assigned_to' => 'nullable|exists:users,id',
            'resolution_notes' => 'nullable|string|max:5000',
        ]);
    }

    private function generateCaseNumber(int $clientId): string
    {
        $count = LegalCase::where('client_id', $clientId)->count() + 1;
        return 'CASE-' . now()->format('Y') . '-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
    }

    private function logActivity(LegalCase $case, string $action, string $description, array $oldValues = [], array $newValues = []): void
    {
        CaseActivity::create([
            'legal_case_id' => $case->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
        ]);
    }

    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:disciplinary,grievance,complaint,legal',
            'subject' => 'required|string|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);

        // Get current templates from session or default
        $templates = session('case_templates', collect([
            ['name' => 'Disciplinary Notice', 'type' => 'disciplinary', 'uses' => 45, 'status' => 'Active', 'subject' => 'Disciplinary hearing notice'],
            ['name' => 'Grievance Form', 'type' => 'grievance', 'uses' => 32, 'status' => 'Active', 'subject' => 'Employee grievance intake'],
            ['name' => 'Warning Letter', 'type' => 'disciplinary', 'uses' => 28, 'status' => 'Active', 'subject' => 'Formal written warning'],
            ['name' => 'Termination Notice', 'type' => 'legal', 'uses' => 15, 'status' => 'Active', 'subject' => 'Contract termination review'],
            ['name' => 'Complaint Form', 'type' => 'complaint', 'uses' => 22, 'status' => 'Active', 'subject' => 'Workplace complaint review'],
            ['name' => 'Settlement Agreement', 'type' => 'legal', 'uses' => 8, 'status' => 'Active', 'subject' => 'Dispute settlement preparation'],
        ]));

        // Add new template
        $templates->push([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'status' => $validated['status'],
            'uses' => 0,
        ]);

        // Save back to session
        session(['case_templates' => $templates]);

        return redirect()->route('casemanagement.index')->with('success', 'Template created successfully!');
    }

    public function updateTemplate(Request $request, int $index)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:disciplinary,grievance,complaint,legal',
            'subject' => 'required|string|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);

        // Get current templates from session or default
        $templates = session('case_templates', collect([
            ['name' => 'Disciplinary Notice', 'type' => 'disciplinary', 'uses' => 45, 'status' => 'Active', 'subject' => 'Disciplinary hearing notice'],
            ['name' => 'Grievance Form', 'type' => 'grievance', 'uses' => 32, 'status' => 'Active', 'subject' => 'Employee grievance intake'],
            ['name' => 'Warning Letter', 'type' => 'disciplinary', 'uses' => 28, 'status' => 'Active', 'subject' => 'Formal written warning'],
            ['name' => 'Termination Notice', 'type' => 'legal', 'uses' => 15, 'status' => 'Active', 'subject' => 'Contract termination review'],
            ['name' => 'Complaint Form', 'type' => 'complaint', 'uses' => 22, 'status' => 'Active', 'subject' => 'Workplace complaint review'],
            ['name' => 'Settlement Agreement', 'type' => 'legal', 'uses' => 8, 'status' => 'Active', 'subject' => 'Dispute settlement preparation'],
        ]));

        // Update template at index
        if ($templates->has($index)) {
            $template = $templates->get($index);
            $templates->put($index, [
                'name' => $validated['name'],
                'type' => $validated['type'],
                'subject' => $validated['subject'],
                'status' => $validated['status'],
                'uses' => $template['uses'] ?? 0,
            ]);

            // Save back to session
            session(['case_templates' => $templates]);

            return redirect()->route('casemanagement.index')->with('success', 'Template updated successfully!');
        }

        return redirect()->route('casemanagement.index')->with('error', 'Template not found!');
    }
}
