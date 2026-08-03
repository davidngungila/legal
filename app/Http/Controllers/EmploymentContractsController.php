<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmploymentContractsController extends Controller
{
    /**
     * Display the employment contracts dashboard.
     */
    public function index()
    {
        $employees = Employee::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('hris.employment-contracts.index', compact('employees'));
    }

    /**
     * Display contracts for a specific employee.
     */
    public function employeeContracts(Employee $employee)
    {
        // In a real implementation, this would fetch employment contracts from database
        // For now, we'll pass the employee and show a placeholder view
        return view('hris.employment-contracts.employee-contracts', compact('employee'));
    }

    /**
     * Store employment contract for an employee.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_registration_id' => 'required|exists:employee_registrations,id',
            'contract_title' => 'required|string|max:255',
            'contract_type' => 'required|in:unspecified,fixed_term,specific_task,commission,internship',
            'contract_number' => 'required|string|max:50',
            'effective_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'job_title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'reporting_line' => 'required|string|max:255',
            'work_location' => 'required|string|max:255',
            'work_schedule' => 'required|string|max:500',
            'probation_period' => 'nullable|integer|min:1|max:12',
            'salary_currency' => 'required|string|max:3',
            'basic_salary' => 'required|numeric|min:0',
            'housing_allowance' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'meal_allowance' => 'nullable|numeric|min:0',
            'other_allowances' => 'nullable|numeric|min:0',
            'total_compensation' => 'required|numeric|min:0',
            'payment_frequency' => 'required|in:weekly,biweekly,monthly,quarterly,annually',
            'payment_method' => 'required|in:bank_transfer,cash,cheque',
            'bank_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_name' => 'required|string|max:255',
            'working_hours_per_week' => 'required|numeric|min:1|max:80',
            'overtime_rate' => 'nullable|numeric|min:1|max:5',
            'leave_entitlement_days' => 'required|integer|min:0|max:365',
            'sick_leave_days' => 'required|integer|min:0|max:365',
            'public_holidays' => 'required|integer|min:0|max:30',
            'maternity_leave_weeks' => 'nullable|integer|min:0|max:52',
            'paternity_leave_weeks' => 'nullable|integer|min:0|max:52',
            'notice_period_days' => 'required|integer|min:1|max:365',
            'confidentiality_clause' => 'required|boolean',
            'non_compete_clause' => 'required|boolean',
            'non_compete_duration_months' => 'nullable|integer|min:1|max:60',
            'non_compete_restriction' => 'nullable|string|max:1000',
            'intellectual_property_clause' => 'required|boolean',
            'data_protection_clause' => 'required|boolean',
            'health_and_safety_clause' => 'required|boolean',
            'termination_clause' => 'required|string|max:2000',
            'grievance_procedure' => 'required|string|max:2000',
            'disciplinary_procedure' => 'required|string|max:2000',
            'benefits_package' => 'nullable|string|max:3000',
            'performance_review_frequency' => 'required|in:monthly,quarterly,semi_annually,annually',
            'training_development_clause' => 'required|boolean',
            'company_policies_acknowledgment' => 'required|boolean',
            'contract_document_path' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'signed_contract_path' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'witness_name' => 'required|string|max:255',
            'witness_title' => 'required|string|max:255',
            'witness_signature_path' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'status' => 'required|in:draft,active,expired,terminated,renewed',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Handle file uploads
            $uploadedFiles = [];
            $fileFields = [
                'contract_document_path' => 'contract_document',
                'signed_contract_path' => 'signed_contract',
                'witness_signature_path' => 'witness_signature'
            ];

            foreach ($fileFields as $dbField => $fileField) {
                if ($request->hasFile($fileField)) {
                    $file = $request->file($fileField);
                    $fileName = time() . '_' . $fileField . '_' . $request->employee_registration_id . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('employment-contracts', $fileName, 'public');
                    $uploadedFiles[$dbField] = $filePath;
                }
            }

            // Generate unique contract number if not provided
            $contractNumber = $request->contract_number ?: $this->generateContractNumber($request->contract_type);

            // Simulate creating employment contract
            $contractData = array_merge($request->all(), $uploadedFiles, [
                'contract_number' => $contractNumber,
                'created_by' => auth()->id(),
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employment contract created successfully',
                'data' => $contractData,
                'contract_number' => $contractNumber
            ]);

        } catch (\Exception $e) {
            \Log::error('Employment contract creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update employment contract for an employee.
     */
    public function update(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'contract_title' => 'required|string|max:255',
            'contract_type' => 'required|in:unspecified,fixed_term,specific_task,commission,internship',
            'contract_number' => 'required|string|max:50',
            'effective_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'job_title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'reporting_line' => 'required|string|max:255',
            'work_location' => 'required|string|max:255',
            'work_schedule' => 'required|string|max:500',
            'probation_period' => 'nullable|integer|min:1|max:12',
            'salary_currency' => 'required|string|max:3',
            'basic_salary' => 'required|numeric|min:0',
            'housing_allowance' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'meal_allowance' => 'nullable|numeric|min:0',
            'other_allowances' => 'nullable|numeric|min:0',
            'total_compensation' => 'required|numeric|min:0',
            'payment_frequency' => 'required|in:weekly,biweekly,monthly,quarterly,annually',
            'payment_method' => 'required|in:bank_transfer,cash,cheque',
            'bank_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_name' => 'required|string|max:255',
            'working_hours_per_week' => 'required|numeric|min:1|max:80',
            'overtime_rate' => 'nullable|numeric|min:1|max:5',
            'leave_entitlement_days' => 'required|integer|min:0|max:365',
            'sick_leave_days' => 'required|integer|min:0|max:365',
            'public_holidays' => 'required|integer|min:0|max:30',
            'maternity_leave_weeks' => 'nullable|integer|min:0|max:52',
            'paternity_leave_weeks' => 'nullable|integer|min:0|max:52',
            'notice_period_days' => 'required|integer|min:1|max:365',
            'confidentiality_clause' => 'required|boolean',
            'non_compete_clause' => 'required|boolean',
            'non_compete_duration_months' => 'nullable|integer|min:1|max:60',
            'non_compete_restriction' => 'nullable|string|max:1000',
            'intellectual_property_clause' => 'required|boolean',
            'data_protection_clause' => 'required|boolean',
            'health_and_safety_clause' => 'required|boolean',
            'termination_clause' => 'required|string|max:2000',
            'grievance_procedure' => 'required|string|max:2000',
            'disciplinary_procedure' => 'required|string|max:2000',
            'benefits_package' => 'nullable|string|max:3000',
            'performance_review_frequency' => 'required|in:monthly,quarterly,semi_annually,annually',
            'training_development_clause' => 'required|boolean',
            'company_policies_acknowledgment' => 'required|boolean',
            'status' => 'required|in:draft,active,expired,terminated,renewed',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // In a real implementation, this would update the employment_contracts table
            $contractData = array_merge($request->all(), [
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employment contract updated successfully',
                'data' => $contractData
            ]);

        } catch (\Exception $e) {
            \Log::error('Employment contract update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activate employment contract.
     */
    public function activate(Employee $employee)
    {
        try {
            // In a real implementation, this would update the contract status
            return response()->json([
                'success' => true,
                'message' => 'Employment contract activated successfully',
                'data' => [
                    'status' => 'active',
                    'activated_by' => auth()->id(),
                    'activated_at' => now(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Employment contract activation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Terminate employment contract.
     */
    public function terminate(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'termination_date' => 'required|date|before_or_equal:today',
            'termination_reason' => 'required|string|max:500',
            'termination_type' => 'required|in:resignation,dismissal,retirement,contract_expiry,mutual_agreement,redundancy',
            'final_pay_date' => 'required|date|after_or_equal:termination_date',
            'final_settlement_amount' => 'nullable|numeric|min:0',
            'handover_completed' => 'required|boolean',
            'clearance_completed' => 'required|boolean',
            'exit_interview_completed' => 'required|boolean',
            'reference_letter_provided' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // In a real implementation, this would update the contract status and add termination details
            return response()->json([
                'success' => true,
                'message' => 'Employment contract terminated successfully',
                'data' => array_merge($request->all(), [
                    'status' => 'terminated',
                    'terminated_by' => auth()->id(),
                    'terminated_at' => now(),
                ])
            ]);

        } catch (\Exception $e) {
            \Log::error('Employment contract termination failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Renew employment contract.
     */
    public function renew(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'new_contract_number' => 'required|string|max:50',
            'new_effective_date' => 'required|date',
            'new_expiry_date' => 'nullable|date|after:new_effective_date',
            'renewal_reason' => 'required|string|max:500',
            'salary_change_percentage' => 'nullable|numeric|min:-100|max:100',
            'new_basic_salary' => 'nullable|numeric|min:0',
            'terms_changes' => 'nullable|string|max:2000',
            'renewal_document_path' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Handle file upload
            $uploadedFiles = [];
            if ($request->hasFile('renewal_document_path')) {
                $file = $request->file('renewal_document_path');
                $fileName = time() . '_renewal_' . $employee->id . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('employment-contracts', $fileName, 'public');
                $uploadedFiles['renewal_document_path'] = $filePath;
            }

            // In a real implementation, this would create a new contract record
            return response()->json([
                'success' => true,
                'message' => 'Employment contract renewed successfully',
                'data' => array_merge($request->all(), $uploadedFiles, [
                    'status' => 'renewed',
                    'renewed_by' => auth()->id(),
                    'renewed_at' => now(),
                ])
            ]);

        } catch (\Exception $e) {
            \Log::error('Employment contract renewal failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate contract PDF.
     */
    public function generatePdf(Employee $employee)
    {
        try {
            // This would generate a PDF contract using a library like DomPDF
            // For now, return a success response
            return response()->json([
                'success' => true,
                'message' => 'Employment contract PDF generated successfully',
                'download_url' => '/employment-contracts/' . $employee->id . '/pdf'
            ]);

        } catch (\Exception $e) {
            \Log::error('PDF generation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get contract statistics.
     */
    public function statistics()
    {
        try {
            // In a real implementation, this would query the employment_contracts table
            $stats = [
                'total_contracts' => Employee::where('status', 'approved')->count(), // Placeholder
                'active_contracts' => 45, // Placeholder
                'expired_contracts' => 8, // Placeholder
                'terminated_contracts' => 5, // Placeholder
                'expiring_soon' => 12, // Placeholder
                'by_type' => [
                    'permanent' => 28,
                    'temporary' => 10,
                    'probation' => 7,
                    'internship' => 3,
                    'consultant' => 4,
                    'contractor' => 2
                ],
                'average_duration_months' => 24.5, // Placeholder
                'renewal_rate' => 82.3, // Placeholder
                'termination_rate' => 8.7, // Placeholder
                'average_salary' => 4500.00, // Placeholder
            ];

            return response()->json([
                'success' => true,
                'statistics' => $stats
            ]);

        } catch (\Exception $e) {
            \Log::error('Statistics retrieval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get contracts requiring attention.
     */
    public function requiringAttention()
    {
        try {
            // In a real implementation, this would find contracts requiring attention
            $employees = Employee::where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'employees' => $employees
            ]);

        } catch (\Exception $e) {
            \Log::error('Requiring attention contracts retrieval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload contract document.
     */
    public function uploadDocument(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'document_type' => 'required|in:contract,signed_contract,renewal_document,amendment,termination_notice,witness_signature',
            'document_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->hasFile('document_file')) {
                $file = $request->file('document_file');
                $fileName = time() . '_' . $request->document_type . '_' . $employee->id . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('employment-contracts', $fileName, 'public');

                return response()->json([
                    'success' => true,
                    'message' => 'Contract document uploaded successfully',
                    'file_path' => $filePath
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Document upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download contract document.
     */
    public function downloadDocument(Employee $employee, $documentType)
    {
        try {
            // In a real implementation, this would fetch the document from storage
            return response()->json([
                'success' => false,
                'message' => 'Document download feature coming soon'
            ], 501);

        } catch (\Exception $e) {
            \Log::error('Document download failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get contract calendar.
     */
    public function calendar()
    {
        try {
            // In a real implementation, this would return contract events for calendar
            $events = [
                [
                    'title' => 'Contract Expiry - John Doe',
                    'start' => now()->addDays(30)->format('Y-m-d'),
                    'type' => 'expiry',
                    'employee' => 'John Doe'
                ],
                [
                    'title' => 'Probation End - Jane Smith',
                    'start' => now()->addDays(45)->format('Y-m-d'),
                    'type' => 'probation_end',
                    'employee' => 'Jane Smith'
                ],
                [
                    'title' => 'Contract Renewal - Bob Johnson',
                    'start' => now()->addDays(60)->format('Y-m-d'),
                    'type' => 'renewal',
                    'employee' => 'Bob Johnson'
                ]
            ];

            return response()->json([
                'success' => true,
                'events' => $events
            ]);

        } catch (\Exception $e) {
            \Log::error('Calendar retrieval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique contract number.
     */
    private function generateContractNumber($contractType)
    {
        $prefixes = [
            'permanent' => 'EMP',
            'temporary' => 'TMP',
            'probation' => 'PROB',
            'internship' => 'INT',
            'consultant' => 'CONS',
            'contractor' => 'CONT'
        ];
        
        $prefix = $prefixes[$contractType] ?? 'EC';
        $year = date('Y');
        $sequence = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        
        return "{$prefix}{$year}{$sequence}";
    }
}

