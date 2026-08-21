<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Client;
use App\Models\OnboardingChecklist;
use App\Models\OnboardingChecklistTemplate;
use App\Models\EmployeeDocument;
use App\Models\EmployeeRegistration;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OnboardingController extends Controller
{
    /**
     * Display the onboarding page.
     */
    public function index()
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return redirect()->route('clients.index')
                ->with('error', 'Please select a client first.');
        }

        $client = Client::find($currentClient['id']);
        
        if (!$client) {
            return redirect()->route('clients.index')
                ->with('error', 'Client not found.');
        }

        // Get onboarding statistics
        $stats = $this->getOnboardingStats($currentClient['id']);
        
        // Get employees currently in onboarding (probation)
        $onboardingEmployees = Employee::where('client_id', $currentClient['id'])
            ->where('status', 'probation')
            ->with(['onboardingChecklists'])
            ->orderBy('hire_date', 'desc')
            ->get()
            ->map(function ($employee) {
                $progress = $this->calculateOnboardingProgress($employee);
                $employee->onboarding_progress = $progress;
                return $employee;
            });

        // Get recently completed onboarding (employees who became active in last 30 days)
        $completedEmployees = Employee::where('client_id', $currentClient['id'])
            ->where('status', 'active')
            ->where('updated_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        // Get departments and positions for form dropdowns
        $departments = Employee::where('client_id', $currentClient['id'])
            ->distinct()
            ->pluck('department')
            ->filter()
            ->sort()
            ->values();
        
        $positions = Employee::where('client_id', $currentClient['id'])
            ->distinct()
            ->pluck('position')
            ->filter()
            ->sort()
            ->values();

        return view('onboarding.index', compact(
            'client', 
            'stats', 
            'onboardingEmployees', 
            'completedEmployees',
            'departments',
            'positions'
        ));
    }

    /**
     * Get onboarding statistics for the current client.
     */
    private function getOnboardingStats($clientId)
    {
        // Active onboarding (employees in probation)
        $activeOnboarding = Employee::where('client_id', $clientId)
            ->where('status', 'probation')
            ->count();
        
        // Completed this month (employees who became active in current month)
        $completedThisMonth = Employee::where('client_id', $clientId)
            ->where('status', 'active')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();
        
        // New hires this month (employees hired in current month)
        $newHiresThisMonth = Employee::where('client_id', $clientId)
            ->whereMonth('hire_date', now()->month)
            ->whereYear('hire_date', now()->year)
            ->count();
        
        // Average onboarding completion rate
        $totalEmployees = Employee::where('client_id', $clientId)->count();
        $activeEmployees = Employee::where('client_id', $clientId)
            ->where('status', 'active')
            ->count();
        $completionRate = $totalEmployees > 0 ? round(($activeEmployees / $totalEmployees) * 100, 1) : 0;
        
        // Pending documentation (missing emergency contact or probation end date)
        $pendingDocumentation = Employee::where('client_id', $clientId)
            ->where('status', 'probation')
            ->where(function($query) {
                $query->whereNull('probation_end_date')
                      ->orWhereNull('emergency_contact_name');
            })
            ->count();

        // Overdue probations
        $overdueProbation = Employee::where('client_id', $clientId)
            ->where('status', 'probation')
            ->whereNotNull('probation_end_date')
            ->where('probation_end_date', '<', Carbon::now())
            ->count();

        return [
            'active_onboarding' => $activeOnboarding,
            'completed_this_month' => $completedThisMonth,
            'new_hires_this_month' => $newHiresThisMonth,
            'completion_rate' => $completionRate,
            'pending_documentation' => $pendingDocumentation,
            'total_employees' => $totalEmployees,
            'overdue_probation' => $overdueProbation,
        ];
    }

    /**
     * Get approved new hires (from recruitment) available for onboarding.
     */
    public function getNewHires()
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        // Approved registrations whose email is not already an employee
        $existingEmails = Employee::where('client_id', $currentClient['id'])
            ->pluck('email')
            ->map(fn ($e) => strtolower(trim($e)))
            ->toArray();

        $registrations = EmployeeRegistration::where('client_id', $currentClient['id'])
            ->where('status', 'approved')
            ->with('hrInterview:id,job_title')
            ->orderBy('approved_at', 'desc')
            ->get()
            ->filter(fn ($r) => !in_array(strtolower(trim($r->email_address)), $existingEmails))
            ->values();

        return response()->json([
            'success' => true,
            'new_hires' => $registrations->map(function ($r) {
                $jobTitle = $r->hrInterview->job_title ?? null;
                $contract = strtolower($r->type_of_contract ?? '');
                
                if (str_contains($contract, 'intern')) {
                    $contractType = 'intern';
                } elseif (str_contains($contract, 'permanent')) {
                    $contractType = 'permanent';
                } else {
                    $contractType = 'contract';
                }

                return [
                    'id' => $r->id,
                    'employee_number' => $r->employee_number,
                    'first_name' => $r->first_name,
                    'last_name' => $r->surname,
                    'email' => $r->email_address,
                    'phone' => $r->phone_number,
                    'position' => $jobTitle,
                    'work_station' => $r->work_station,
                    'hire_date' => $r->date_employed ? $r->date_employed->format('Y-m-d') : null,
                    'contract_type' => $contractType,
                ];
            }),
        ]);
    }

    /**
     * Start onboarding process for a new employee.
     */
    public function startOnboarding(Request $request)
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
            'contract_type' => 'required|string|in:permanent,contract,intern',
            'probation_period_days' => 'nullable|integer|min:30|max:180',
        ]);

        $probationDays = $validated['probation_period_days'] ?? 90;
        $hireDate = Carbon::parse($validated['hire_date']);
        $probationEndDate = $hireDate->copy()->addDays($probationDays);

        $employee = Employee::create(array_merge($validated, [
            'client_id' => $currentClient['id'],
            'employee_id' => 'EMP-' . str_pad(Employee::where('client_id', $currentClient['id'])->count() + 1, 4, '0', STR_PAD_LEFT),
            'status' => 'probation',
            'probation_end_date' => $probationEndDate,
            'employment_type' => $validated['contract_type'],
            'created_by' => Auth::id(),
        ]));

        // Create default checklist for the new employee
        $this->createDefaultChecklist($employee);

        return response()->json([
            'success' => true,
            'message' => 'Onboarding process started successfully!',
            'employee' => $employee
        ]);
    }

    /**
     * Create default onboarding checklist for employee
     */
    private function createDefaultChecklist(Employee $employee)
    {
        // Use the client's customized template if available, otherwise the default
        $tasks = OnboardingChecklistTemplate::getForClient($employee->client_id);
        
        foreach ($tasks as $task) {
            OnboardingChecklist::create([
                'client_id' => $employee->client_id,
                'employee_id' => $employee->id,
                'task_key' => $task['task_key'],
                'task_name' => $task['task_name'],
                'category' => $task['category'],
                'order' => $task['order'],
                'is_required' => $task['is_required'],
                'is_completed' => false,
            ]);
        }
    }

    /**
     * Complete onboarding for an employee.
     */
    public function completeOnboarding(Request $request, $employeeId)
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $employee = Employee::where('client_id', $currentClient['id'])
            ->findOrFail($employeeId);

        $validated = $request->validate([
            'contract_end_date' => 'nullable|date',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'bank_name' => 'required|string|max:255',
            'bank_account' => 'required|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
        ]);

        $employee->update(array_merge($validated, [
            'status' => 'active',
            'updated_by' => Auth::id(),
        ]));

        // Mark all required checklist items as completed
        OnboardingChecklist::where('employee_id', $employee->id)
            ->where('is_required', true)
            ->where('is_completed', false)
            ->update([
                'is_completed' => true,
                'completed_at' => Carbon::now(),
                'completed_by' => Auth::id(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Onboarding completed successfully!',
            'employee' => $employee
        ]);
    }

    /**
     * Get onboarding progress for an employee.
     */
    public function getProgress($employeeId)
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $employee = Employee::where('client_id', $currentClient['id'])
            ->with(['onboardingChecklists', 'documents'])
            ->findOrFail($employeeId);

        $progress = $this->calculateOnboardingProgress($employee);
        $checklist = $employee->onboardingChecklists->groupBy('category');

        return response()->json([
            'employee' => $employee,
            'progress' => $progress,
            'checklist' => $checklist,
        ]);
    }

    /**
     * Toggle checklist item completion.
     */
    public function toggleChecklistItem(Request $request, $checklistId)
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $item = OnboardingChecklist::where('client_id', $currentClient['id'])
            ->findOrFail($checklistId);

        $item->update([
            'is_completed' => !$item->is_completed,
            'completed_at' => !$item->is_completed ? Carbon::now() : null,
            'completed_by' => !$item->is_completed ? Auth::id() : null,
            'notes' => $request->input('notes', $item->notes),
        ]);

        $employee = $item->employee;
        $progress = $this->calculateOnboardingProgress($employee);

        return response()->json([
            'success' => true,
            'item' => $item->fresh(),
            'progress' => $progress,
        ]);
    }

    /**
     * Calculate onboarding progress percentage.
     */
    private function calculateOnboardingProgress(Employee $employee)
    {
        // Calculate based on employee fields
        $fieldProgress = [
            'personal_info' => $employee->first_name && $employee->last_name && $employee->email ? 15 : 0,
            'contact_info' => $employee->phone && $employee->address ? 10 : 0,
            'job_details' => $employee->position && $employee->department && $employee->salary ? 10 : 0,
            'contract_info' => $employee->employment_type && $employee->hire_date && $employee->probation_end_date ? 10 : 0,
            'emergency_contact' => $employee->emergency_contact_name && $employee->emergency_contact_phone ? 10 : 0,
            'bank_details' => $employee->bank_name && $employee->bank_account ? 10 : 0,
        ];

        // Calculate checklist progress (35% weight)
        $totalChecklistItems = $employee->onboardingChecklists->count();
        $completedChecklistItems = $employee->onboardingChecklists->where('is_completed', true)->count();
        $checklistProgress = $totalChecklistItems > 0 ? round(($completedChecklistItems / $totalChecklistItems) * 35) : 0;

        // Calculate document progress (15% weight)
        $requiredDocs = ['national_id', 'tin_number', 'nssf_number', 'nhif_number', 'bank_account'];
        $completedDocs = 0;
        foreach ($requiredDocs as $doc) {
            if (!empty($employee->$doc)) {
                $completedDocs++;
            }
        }
        $docProgress = round(($completedDocs / count($requiredDocs)) * 15);

        $fieldTotal = array_sum($fieldProgress);
        $total = $fieldTotal + $checklistProgress + $docProgress;

        return [
            'total' => $total,
            'field_progress' => $fieldProgress,
            'checklist_progress' => $checklistProgress,
            'document_progress' => $docProgress,
            'checklist_completed' => $completedChecklistItems,
            'checklist_total' => $totalChecklistItems,
            'percentage' => min(100, $total),
        ];
    }

    /**
     * Get departments and positions for form.
     */
    public function getFormData()
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $departments = Employee::where('client_id', $currentClient['id'])
            ->distinct()
            ->pluck('department')
            ->filter()
            ->sort()
            ->values();
        
        $positions = Employee::where('client_id', $currentClient['id'])
            ->distinct()
            ->pluck('position')
            ->filter()
            ->sort()
            ->values();

        return response()->json([
            'departments' => $departments,
            'positions' => $positions,
            'contract_types' => ['permanent' => 'Permanent', 'contract' => 'Contract', 'intern' => 'Intern'],
        ]);
    }

    /**
     * Get the checklist template for the current client (customized or default).
     */
    public function getChecklistTemplate()
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $tasks = OnboardingChecklistTemplate::getForClient($currentClient['id']);
        $isCustomized = OnboardingChecklistTemplate::where('client_id', $currentClient['id'])->exists();

        return response()->json([
            'success' => true,
            'is_customized' => $isCustomized,
            'categories' => [
                'orientation' => 'Day 1: Orientation',
                'training' => 'Week 1: Department Integration',
                'documentation' => 'Month 1: Full Integration',
                'compliance' => 'Compliance & Documentation',
            ],
            'tasks' => $tasks,
        ]);
    }

    /**
     * Save the customized checklist template for the current client.
     */
    public function saveChecklistTemplate(Request $request)
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $validated = $request->validate([
            'tasks' => 'required|array|min:1',
            'tasks.*.task_name' => 'required|string|max:255',
            'tasks.*.category' => 'required|string|in:orientation,training,documentation,compliance',
            'tasks.*.is_required' => 'nullable',
        ]);

        // Generate unique task keys
        $tasks = collect($validated['tasks'])->map(function ($task, $index) {
            $isRequired = $task['is_required'] ?? true;
            if (is_string($isRequired)) {
                $isRequired = filter_var($isRequired, FILTER_VALIDATE_BOOLEAN);
            }
            return [
                'task_key' => \Illuminate\Support\Str::slug($task['task_name']) . '_' . ($index + 1),
                'task_name' => trim($task['task_name']),
                'category' => $task['category'],
                'order' => $index + 1,
                'is_required' => (bool) $isRequired,
            ];
        })->toArray();

        OnboardingChecklistTemplate::saveForClient($currentClient['id'], $tasks);

        return response()->json([
            'success' => true,
            'message' => 'Checklist template saved! It will apply to new onboardings.',
            'total_tasks' => count($tasks),
        ]);
    }

    /**
     * Reset the checklist template to the default.
     */
    public function resetChecklistTemplate()
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        OnboardingChecklistTemplate::where('client_id', $currentClient['id'])->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template reset to default.',
            'tasks' => OnboardingChecklistTemplate::getDefaultTasks(),
        ]);
    }

    /**
     * Upload onboarding document.
     */
    public function uploadDocument(Request $request, $employeeId)
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $employee = Employee::where('client_id', $currentClient['id'])
            ->findOrFail($employeeId);

        $validator = Validator::make($request->all(), [
            'document_type' => 'required|string|in:national_id,tin_certificate,nssf_card,nhif_card,employment_contract,bank_letter,passport,work_permit,medical_certificate,police_clearance,other',
            'document_name' => 'required|string|max:255',
            'document_number' => 'nullable|string|max:100',
            'issuing_authority' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'is_required' => 'boolean',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        $path = $file->store('onboarding/' . $employee->employee_id, 'public');

        $document = EmployeeDocument::create([
            'client_id' => $currentClient['id'],
            'employee_id' => $employee->id,
            'document_type' => $request->document_type,
            'document_name' => $request->document_name,
            'document_number' => $request->document_number,
            'issuing_authority' => $request->issuing_authority,
            'issue_date' => $request->issue_date,
            'expiry_date' => $request->expiry_date,
            'file_path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'status' => 'pending_verification',
            'uploaded_by' => Auth::id(),
            'is_required' => $request->boolean('is_required'),
            'is_active' => true,
        ]);

        // If this is a compliance document, update employee record
        $this->updateEmployeeComplianceFields($employee, $document);

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully!',
            'document' => $document->fresh(),
        ]);
    }

    /**
     * Update employee compliance fields based on uploaded document.
     */
    private function updateEmployeeComplianceFields(Employee $employee, EmployeeDocument $document)
    {
        $updates = [];
        
        switch ($document->document_type) {
            case 'national_id':
                if ($document->document_number) {
                    $updates['national_id'] = $document->document_number;
                }
                break;
            case 'tin_certificate':
                if ($document->document_number) {
                    $updates['tin_number'] = $document->document_number;
                }
                break;
            case 'nssf_card':
                if ($document->document_number) {
                    $updates['nssf_number'] = $document->document_number;
                }
                break;
            case 'nhif_card':
                if ($document->document_number) {
                    $updates['nhif_number'] = $document->document_number;
                }
                break;
            case 'bank_letter':
                // Could parse bank details from document
                break;
        }

        if (!empty($updates)) {
            $employee->update($updates);
        }
    }

    /**
     * Verify onboarding document.
     */
    public function verifyDocument(Request $request, $documentId)
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $document = EmployeeDocument::where('client_id', $currentClient['id'])
            ->findOrFail($documentId);

        $document->update([
            'status' => $request->boolean('approved') ? 'verified' : 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $request->boolean('approved') ? 'Document verified' : 'Document rejected',
            'document' => $document->fresh(),
        ]);
    }

    /**
     * Delete onboarding document.
     */
    public function deleteDocument($documentId)
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $document = EmployeeDocument::where('client_id', $currentClient['id'])
            ->findOrFail($documentId);

        // Delete file from storage
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully!',
        ]);
    }

    /**
     * Get required document types for onboarding.
     */
    public function getRequiredDocumentTypes()
    {
        return response()->json([
            'types' => [
                'national_id' => 'National ID / NIDA',
                'tin_certificate' => 'TIN Certificate',
                'nssf_card' => 'NSSF Card',
                'nhif_card' => 'NHIF Card',
                'employment_contract' => 'Employment Contract',
                'bank_letter' => 'Bank Letter / Account Details',
                'passport' => 'Passport (for foreigners)',
                'work_permit' => 'Work Permit (for foreigners)',
                'medical_certificate' => 'Medical Certificate',
                'police_clearance' => 'Police Clearance',
                'other' => 'Other Document',
            ],
        ]);
    }

    /**
     * Generate employment contract from template.
     */
    public function generateContract(Request $request, $employeeId)
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $employee = Employee::where('client_id', $currentClient['id'])
            ->findOrFail($employeeId);

        $client = Client::find($currentClient['id']);
        
        $validator = Validator::make($request->all(), [
            'template_id' => 'nullable|exists:documents,id',
            'contract_end_date' => 'nullable|date',
            'custom_terms' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Use default contract template if none specified
        $template = $request->template_id 
            ? \App\Models\Document::find($request->template_id)
            : \App\Models\Document::where('client_id', $currentClient['id'])
                ->where('document_type', 'contract')
                ->where('category', 'contract')
                ->first();

        if (!$template) {
            // Generate basic contract content
            $contractContent = $this->generateDefaultContract($employee, $client);
        } else {
            $contractContent = $this->renderContractTemplate($template, $employee, $client);
        }

        // Create contract document record
        $contract = \App\Models\Document::create([
            'client_id' => $currentClient['id'],
            'title' => 'Employment Contract - ' . $employee->full_name,
            'description' => 'Employment contract for ' . $employee->full_name,
            'document_type' => 'contract',
            'category' => 'contract',
            'version' => '1.0',
            'status' => 'draft',
            'effective_date' => $employee->hire_date,
            'expiry_date' => $request->contract_end_date,
            'is_public' => false,
            'is_required' => true,
            'created_by' => Auth::id(),
        ]);

        // Store contract content
        $contractPath = 'contracts/' . $employee->employee_id . '/contract-' . $contract->id . '.html';
        Storage::disk('public')->put($contractPath, $contractContent);
        $contract->update(['file_path' => $contractPath]);

        return response()->json([
            'success' => true,
            'message' => 'Contract generated successfully!',
            'contract' => $contract->fresh(),
            'content' => $contractContent,
        ]);
    }

    /**
     * Render contract template with employee data.
     */
    private function renderContractTemplate($template, $employee, $client)
    {
        $content = view('documents.pdf', [
            'document' => $template,
            'client' => $client,
            'employee' => $employee,
        ])->render();

        return $content;
    }

    /**
     * Generate default contract if no template exists.
     */
    private function generateDefaultContract($employee, $client)
    {
        return '
<!DOCTYPE html>
<html>
<head>
    <title>Employment Contract - ' . $employee->full_name . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #4f46e5; padding-bottom: 20px; }
        .company-name { font-size: 24px; font-weight: bold; color: #4f46e5; }
        .contract-title { font-size: 20px; font-weight: bold; margin: 20px 0; text-align: center; }
        .section { margin: 20px 0; }
        .section-title { font-weight: bold; font-size: 16px; margin-bottom: 10px; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; }
        .field { margin: 8px 0; }
        .label { font-weight: bold; display: inline-block; width: 200px; }
        .value { display: inline-block; }
        .signature-section { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-block { width: 45%; }
        .signature-line { border-top: 1px solid #374151; margin-top: 50px; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">' . $client->name . '</div>
        <div>LegalHR Tanzania</div>
    </div>
    
    <div class="contract-title">EMPLOYMENT CONTRACT</div>
    
    <p>This Employment Contract ("Contract") is made on ' . now()->format('F j, Y') . ' between:</p>
    
    <div class="section">
        <div class="section-title">1. PARTIES</div>
        <div class="field"><span class="label">Employer:</span> <span class="value">' . $client->name . '</span></div>
        <div class="field"><span class="label">Employee:</span> <span class="value">' . $employee->full_name . '</span></div>
        <div class="field"><span class="label">Employee ID:</span> <span class="value">' . $employee->employee_id . '</span></div>
    </div>
    
    <div class="section">
        <div class="section-title">2. POSITION & DEPARTMENT</div>
        <div class="field"><span class="label">Position:</span> <span class="value">' . $employee->position . '</span></div>
        <div class="field"><span class="label">Department:</span> <span class="value">' . $employee->department . '</span></div>
        <div class="field"><span class="label">Reports To:</span> <span class="value">' . ($employee->manager_id ? "Manager" : "Direct Supervisor") . '</span></div>
    </div>
    
    <div class="section">
        <div class="section-title">3. EMPLOYMENT TERMS</div>
        <div class="field"><span class="label">Start Date:</span> <span class="value">' . $employee->hire_date->format('F j, Y') . '</span></div>
        <div class="field"><span class="label">Contract Type:</span> <span class="value">' . ucfirst($employee->employment_type) . '</span></div>
        <div class="field"><span class="label">Probation Period:</span> <span class="value">' . ($employee->probation_end_date ? $employee->probation_end_date->format('F j, Y') : '90 days') . '</span></div>
    </div>
    
    <div class="section">
        <div class="section-title">4. COMPENSATION</div>
        <div class="field"><span class="label">Salary:</span> <span class="value">' . $employee->formatted_salary . ' per month</span></div>
        <div class="field"><span class="label">Payment Frequency:</span> <span class="value">Monthly</span></div>
        <div class="field"><span class="label">Currency:</span> <span class="value">TZS</span></div>
    </div>
    
    <div class="section">
        <div class="section-title">5. WORKING HOURS</div>
        <div class="field"><span class="label">Schedule:</span> <span class="value">Standard business hours (Mon-Fri, 8:00 AM - 5:00 PM)</span></div>
        <div class="field"><span class="label">Overtime:</span> <span class="value">As per Tanzania Labor Law</span></div>
    </div>
    
    <div class="section">
        <div class="section-title">6. LEAVE ENTITLEMENTS</div>
        <div class="field"><span class="label">Annual Leave:</span> <span class="value">28 calendar days per year</span></div>
        <div class="field"><span class="label">Sick Leave:</span> <span class="value">As per Tanzania Labor Law</span></div>
        <div class="field"><span class="label">Maternity/Paternity:</span> <span class="value">As per Tanzania Labor Law</span></div>
    </div>
    
    <div class="section">
        <div class="section-title">7. TERMINATION</div>
        <div class="field"><span class="label">Notice Period:</span> <span class="value">As per Tanzania Labor Law (minimum 7 days during probation, 28 days after)</span></div>
        <div class="field"><span class="label">Termination Grounds:</span> <span class="value">As per Tanzania Employment and Labor Relations Act, 2004</span></div>
    </div>
    
    <div class="section">
        <div class="section-title">8. CONFIDENTIALITY & IP</div>
        <p>The Employee agrees to maintain confidentiality of all proprietary information and that all intellectual property created during employment belongs to the Employer.</p>
    </div>
    
    <div class="signature-section">
        <div class="signature-block">
            <div class="signature-line"></div>
            <div><strong>' . $client->name . '</strong></div>
            <div>Employer / Authorized Representative</div>
            <div>Date: _______________</div>
        </div>
        <div class="signature-block">
            <div class="signature-line"></div>
            <div><strong>' . $employee->full_name . '</strong></div>
            <div>Employee</div>
            <div>Date: _______________</div>
        </div>
    </div>
</body>
</html>';
    }

    /**
     * Sign contract digitally.
     */
    public function signContract(Request $request, $contractId)
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $contract = \App\Models\Document::where('client_id', $currentClient['id'])
            ->findOrFail($contractId);

        $validator = Validator::make($request->all(), [
            'signature_data' => 'required|string', // Base64 encoded signature
            'signed_by' => 'required|string|in:employer,employee',
            'witness_name' => 'nullable|string|max:255',
            'witness_signature' => 'nullable|string', // Base64 encoded
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $signedBy = $request->signed_by;
        $signatureData = $request->signature_data;

        // Store signature
        $signaturePath = 'contracts/signatures/' . $contractId . '_' . $signedBy . '_' . time() . '.png';
        $signatureImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureData));
        Storage::disk('public')->put($signaturePath, $signatureImage);

        $updateField = $signedBy === 'employer' ? 'employer_signed_at' : 'employee_signed_at';
        $signatureField = $signedBy === 'employer' ? 'employer_signature_path' : 'employee_signature_path';

        $contract->update([
            $updateField => Carbon::now(),
            $signatureField => $signaturePath,
        ]);

        // If both signed, mark as active
        if ($contract->employer_signed_at && $contract->employee_signed_at) {
            $contract->update(['status' => 'active']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Contract signed successfully!',
            'contract' => $contract->fresh(),
        ]);
    }

    /**
     * Get contract for signing.
     */
    public function getContractForSigning($contractId)
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $contract = \App\Models\Document::where('client_id', $currentClient['id'])
            ->where('id', $contractId)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'contract' => $contract,
        ]);
    }

    /**
     * Get available policy types.
     */
    public function getPolicyTypes()
    {
        return response()->json([
            'types' => PolicyAcknowledgment::getRequiredPolicies(),
        ]);
    }

    /**
     * Assign policies to an employee.
     */
    public function assignPolicies(Request $request, $employeeId)
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $employee = Employee::where('client_id', $currentClient['id'])
            ->findOrFail($employeeId);

        $validator = Validator::make($request->all(), [
            'policy_keys' => 'required|array',
            'policy_keys.*' => 'string',
            'document_ids' => 'nullable|array',
            'document_ids.*' => 'exists:documents,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $policyKeys = $request->policy_keys;
        $documentIds = $request->document_ids ?? [];
        
        $created = [];
        foreach ($policyKeys as $index => $key) {
            $documentId = isset($documentIds[$index]) ? $documentIds[$index] : null;
            
            $acknowledgment = PolicyAcknowledgment::firstOrCreate([
                'client_id' => $currentClient['id'],
                'employee_id' => $employee->id,
                'policy_name' => $key,
            ], [
                'policy_version' => '1.0',
                'document_id' => $documentId,
                'acknowledged' => false,
            ]);
            
            $created[] = $acknowledgment;
        }

        return response()->json([
            'success' => true,
            'message' => 'Policies assigned successfully!',
            'policies' => $created,
        ]);
    }

    /**
     * Acknowledge a policy.
     */
    public function acknowledgePolicy(Request $request, $acknowledgmentId)
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $acknowledgment = PolicyAcknowledgment::where('client_id', $currentClient['id'])
            ->findOrFail($acknowledgmentId);

        $validator = Validator::make($request->all(), [
            'signature_data' => 'nullable|string', // Base64 signature
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $acknowledgment->update([
            'acknowledged' => true,
            'acknowledged_at' => Carbon::now(),
            'signature_data' => $request->signature_data,
            'notes' => $request->notes,
        ]);

        // Check if all required policies are acknowledged
        $this->checkOnboardingCompletion($acknowledgment->employee_id);

        return response()->json([
            'success' => true,
            'message' => 'Policy acknowledged successfully!',
            'acknowledgment' => $acknowledgment->fresh(),
        ]);
    }

    /**
     * Check if employee has completed all policy acknowledgments.
     */
    private function checkOnboardingCompletion($employeeId)
    {
        $pendingPolicies = PolicyAcknowledgment::where('client_id', session('current_client')['id'])
            ->where('employee_id', $employeeId)
            ->where('acknowledged', false)
            ->count();

        if ($pendingPolicies === 0) {
            // Mark policy acknowledgment checklist items as complete
            OnboardingChecklist::where('employee_id', $employeeId)
                ->where('category', 'compliance')
                ->where('is_completed', false)
                ->update([
                    'is_completed' => true,
                    'completed_at' => Carbon::now(),
                    'completed_by' => auth()->id(),
                ]);
        }
    }

    /**
     * Get employee policies.
     */
    public function getEmployeePolicies($employeeId)
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $employee = Employee::where('client_id', $currentClient['id'])
            ->findOrFail($employeeId);

        $policies = PolicyAcknowledgment::where('client_id', $currentClient['id'])
            ->where('employee_id', $employeeId)
            ->with('document')
            ->orderBy('policy_name')
            ->get();

        $allPolicies = PolicyAcknowledgment::getRequiredPolicies();
        $assignedKeys = $policies->pluck('policy_name')->toArray();
        
        // Add missing policies as pending
        foreach ($allPolicies as $key => $name) {
            if (!in_array($key, $assignedKeys)) {
                $policies->push((object)[
                    'policy_name' => $key,
                    'policy_version' => '1.0',
                    'acknowledged' => false,
                    'acknowledged_at' => null,
                    'is_required' => true,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'employee' => $employee,
            'policies' => $policies,
        ]);
    }

    /**
     * Export onboarding report as CSV.
     */
    public function exportReport()
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return redirect()->route('clients.index')
                ->with('error', 'Please select a client first.');
        }

        $clientId = $currentClient['id'];

        // Onboarding employees with progress
        $onboardingEmployees = Employee::where('client_id', $clientId)
            ->where('status', 'probation')
            ->with(['onboardingChecklists'])
            ->orderBy('hire_date', 'desc')
            ->get()
            ->map(function ($employee) {
                $progress = $this->calculateOnboardingProgress($employee);
                $employee->onboarding_progress = $progress;
                return $employee;
            });

        // Recently completed employees
        $completedEmployees = Employee::where('client_id', $clientId)
            ->where('status', 'active')
            ->where('updated_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('updated_at', 'desc')
            ->get();

        $fileName = 'onboarding-report-' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Section 1: Active Onboarding
        fputcsv($output, ['ACTIVE ONBOARDING PROCESSES']);
        fputcsv($output, [
            'Employee Name', 'Employee ID', 'Position', 'Department', 'Hire Date',
            'Probation End', 'Progress (%)', 'Checklist Completed', 'Checklist Total',
            'Documents Verified', 'Documents Pending', 'Status',
        ]);

        foreach ($onboardingEmployees as $emp) {
            $checklist = $emp->onboardingChecklists;
            $completedCount = $checklist->where('is_completed', true)->count();
            $totalCount = $checklist->count();
            $overdue = $emp->probation_end_date && $emp->probation_end_date->isPast();

            fputcsv($output, [
                $emp->first_name . ' ' . $emp->last_name,
                $emp->employee_id ?? $emp->id,
                $emp->position,
                $emp->department,
                $emp->hire_date ? $emp->hire_date->format('Y-m-d') : '',
                $emp->probation_end_date ? $emp->probation_end_date->format('Y-m-d') : '',
                $emp->onboarding_progress['percentage'] ?? 0,
                $completedCount,
                $totalCount,
                $emp->documents()->where('status', 'verified')->count(),
                $emp->documents()->where('status', '!=', 'verified')->count(),
                $overdue ? 'Overdue' : 'In Progress',
            ]);
        }

        fputcsv($output, []);

        // Section 2: Recently Completed
        fputcsv($output, ['RECENTLY COMPLETED ONBOARDING (LAST 30 DAYS)']);
        fputcsv($output, [
            'Employee Name', 'Employee ID', 'Position', 'Department',
            'Hire Date', 'Completed On',
        ]);

        foreach ($completedEmployees as $emp) {
            fputcsv($output, [
                $emp->first_name . ' ' . $emp->last_name,
                $emp->employee_id ?? $emp->id,
                $emp->position,
                $emp->department,
                $emp->hire_date ? $emp->hire_date->format('Y-m-d') : '',
                $emp->updated_at->format('Y-m-d'),
            ]);
        }

        fclose($output);
        exit;
    }
}