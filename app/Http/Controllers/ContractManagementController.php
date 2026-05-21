<?php

namespace App\Http\Controllers;

use App\Models\EmployeeRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContractManagementController extends Controller
{
    /**
     * Display the contract management dashboard.
     */
    public function index()
    {
        $employees = EmployeeRegistration::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('hris.contract-management.index', compact('employees'));
    }

    /**
     * Display contracts for a specific employee.
     */
    public function employeeContracts(EmployeeRegistration $employee)
    {
        // In a real implementation, this would fetch contracts from database
        // For now, we'll pass the employee and show a placeholder view
        return view('hris.contract-management.employee-contracts', compact('employee'));
    }

    /**
     * Store contract management data for an employee.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_registration_id' => 'required|exists:employee_registrations,id',
            'contract_type' => 'required|in:permanent,temporary,probation,internship,consultant,contractor',
            'contract_number' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'probation_period_months' => 'nullable|integer|min:1|max:12',
            'job_title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'work_location' => 'required|string|max:255',
            'reporting_to' => 'required|string|max:255',
            'working_hours' => 'required|string|max:100',
            'salary_currency' => 'required|string|max:3',
            'gross_salary' => 'required|numeric|min:0',
            'net_salary' => 'nullable|numeric|min:0',
            'payment_frequency' => 'required|in:weekly,biweekly,monthly,quarterly,annually',
            'benefits' => 'nullable|string|max:2000',
            'allowances' => 'nullable|string|max:2000',
            'leave_entitlement_days' => 'required|integer|min:0|max:365',
            'notice_period_days' => 'required|integer|min:1|max:365',
            'confidentiality_clause' => 'required|boolean',
            'non_compete_clause' => 'required|boolean',
            'non_compete_duration_months' => 'nullable|integer|min:1|max:60',
            'termination_conditions' => 'nullable|string|max:2000',
            'renewal_terms' => 'nullable|string|max:2000',
            'contract_file_path' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'signed_contract_path' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
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
                'contract_file_path' => 'contract_file',
                'signed_contract_path' => 'signed_contract'
            ];

            foreach ($fileFields as $dbField => $fileField) {
                if ($request->hasFile($fileField)) {
                    $file = $request->file($fileField);
                    $fileName = time() . '_' . $fileField . '_' . $request->employee_registration_id . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('contracts', $fileName, 'public');
                    $uploadedFiles[$dbField] = $filePath;
                }
            }

            // Simulate creating contract management record
            $contractData = array_merge($request->all(), $uploadedFiles, [
                'created_by' => auth()->id(),
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contract created successfully',
                'data' => $contractData
            ]);

        } catch (\Exception $e) {
            \Log::error('Contract creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update contract management data for an employee.
     */
    public function update(Request $request, EmployeeRegistration $employee)
    {
        $validator = Validator::make($request->all(), [
            'contract_type' => 'required|in:permanent,temporary,probation,internship,consultant,contractor',
            'contract_number' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'probation_period_months' => 'nullable|integer|min:1|max:12',
            'job_title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'work_location' => 'required|string|max:255',
            'reporting_to' => 'required|string|max:255',
            'working_hours' => 'required|string|max:100',
            'salary_currency' => 'required|string|max:3',
            'gross_salary' => 'required|numeric|min:0',
            'net_salary' => 'nullable|numeric|min:0',
            'payment_frequency' => 'required|in:weekly,biweekly,monthly,quarterly,annually',
            'benefits' => 'nullable|string|max:2000',
            'allowances' => 'nullable|string|max:2000',
            'leave_entitlement_days' => 'required|integer|min:0|max:365',
            'notice_period_days' => 'required|integer|min:1|max:365',
            'confidentiality_clause' => 'required|boolean',
            'non_compete_clause' => 'required|boolean',
            'non_compete_duration_months' => 'nullable|integer|min:1|max:60',
            'termination_conditions' => 'nullable|string|max:2000',
            'renewal_terms' => 'nullable|string|max:2000',
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
            // In a real implementation, this would update the contracts table
            $contractData = array_merge($request->all(), [
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contract updated successfully',
                'data' => $contractData
            ]);

        } catch (\Exception $e) {
            \Log::error('Contract update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activate contract.
     */
    public function activate(EmployeeRegistration $employee)
    {
        try {
            // In a real implementation, this would update the contract status
            return response()->json([
                'success' => true,
                'message' => 'Contract activated successfully',
                'data' => [
                    'status' => 'active',
                    'activated_by' => auth()->id(),
                    'activated_at' => now(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Contract activation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Terminate contract.
     */
    public function terminate(Request $request, EmployeeRegistration $employee)
    {
        $validator = Validator::make($request->all(), [
            'termination_date' => 'required|date|before_or_equal:today',
            'termination_reason' => 'required|string|max:500',
            'termination_type' => 'required|in:resignation,dismissal,retirement,contract_expiry,mutual_agreement',
            'final_settlement_amount' => 'nullable|numeric|min:0',
            'handover_completed' => 'required|boolean',
            'clearance_completed' => 'required|boolean',
            'exit_interview_completed' => 'required|boolean',
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
                'message' => 'Contract terminated successfully',
                'data' => array_merge($request->all(), [
                    'status' => 'terminated',
                    'terminated_by' => auth()->id(),
                    'terminated_at' => now(),
                ])
            ]);

        } catch (\Exception $e) {
            \Log::error('Contract termination failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Renew contract.
     */
    public function renew(Request $request, EmployeeRegistration $employee)
    {
        $validator = Validator::make($request->all(), [
            'new_start_date' => 'required|date',
            'new_end_date' => 'nullable|date|after:new_start_date',
            'renewal_reason' => 'required|string|max:500',
            'salary_change' => 'nullable|numeric',
            'new_gross_salary' => 'nullable|numeric|min:0',
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
                $filePath = $file->storeAs('contracts', $fileName, 'public');
                $uploadedFiles['renewal_document_path'] = $filePath;
            }

            // In a real implementation, this would create a new contract record
            return response()->json([
                'success' => true,
                'message' => 'Contract renewed successfully',
                'data' => array_merge($request->all(), $uploadedFiles, [
                    'status' => 'renewed',
                    'renewed_by' => auth()->id(),
                    'renewed_at' => now(),
                ])
            ]);

        } catch (\Exception $e) {
            \Log::error('Contract renewal failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate contract report.
     */
    public function generateReport(EmployeeRegistration $employee)
    {
        try {
            // This would generate a PDF report using a library like DomPDF
            // For now, return a success response
            return response()->json([
                'success' => true,
                'message' => 'Contract report generated successfully',
                'download_url' => '/contract-management/' . $employee->id . '/report'
            ]);

        } catch (\Exception $e) {
            \Log::error('Report generation failed: ' . $e->getMessage());
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
            // In a real implementation, this would query the contracts table
            $stats = [
                'total_contracts' => EmployeeRegistration::where('status', 'approved')->count(), // Placeholder
                'active_contracts' => 42, // Placeholder
                'expired_contracts' => 8, // Placeholder
                'terminated_contracts' => 3, // Placeholder
                'expiring_soon' => 12, // Placeholder
                'by_type' => [
                    'permanent' => 25,
                    'temporary' => 12,
                    'probation' => 8,
                    'internship' => 5,
                    'consultant' => 3,
                    'contractor' => 2
                ],
                'average_duration_months' => 18.5, // Placeholder
                'renewal_rate' => 75.2, // Placeholder
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
            $employees = EmployeeRegistration::where('status', 'approved')
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
    public function uploadDocument(Request $request, EmployeeRegistration $employee)
    {
        $validator = Validator::make($request->all(), [
            'document_type' => 'required|in:contract,signed_contract,renewal_document,amendment,termination_notice',
            'document_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
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
                $filePath = $file->storeAs('contracts', $fileName, 'public');

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
    public function downloadDocument(EmployeeRegistration $employee, $documentType)
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
                    'start' => now()->addDays(15)->format('Y-m-d'),
                    'type' => 'expiry',
                    'employee' => 'John Doe'
                ],
                [
                    'title' => 'Probation End - Jane Smith',
                    'start' => now()->addDays(30)->format('Y-m-d'),
                    'type' => 'probation_end',
                    'employee' => 'Jane Smith'
                ],
                [
                    'title' => 'Contract Renewal - Bob Johnson',
                    'start' => now()->addDays(45)->format('Y-m-d'),
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
}
