<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PersonnelIdController extends Controller
{
    /**
     * Display the personnel ID applications dashboard.
     */
    public function index()
    {
        $employees = Employee::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('hris.personnel-id.index', compact('employees'));
    }

    /**
     * Display ID application for a specific employee.
     */
    public function employeeId(Employee $employee)
    {
        // In a real implementation, this would fetch ID application from database
        // For now, we'll pass the employee and show a placeholder view
        return view('hris.personnel-id.employee-id', compact('employee'));
    }

    /**
     * Store personnel ID application for an employee.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_registration_id' => 'required|exists:employee_registrations,id',
            'id_type' => 'required|in:employee_card,access_card,visitor_card,contractor_card',
            'id_purpose' => 'required|string|max:255',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'access_areas' => 'nullable|string|max:1000',
            'special_permissions' => 'nullable|string|max:1000',
            'photo_path' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'signature_path' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'fingerprint_path' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'emergency_access' => 'required|boolean',
            'after_hours_access' => 'required|boolean',
            'status' => 'required|in:pending,approved,rejected,issued,expired,lost,damaged',
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
            // In a real implementation, this would save to a personnel_id_applications table
            // For now, we'll simulate the process and return success
            
            // Handle file uploads
            $uploadedFiles = [];
            $fileFields = [
                'photo_path' => 'photo',
                'signature_path' => 'signature',
                'fingerprint_path' => 'fingerprint'
            ];

            foreach ($fileFields as $dbField => $fileField) {
                if ($request->hasFile($fileField)) {
                    $file = $request->file($fileField);
                    $fileName = time() . '_' . $fileField . '_' . $request->employee_registration_id . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('personnel-id', $fileName, 'public');
                    $uploadedFiles[$dbField] = $filePath;
                }
            }

            // Generate unique ID number
            $idNumber = $this->generateIdNumber($request->id_type);

            // Simulate creating personnel ID application
            $idData = array_merge($request->all(), $uploadedFiles, [
                'id_number' => $idNumber,
                'created_by' => auth()->id(),
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Personnel ID application submitted successfully',
                'data' => $idData,
                'id_number' => $idNumber
            ]);

        } catch (\Exception $e) {
            \Log::error('Personnel ID application failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update personnel ID application for an employee.
     */
    public function update(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'id_type' => 'required|in:employee_card,access_card,visitor_card,contractor_card',
            'id_purpose' => 'required|string|max:255',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'access_areas' => 'nullable|string|max:1000',
            'special_permissions' => 'nullable|string|max:1000',
            'emergency_access' => 'required|boolean',
            'after_hours_access' => 'required|boolean',
            'status' => 'required|in:pending,approved,rejected,issued,expired,lost,damaged',
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
            // In a real implementation, this would update the personnel_id_applications table
            $idData = array_merge($request->all(), [
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Personnel ID application updated successfully',
                'data' => $idData
            ]);

        } catch (\Exception $e) {
            \Log::error('Personnel ID update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve personnel ID application.
     */
    public function approve(Employee $employee)
    {
        try {
            // In a real implementation, this would update the status in the database
            return response()->json([
                'success' => true,
                'message' => 'Personnel ID application approved successfully',
                'data' => [
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Personnel ID approval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject personnel ID application.
     */
    public function reject(Request $request, Employee $employee)
    {
        $reason = $request->input('reason');

        if (!$reason) {
            return response()->json([
                'success' => false,
                'message' => 'Rejection reason is required'
            ], 400);
        }

        try {
            // In a real implementation, this would update the status and add rejection reason
            return response()->json([
                'success' => true,
                'message' => 'Personnel ID application rejected successfully',
                'data' => [
                    'status' => 'rejected',
                    'rejection_reason' => $reason,
                    'rejected_by' => auth()->id(),
                    'rejected_at' => now(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Personnel ID rejection failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Issue personnel ID.
     */
    public function issue(Employee $employee)
    {
        try {
            // In a real implementation, this would update the status and issue date
            return response()->json([
                'success' => true,
                'message' => 'Personnel ID issued successfully',
                'data' => [
                    'status' => 'issued',
                    'issued_by' => auth()->id(),
                    'issued_at' => now(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Personnel ID issuance failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Report lost personnel ID.
     */
    public function reportLost(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'lost_date' => 'required|date|before_or_equal:today',
            'lost_location' => 'required|string|max:255',
            'circumstances' => 'required|string|max:1000',
            'police_report_filed' => 'required|boolean',
            'police_report_number' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // In a real implementation, this would update the status and add loss details
            return response()->json([
                'success' => true,
                'message' => 'Personnel ID reported as lost successfully',
                'data' => array_merge($request->all(), [
                    'status' => 'lost',
                    'reported_by' => auth()->id(),
                    'reported_at' => now(),
                ])
            ]);

        } catch (\Exception $e) {
            \Log::error('Personnel ID loss reporting failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate personnel ID card.
     */
    public function generateCard(Employee $employee)
    {
        try {
            // This would generate a PDF ID card using a library like DomPDF
            // For now, return a success response
            return response()->json([
                'success' => true,
                'message' => 'Personnel ID card generated successfully',
                'download_url' => '/personnel-id/' . $employee->id . '/card'
            ]);

        } catch (\Exception $e) {
            \Log::error('ID card generation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ID statistics.
     */
    public function statistics()
    {
        try {
            // In a real implementation, this would query the personnel_id_applications table
            $stats = [
                'total_applications' => Employee::where('status', 'approved')->count(), // Placeholder
                'pending_applications' => 5, // Placeholder
                'approved_applications' => 45, // Placeholder
                'issued_cards' => 42, // Placeholder
                'expired_cards' => 3, // Placeholder
                'lost_cards' => 2, // Placeholder
                'damaged_cards' => 1, // Placeholder
                'by_type' => [
                    'employee_card' => 35,
                    'access_card' => 12,
                    'visitor_card' => 5,
                    'contractor_card' => 3
                ],
                'expiring_soon' => 8, // Placeholder
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
     * Get applications requiring attention.
     */
    public function requiringAttention()
    {
        try {
            // In a real implementation, this would find applications requiring attention
            $employees = Employee::where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'employees' => $employees
            ]);

        } catch (\Exception $e) {
            \Log::error('Requiring attention applications retrieval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload ID photo.
     */
    public function uploadPhoto(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'photo_file' => 'required|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->hasFile('photo_file')) {
                $file = $request->file('photo_file');
                $fileName = time() . '_photo_' . $employee->id . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('personnel-id', $fileName, 'public');

                return response()->json([
                    'success' => true,
                    'message' => 'Photo uploaded successfully',
                    'file_path' => $filePath
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Photo upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique ID number.
     */
    private function generateIdNumber($idType)
    {
        $prefixes = [
            'employee_card' => 'EMP',
            'access_card' => 'ACC',
            'visitor_card' => 'VIS',
            'contractor_card' => 'CON'
        ];
        
        $prefix = $prefixes[$idType] ?? 'ID';
        $year = date('Y');
        $sequence = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        
        return "{$prefix}{$year}{$sequence}";
    }
}
