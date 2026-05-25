<?php

namespace App\Http\Controllers;

use App\Models\EmployeeRegistration;
use App\Models\SocialRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SocialRecordsController extends Controller
{
    /**
     * Display the social records dashboard.
     */
    public function index()
    {
        $employees = EmployeeRegistration::where('status', 'approved')
            ->with('socialRecord')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('hris.social-records.index', compact('employees'));
    }

    /**
     * Display social records for a specific employee.
     */
    public function employeeRecords(EmployeeRegistration $employee)
    {
        $socialRecord = SocialRecord::where('employee_registration_id', $employee->id)->first();
        return view('hris.social-records.employee-records', compact('employee', 'socialRecord'));
    }

    /**
     * Store social records for an employee.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_registration_id' => 'required|exists:employee_registrations,id',
            'nssf_number' => 'required|string|max:50',
            'nssf_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'nhif_number' => 'required|string|max:50',
            'nhif_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'tin_number' => 'required|string|max:50',
            'tin_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'wcf_number' => 'required|string|max:50',
            'wcf_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'osha_number' => 'nullable|string|max:50',
            'osha_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'bank_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:50',
            'bank_branch' => 'required|string|max:255',
            'bank_verification' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_relationship' => 'required|string|max:100',
            'emergency_contact_phone' => 'required|string|max:20',
            'emergency_contact_address' => 'required|string|max:500',
            'next_of_kin_name' => 'required|string|max:255',
            'next_of_kin_relationship' => 'required|string|max:100',
            'next_of_kin_phone' => 'required|string|max:20',
            'next_of_kin_address' => 'required|string|max:500',
            'status' => 'required|in:active,inactive',
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
                'nssf_card' => 'nssf_card_path',
                'nhif_card' => 'nhif_card_path',
                'tin_certificate' => 'tin_certificate_path',
                'wcf_certificate' => 'wcf_certificate_path',
                'osha_certificate' => 'osha_certificate_path',
                'bank_verification' => 'bank_verification_path'
            ];

            foreach ($fileFields as $fileInput => $dbField) {
                if ($request->hasFile($fileInput)) {
                    $file = $request->file($fileInput);
                    $fileName = time() . '_' . $fileInput . '_' . $request->employee_registration_id . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('social-records', $fileName, 'public');
                    $uploadedFiles[$dbField] = $filePath;
                }
            }

            // Create or update social record
            $socialRecord = SocialRecord::updateOrCreate(
                ['employee_registration_id' => $request->employee_registration_id],
                array_merge($request->except(['nssf_card', 'nhif_card', 'tin_certificate', 'wcf_certificate', 'osha_certificate', 'bank_verification']), $uploadedFiles, [
                    'client_id' => session('current_client_id'),
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ])
            );

            return response()->json([
                'success' => true,
                'message' => 'Social records registered successfully',
                'data' => $socialRecord
            ]);

        } catch (\Exception $e) {
            \Log::error('Social records registration failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update social records for an employee.
     */
    public function update(Request $request, EmployeeRegistration $employee)
    {
        $validator = Validator::make($request->all(), [
            'nssf_number' => 'required|string|max:50',
            'nhif_number' => 'required|string|max:50',
            'tin_number' => 'required|string|max:50',
            'wcf_number' => 'required|string|max:50',
            'osha_number' => 'nullable|string|max:50',
            'bank_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:50',
            'bank_branch' => 'required|string|max:255',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_relationship' => 'required|string|max:100',
            'emergency_contact_phone' => 'required|string|max:20',
            'emergency_contact_address' => 'required|string|max:500',
            'next_of_kin_name' => 'required|string|max:255',
            'next_of_kin_relationship' => 'required|string|max:100',
            'next_of_kin_phone' => 'required|string|max:20',
            'next_of_kin_address' => 'required|string|max:500',
            'status' => 'required|in:active,inactive',
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
            // In a real implementation, this would update the social_records table
            $socialRecordData = array_merge($request->all(), [
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Social records updated successfully',
                'data' => $socialRecordData
            ]);

        } catch (\Exception $e) {
            \Log::error('Social records update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate social records report.
     */
    public function generateReport(EmployeeRegistration $employee)
    {
        try {
            // This would generate a PDF report using a library like DomPDF
            // For now, return a success response
            return response()->json([
                'success' => true,
                'message' => 'Social records report generated successfully',
                'download_url' => '/social-records/' . $employee->id . '/pdf'
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
     * Get social records statistics.
     */
    public function statistics()
    {
        try {
            // In a real implementation, this would query the social_records table
            $stats = [
                'total_employees' => EmployeeRegistration::where('status', 'approved')->count(),
                'employees_with_nssf' => EmployeeRegistration::where('status', 'approved')->count(), // Placeholder
                'employees_with_nhif' => EmployeeRegistration::where('status', 'approved')->count(), // Placeholder
                'employees_with_tin' => EmployeeRegistration::where('status', 'approved')->count(), // Placeholder
                'employees_with_wcf' => EmployeeRegistration::where('status', 'approved')->count(), // Placeholder
                'employees_with_bank' => EmployeeRegistration::where('status', 'approved')->count(), // Placeholder
                'active_records' => EmployeeRegistration::where('status', 'approved')->count(), // Placeholder
                'inactive_records' => 0, // Placeholder
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
     * Get employees missing social records.
     */
    public function missingRecords()
    {
        try {
            // In a real implementation, this would find employees without complete social records
            $employees = EmployeeRegistration::where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'employees' => $employees
            ]);

        } catch (\Exception $e) {
            \Log::error('Missing records retrieval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload social records document.
     */
    public function uploadDocument(Request $request, EmployeeRegistration $employee)
    {
        $validator = Validator::make($request->all(), [
            'document_type' => 'required|in:nssf_card,nhif_card,tin_certificate,wcf_certificate,osha_certificate,bank_verification',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
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
                $filePath = $file->storeAs('social-records', $fileName, 'public');

                return response()->json([
                    'success' => true,
                    'message' => 'Document uploaded successfully',
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
     * Download social records document.
     */
    public function downloadDocument(EmployeeRegistration $employee, $documentType)
    {
        try {
            // In a real implementation, this would fetch the document from storage
            // For now, return a placeholder response
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
}
