<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDocument;
use App\Models\EmployeeRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeDocumentController extends Controller
{
    /**
     * Display the document management dashboard.
     */
    public function index()
    {
        $documents = EmployeeDocument::with(['employee', 'uploader', 'verifier'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $employees = EmployeeRegistration::where('status', 'approved')->get();
        
        return view('hris.employee-document.index', compact('documents', 'employees'));
    }

    /**
     * Display documents for a specific employee.
     */
    public function employeeDocuments(EmployeeRegistration $employee)
    {
        $documents = EmployeeDocument::where('employee_registration_id', $employee->id)
            ->with(['uploader', 'verifier'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('hris.employee-document.employee-documents', compact('employee', 'documents'));
    }

    /**
     * Store a newly uploaded document.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_registration_id' => 'required|exists:employee_registrations,id',
            'document_type' => 'required|in:national_id,passport,birth_certificate,academic_certificate,professional_certificate,medical_certificate,police_clearance,reference_letter,resume_cv,contract,other',
            'document_name' => 'required|string|max:255',
            'document_number' => 'nullable|string|max:255',
            'issuing_authority' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'document_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'notes' => 'nullable|string|max:1000',
            'is_required' => 'boolean',
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
            if ($request->hasFile('document_file')) {
                $file = $request->file('document_file');
                $fileName = time() . '_' . Str::slug($request->document_name) . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('employee-documents', $fileName, 'public');
                
                $document = EmployeeDocument::create([
                    'employee_registration_id' => $request->employee_registration_id,
                    'document_type' => $request->document_type,
                    'document_name' => $request->document_name,
                    'document_number' => $request->document_number,
                    'issuing_authority' => $request->issuing_authority,
                    'issue_date' => $request->issue_date,
                    'expiry_date' => $request->expiry_date,
                    'document_path' => $filePath,
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getMimeType(),
                    'status' => 'uploaded',
                    'uploaded_by' => auth()->id(),
                    'notes' => $request->notes,
                    'is_required' => $request->boolean('is_required', false),
                    'is_active' => true,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Document uploaded successfully',
                    'document' => $document
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
     * Display the specified document.
     */
    public function show(EmployeeDocument $employeeDocument)
    {
        $employeeDocument->load(['employee', 'uploader', 'verifier']);
        return view('hris.employee-document.show', compact('employeeDocument'));
    }

    /**
     * Update the specified document.
     */
    public function update(Request $request, EmployeeDocument $employeeDocument)
    {
        $validator = Validator::make($request->all(), [
            'document_name' => 'required|string|max:255',
            'document_number' => 'nullable|string|max:255',
            'issuing_authority' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'notes' => 'nullable|string|max:1000',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $employeeDocument->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Document updated successfully',
                'document' => $employeeDocument
            ]);

        } catch (\Exception $e) {
            \Log::error('Document update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify the specified document.
     */
    public function verify(EmployeeDocument $employeeDocument)
    {
        if (!$employeeDocument->canBeVerified()) {
            return response()->json([
                'success' => false,
                'message' => 'Document cannot be verified in its current status'
            ], 400);
        }

        try {
            $employeeDocument->verify(auth()->user());

            return response()->json([
                'success' => true,
                'message' => 'Document verified successfully',
                'document' => $employeeDocument
            ]);

        } catch (\Exception $e) {
            \Log::error('Document verification failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject the specified document.
     */
    public function reject(Request $request, EmployeeDocument $employeeDocument)
    {
        $reason = $request->input('reason');

        if (!$reason) {
            return response()->json([
                'success' => false,
                'message' => 'Rejection reason is required'
            ], 400);
        }

        if (!$employeeDocument->canBeVerified()) {
            return response()->json([
                'success' => false,
                'message' => 'Document cannot be rejected in its current status'
            ], 400);
        }

        try {
            $employeeDocument->reject(auth()->user(), $reason);

            return response()->json([
                'success' => true,
                'message' => 'Document rejected successfully',
                'document' => $employeeDocument
            ]);

        } catch (\Exception $e) {
            \Log::error('Document rejection failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified document.
     */
    public function destroy(EmployeeDocument $employeeDocument)
    {
        try {
            // Delete file from storage
            if ($employeeDocument->document_path && Storage::disk('public')->exists($employeeDocument->document_path)) {
                Storage::disk('public')->delete($employeeDocument->document_path);
            }

            $employeeDocument->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Document deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download the specified document.
     */
    public function download(EmployeeDocument $employeeDocument)
    {
        try {
            if (!$employeeDocument->document_path || !Storage::disk('public')->exists($employeeDocument->document_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            $filePath = Storage::disk('public')->path($employeeDocument->document_path);
            $fileName = $employeeDocument->document_name . '.' . pathinfo($filePath, PATHINFO_EXTENSION);

            return response()->download($filePath, $fileName);

        } catch (\Exception $e) {
            \Log::error('Document download failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get document statistics.
     */
    public function statistics()
    {
        try {
            $stats = [
                'total_documents' => EmployeeDocument::count(),
                'verified_documents' => EmployeeDocument::where('status', 'verified')->count(),
                'pending_verification' => EmployeeDocument::where('status', 'pending_verification')->count(),
                'expired_documents' => EmployeeDocument::expired()->count(),
                'expiring_soon' => EmployeeDocument::expiringSoon()->count(),
                'by_type' => EmployeeDocument::selectRaw('document_type, COUNT(*) as count')
                    ->groupBy('document_type')
                    ->pluck('count', 'document_type')
                    ->toArray(),
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
     * Get documents requiring attention.
     */
    public function requiringAttention()
    {
        try {
            $documents = EmployeeDocument::with(['employee'])
                ->where(function($query) {
                    $query->where('status', 'pending_verification')
                          ->orWhere('status', 'uploaded')
                          ->orWhere(function($subQuery) {
                              $subQuery->where('expiry_date', '<', now()->addDays(30))
                                      ->where('expiry_date', '>', now());
                          });
                })
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'documents' => $documents
            ]);

        } catch (\Exception $e) {
            \Log::error('Requiring attention documents retrieval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }
}
