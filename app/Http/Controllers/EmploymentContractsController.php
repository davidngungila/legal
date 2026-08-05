<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogger;
use App\Models\Client;
use App\Models\Employee;
use App\Models\EmploymentContract;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EmploymentContractsController extends Controller
{
    public function index(Request $request)
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);

        $query = EmploymentContract::with(['employee'])
            ->where('client_id', $clientId);

        if ($request->filled('search')) {
            $query->search($request->get('search'));
        }

        if ($request->filled('status') && $request->get('status') !== 'all') {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('contract_type') && $request->get('contract_type') !== 'all') {
            $query->where('contract_type', $request->get('contract_type'));
        }

        if ($request->filled('department') && $request->get('department') !== 'all') {
            $query->where('department', $request->get('department'));
        }

        $sortField = in_array($request->get('sort'), ['effective_date', 'expiry_date', 'basic_salary', 'created_at'])
            ? $request->get('sort')
            : 'created_at';
        $sortDir = $request->get('dir') === 'asc' ? 'asc' : 'desc';

        $contracts = $query->orderBy($sortField, $sortDir)->paginate(12)->withQueryString();

        $stats = EmploymentContract::getContractStats();
        $attention = EmploymentContract::getRequiringAttention();
        $events = EmploymentContract::getCalendarEvents();

        $departments = EmploymentContract::where('client_id', $clientId)
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->select('department')->distinct()->orderBy('department')->pluck('department')->values()->all();

        $employees = Employee::where('client_id', $clientId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('hris.employment-contracts.index', compact(
            'currentClient', 'contracts', 'stats', 'attention', 'events', 'departments', 'employees'
        ));
    }

    public function employeeContracts(Employee $employee)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $employee->client_id != $clientId) {
            return redirect()->route('employment-contracts.index')->with('error', 'Invalid request.');
        }

        $currentClient = Client::find($clientId);
        $contracts = EmploymentContract::with(['employee'])
            ->where('client_id', $clientId)
            ->where('employee_id', $employee->id)
            ->orderByDesc('effective_date')
            ->get();

        return view('hris.employment-contracts.employee-contracts', compact('currentClient', 'employee', 'contracts'));
    }

    public function store(Request $request)
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $validator = Validator::make($request->all(), $this->validationRules());

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $data = $this->mapContractData($request, $clientId, false);
            $data['contract_number'] = $request->contract_number ?: EmploymentContract::generateContractNumber();

            $uploaded = $this->handleUploads($request, $clientId);
            $data = array_merge($data, $uploaded);

            $contract = EmploymentContract::create($data);

            AuditLogger::log(
                'employment_contract.created',
                $contract,
                'Employment Contracts',
                "Employment contract {$contract->formatted_contract_number} created for employee #{$contract->employee_id}"
            );

            return redirect()->route('employment-contracts.index')->with('success', "Employment contract {$contract->formatted_contract_number} created successfully!");
        } catch (\Exception $e) {
            \Log::error('Employment contract creation failed: ' . $e->getMessage());

            return back()->with('error', 'Failed to create employment contract: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(EmploymentContract $contract)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $contract->client_id != $clientId) {
            return redirect()->route('employment-contracts.index')->with('error', 'Invalid request.');
        }

        $currentClient = Client::find($clientId);
        $employees = Employee::where('client_id', $clientId)
            ->orderBy('first_name')
            ->get();

        return view('hris.employment-contracts.edit', compact('currentClient', 'contract', 'employees'));
    }

    public function update(Request $request, EmploymentContract $contract)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $contract->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), $this->validationRules(true));

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $old = $contract->toArray();
            $data = $this->mapContractData($request, $clientId, true);
            $data = array_merge($data, $this->handleUploads($request, $clientId));

            $contract->update($data);

            AuditLogger::log(
                'employment_contract.updated',
                $contract,
                'Employment Contracts',
                "Employment contract {$contract->formatted_contract_number} updated",
                $old,
                $contract->toArray()
            );

            return redirect()->route('employment-contracts.index')->with('success', 'Employment contract updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Employment contract update failed: ' . $e->getMessage());

            return back()->with('error', 'Failed to update employment contract: ' . $e->getMessage())->withInput();
        }
    }

    public function activate(EmploymentContract $contract)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $contract->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        try {
            $old = $contract->toArray();
            $contract->update([
                'status' => 'active',
                'activated_at' => now(),
                'updated_by' => auth()->id(),
            ]);

            AuditLogger::log(
                'employment_contract.activated',
                $contract,
                'Employment Contracts',
                "Employment contract {$contract->formatted_contract_number} activated",
                $old,
                $contract->toArray()
            );

            return back()->with('success', 'Employment contract activated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to activate employment contract: ' . $e->getMessage());
        }
    }

    public function terminate(Request $request, EmploymentContract $contract)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $contract->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), [
            'termination_date' => 'required|date|before_or_equal:today',
            'termination_reason' => 'required|string|max:1000',
            'termination_type' => 'required|in:resignation,dismissal,retirement,contract_expiry,mutual_agreement,redundancy,other',
            'final_pay_date' => 'nullable|date|after_or_equal:termination_date',
            'final_settlement_amount' => 'nullable|numeric|min:0',
            'handover_completed' => 'nullable|boolean',
            'clearance_completed' => 'nullable|boolean',
            'exit_interview_completed' => 'nullable|boolean',
            'reference_letter_provided' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $old = $contract->toArray();
            $contract->update([
                'status' => 'terminated',
                'terminated_at' => $request->termination_date ? now()->setDateFrom($request->termination_date) : now(),
                'termination_reason' => $request->termination_reason,
                'termination_type' => $request->termination_type,
                'notes' => ($contract->notes ? $contract->notes . "\n" : '')
                    . "Terminated {$request->termination_date} - {$request->termination_reason}"
                    . ($request->final_settlement_amount ? " | Final settlement: {$request->final_settlement_amount}" : ''),
                'updated_by' => auth()->id(),
            ]);

            AuditLogger::log(
                'employment_contract.terminated',
                $contract,
                'Employment Contracts',
                "Employment contract {$contract->formatted_contract_number} terminated",
                $old,
                $contract->toArray()
            );

            return back()->with('success', 'Employment contract terminated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to terminate employment contract: ' . $e->getMessage())->withInput();
        }
    }

    public function renew(Request $request, EmploymentContract $contract)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $contract->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), [
            'new_effective_date' => 'required|date',
            'new_expiry_date' => 'nullable|date|after:new_effective_date',
            'renewal_reason' => 'required|string|max:1000',
            'salary_change_percentage' => 'nullable|numeric|min:-100|max:100',
            'new_basic_salary' => 'nullable|numeric|min:0',
            'terms_changes' => 'nullable|string|max:3000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $old = $contract->toArray();

            $newSalary = $request->filled('new_basic_salary')
                ? $request->new_basic_salary
                : ($contract->basic_salary);

            $contract->update([
                'status' => 'renewed',
                'effective_date' => $request->new_effective_date,
                'expiry_date' => $request->new_expiry_date ?: $contract->expiry_date,
                'basic_salary' => $newSalary,
                'total_compensation' => $contract->total_compensation
                    + (($contract->total_compensation - $contract->basic_salary) + $newSalary - $contract->basic_salary),
                'renewal_count' => $contract->renewal_count + 1,
                'last_renewal_date' => now()->toDateString(),
                'notes' => ($contract->notes ? $contract->notes . "\n" : '')
                    . "Renewed on " . now()->format('Y-m-d') . " - {$request->renewal_reason}"
                    . ($request->terms_changes ? " | Changes: {$request->terms_changes}" : ''),
                'updated_by' => auth()->id(),
            ]);

            AuditLogger::log(
                'employment_contract.renewed',
                $contract,
                'Employment Contracts',
                "Employment contract {$contract->formatted_contract_number} renewed (count {$contract->renewal_count})",
                $old,
                $contract->toArray()
            );

            return back()->with('success', 'Employment contract renewed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to renew employment contract: ' . $e->getMessage())->withInput();
        }
    }

    public function generatePdf(EmploymentContract $contract)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $contract->client_id != $clientId) {
            abort(403, 'Unauthorized access to contract record.');
        }

        $contract->load(['employee', 'client']);
        $currentClient = Client::find($clientId);

        AuditLogger::log(
            'employment_contract.pdf_generated',
            $contract,
            'Employment Contracts',
            "Contract PDF generated for {$contract->formatted_contract_number}"
        );

        $pdf = Pdf::loadView('hris.employment-contracts.pdf', compact('contract', 'currentClient'))
            ->setPaper('a4')
            ->setOption('margin-top', '16mm')
            ->setOption('margin-bottom', '18mm')
            ->setOption('margin-left', '14mm')
            ->setOption('margin-right', '14mm');

        return $pdf->download('employment-contract-' . $contract->formatted_contract_number . '.pdf');
    }

    public function statistics()
    {
        return response()->json([
            'success' => true,
            'statistics' => EmploymentContract::getContractStats(),
        ]);
    }

    public function requiringAttention()
    {
        return response()->json([
            'success' => true,
            'attention' => EmploymentContract::getRequiringAttention(),
        ]);
    }

    public function calendar()
    {
        return response()->json([
            'success' => true,
            'events' => EmploymentContract::getCalendarEvents(),
        ]);
    }

    public function uploadDocument(Request $request, EmploymentContract $contract)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $contract->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), [
            'document_type' => 'required|in:contract_document,signed_contract,witness_signature,renewal_document,amendment,termination_notice',
            'document_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('document_file');
            $field = $request->document_type;
            $fileName = time() . '_' . $field . '_' . $contract->id . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs("employment-contracts/{$clientId}", $fileName, 'public');

            $old = $contract->toArray();
            $contract->update([$field => $filePath, 'updated_by' => auth()->id()]);

            AuditLogger::log(
                'employment_contract.document_uploaded',
                $contract,
                'Employment Contracts',
                "{$field} uploaded for {$contract->formatted_contract_number}",
                $old,
                $contract->toArray()
            );

            return back()->with('success', 'Contract document uploaded successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to upload document: ' . $e->getMessage());
        }
    }

    public function downloadDocument(EmploymentContract $contract, $documentType)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $contract->client_id != $clientId) {
            abort(403, 'Unauthorized access to contract record.');
        }

        $allowed = ['contract_document', 'signed_contract', 'witness_signature'];
        if (! in_array($documentType, $allowed)) {
            abort(404, 'Document not found.');
        }

        $path = $contract->{$documentType};
        if (! $path || ! Storage::disk('public')->exists($path)) {
            abort(404, 'Document not found.');
        }

        AuditLogger::log(
            'employment_contract.document_downloaded',
            $contract,
            'Employment Contracts',
            "{$documentType} downloaded for {$contract->formatted_contract_number}"
        );

        return Storage::disk('public')->download($path);
    }

    private function validationRules(bool $isUpdate = false): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'contract_title' => 'nullable|string|max:255',
            'contract_type' => 'required|in:unspecified,fixed_term,specific_task,commission,internship',
            'contract_number' => 'nullable|string|max:50' . ($isUpdate ? '' : '|unique:employment_contracts,contract_number'),
            'effective_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'probation_end_date' => 'nullable|date|after:effective_date|before:expiry_date',
            'job_title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'reporting_line' => 'nullable|string|max:255',
            'work_location' => 'required|string|max:255',
            'work_schedule' => 'nullable|string|max:500',
            'salary_currency' => 'required|string|max:3',
            'basic_salary' => 'required|numeric|min:0',
            'housing_allowance' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'meal_allowance' => 'nullable|numeric|min:0',
            'other_allowances' => 'nullable|numeric|min:0',
            'payment_frequency' => 'required|in:weekly,biweekly,monthly,quarterly,annually',
            'payment_method' => 'nullable|in:bank_transfer,cash,cheque',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'working_hours_per_week' => 'nullable|numeric|min:1|max:80',
            'overtime_rate' => 'nullable|numeric|min:1|max:5',
            'leave_entitlement_days' => 'required|integer|min:0|max:365',
            'sick_leave_days' => 'nullable|integer|min:0|max:365',
            'public_holidays' => 'nullable|integer|min:0|max:30',
            'maternity_leave_weeks' => 'nullable|integer|min:0|max:52',
            'paternity_leave_weeks' => 'nullable|integer|min:0|max:52',
            'notice_period_days' => 'required|integer|min:1|max:365',
            'confidentiality_clause' => 'nullable|boolean',
            'non_compete_clause' => 'nullable|boolean',
            'non_compete_duration_months' => 'nullable|integer|min:1|max:60',
            'non_compete_restriction' => 'nullable|string|max:1000',
            'intellectual_property_clause' => 'nullable|boolean',
            'data_protection_clause' => 'nullable|boolean',
            'health_and_safety_clause' => 'nullable|boolean',
            'training_development_clause' => 'nullable|boolean',
            'company_policies_acknowledgment' => 'nullable|boolean',
            'termination_clause' => 'nullable|string|max:3000',
            'grievance_procedure' => 'nullable|string|max:3000',
            'disciplinary_procedure' => 'nullable|string|max:3000',
            'benefits_package' => 'nullable|string|max:3000',
            'performance_review_frequency' => 'nullable|in:monthly,quarterly,semi_annually,annually',
            'witness_name' => 'nullable|string|max:255',
            'witness_title' => 'nullable|string|max:255',
            'status' => 'required|in:draft,active,expired,terminated,renewed',
            'notes' => 'nullable|string|max:3000',
        ];
    }

    private function mapContractData(Request $request, int $clientId, bool $isUpdate): array
    {
        $basic = (float) $request->basic_salary;
        $housing = (float) ($request->housing_allowance ?: 0);
        $transport = (float) ($request->transport_allowance ?: 0);
        $meal = (float) ($request->meal_allowance ?: 0);
        $other = (float) ($request->other_allowances ?: 0);

        $booleanFields = [
            'confidentiality_clause',
            'non_compete_clause',
            'intellectual_property_clause',
            'data_protection_clause',
            'health_and_safety_clause',
            'training_development_clause',
            'company_policies_acknowledgment',
        ];

        $data = [
            'client_id' => $clientId,
            'employee_id' => $request->employee_id,
            'contract_title' => $request->contract_title,
            'contract_type' => $request->contract_type,
            'contract_number' => $request->contract_number,
            'effective_date' => $request->effective_date,
            'expiry_date' => $request->expiry_date,
            'probation_end_date' => $request->probation_end_date,
            'job_title' => $request->job_title,
            'department' => $request->department,
            'reporting_line' => $request->reporting_line,
            'work_location' => $request->work_location,
            'work_schedule' => $request->work_schedule,
            'salary_currency' => $request->salary_currency ?: 'TZS',
            'basic_salary' => $basic,
            'housing_allowance' => $housing,
            'transport_allowance' => $transport,
            'meal_allowance' => $meal,
            'other_allowances' => $other,
            'total_compensation' => $basic + $housing + $transport + $meal + $other,
            'payment_frequency' => $request->payment_frequency,
            'payment_method' => $request->payment_method,
            'bank_name' => $request->bank_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_name' => $request->bank_account_name,
            'working_hours_per_week' => $request->working_hours_per_week,
            'overtime_rate' => $request->overtime_rate,
            'leave_entitlement_days' => $request->leave_entitlement_days ?? 0,
            'sick_leave_days' => $request->sick_leave_days ?? 0,
            'public_holidays' => $request->public_holidays ?? 0,
            'maternity_leave_weeks' => $request->maternity_leave_weeks ?? 0,
            'paternity_leave_weeks' => $request->paternity_leave_weeks ?? 0,
            'notice_period_days' => $request->notice_period_days ?? 30,
            'non_compete_duration_months' => $request->non_compete_duration_months,
            'non_compete_restriction' => $request->non_compete_restriction,
            'termination_clause' => $request->termination_clause,
            'grievance_procedure' => $request->grievance_procedure,
            'disciplinary_procedure' => $request->disciplinary_procedure,
            'benefits_package' => $request->benefits_package,
            'performance_review_frequency' => $request->performance_review_frequency,
            'witness_name' => $request->witness_name,
            'witness_title' => $request->witness_title,
            'status' => $request->status ?: 'draft',
            'notes' => $request->notes,
        ];

        foreach ($booleanFields as $field) {
            $data[$field] = $request->has($field);
        }

        if ($request->status === 'active' && ! $isUpdate) {
            $data['activated_at'] = now();
        }

        return $data;
    }

    private function handleUploads(Request $request, int $clientId): array
    {
        $uploaded = [];

        $fileFields = [
            'contract_document' => 'contract_document_path',
            'signed_contract' => 'signed_contract_path',
            'witness_signature' => 'witness_signature_path',
        ];

        foreach ($fileFields as $inputName => $dbField) {
            if ($request->hasFile($inputName)) {
                $file = $request->file($inputName);
                $fileName = time() . '_' . $inputName . '_' . $clientId . '.' . $file->getClientOriginalExtension();
                $uploaded[$dbField] = $file->storeAs("employment-contracts/{$clientId}", $fileName, 'public');
            }
        }

        return $uploaded;
    }
}
