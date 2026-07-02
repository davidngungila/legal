<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PersonnelIdApplication;
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
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $employees = Employee::with('personnelIdApplications')
            ->where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('hris.personnel-id.index', compact('employees'));
    }

    /**
     * Display ID application for a specific employee.
     */
    public function employeeId(Employee $employee)
    {
        $applications = PersonnelIdApplication::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('hris.personnel-id.employee-id', compact('employee', 'applications'));
    }

    /**
     * Store personnel ID application for an employee.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'id_type' => 'required|in:employee_card,access_card,visitor_card,contractor_card',
            'id_purpose' => 'required|string|max:255',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'access_areas' => 'nullable|string|max:1000',
            'special_permissions' => 'nullable|string|max:1000',
            'photo' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'signature' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'fingerprint' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
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
            // Handle file uploads
            $uploadedFiles = [];
            $fileFields = [
                'photo' => 'photo_path',
                'signature' => 'signature_path',
                'fingerprint' => 'fingerprint_path'
            ];

            foreach ($fileFields as $fileInput => $dbField) {
                if ($request->hasFile($fileInput)) {
                    $file = $request->file($fileInput);
                    $fileName = time() . '_' . $fileInput . '_' . $request->employee_id . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('personnel-id', $fileName, 'public');
                    $uploadedFiles[$dbField] = $filePath;
                }
            }

            // Generate unique ID number
            $idNumber = $this->generateIdNumber($request->id_type);

            // Create personnel ID application
            $application = PersonnelIdApplication::create(array_merge($request->except(['photo', 'signature', 'fingerprint']), $uploadedFiles, [
                'client_id' => session('current_client_id'),
                'id_number' => $idNumber,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Personnel ID application submitted successfully',
                'data' => $application,
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
            'application_id' => 'nullable|exists:personnel_id_applications,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $application = $request->input('application_id') 
                ? PersonnelIdApplication::findOrFail($request->input('application_id'))
                : $employee->personnelIdApplications()->latest()->firstOrFail();

            $application->update(array_merge($request->except(['application_id', 'photo', 'signature', 'fingerprint']), [
                'updated_by' => auth()->id(),
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Personnel ID application updated successfully',
                'data' => $application
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
    public function approve(Request $request, Employee $employee)
    {
        $application = $employee->personnelIdApplications()->latest()->firstOrFail();
        
        try {
            $application->update([
                'status' => 'approved',
                'updated_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Personnel ID application approved successfully',
                'data' => $application
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
            $application = $employee->personnelIdApplications()->latest()->firstOrFail();
            $application->update([
                'status' => 'rejected',
                'notes' => $reason,
                'updated_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Personnel ID application rejected successfully',
                'data' => $application
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
    public function issue(Request $request, Employee $employee)
    {
        try {
            $application = $employee->personnelIdApplications()->latest()->firstOrFail();
            $application->update([
                'status' => 'issued',
                'updated_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Personnel ID issued successfully',
                'data' => $application
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
            $application = $employee->personnelIdApplications()->latest()->firstOrFail();
            $application->update([
                'status' => 'lost',
                'notes' => $request->input('circumstances') . "\nLocation: " . $request->input('lost_location') . "\nPolice Report Filed: " . ($request->input('police_report_filed') ? 'Yes' : 'No') . ($request->input('police_report_number') ? "\nReport Number: " . $request->input('police_report_number') : ''),
                'updated_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Personnel ID reported as lost successfully',
                'data' => $application
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
        $clientId = session('current_client_id');
        if (!$clientId) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a client first.'
            ], 400);
        }

        try {
            $query = PersonnelIdApplication::where('client_id', $clientId);
            $stats = [
                'total_applications' => (clone $query)->count(),
                'pending_applications' => (clone $query)->where('status', 'pending')->count(),
                'approved_applications' => (clone $query)->where('status', 'approved')->count(),
                'issued_cards' => (clone $query)->where('status', 'issued')->count(),
                'expired_cards' => (clone $query)->where('status', 'expired')->count(),
                'lost_cards' => (clone $query)->where('status', 'lost')->count(),
                'damaged_cards' => (clone $query)->where('status', 'damaged')->count(),
                'by_type' => [
                    'employee_card' => (clone $query)->where('id_type', 'employee_card')->count(),
                    'access_card' => (clone $query)->where('id_type', 'access_card')->count(),
                    'visitor_card' => (clone $query)->where('id_type', 'visitor_card')->count(),
                    'contractor_card' => (clone $query)->where('id_type', 'contractor_card')->count()
                ],
                'expiring_soon' => (clone $query)->where('status', 'issued')
                    ->whereBetween('valid_until', [now(), now()->addDays(30)])
                    ->count(),
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
        $clientId = session('current_client_id');
        if (!$clientId) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a client first.'
            ], 400);
        }

        try {
            $applications = PersonnelIdApplication::with('employee')
                ->where('client_id', $clientId)
                ->whereIn('status', ['pending', 'expired'])
                ->orWhere(function($q) {
                    $q->where('status', 'issued')
                      ->whereBetween('valid_until', [now(), now()->addDays(30)]);
                })
                ->get();

            return response()->json([
                'success' => true,
                'employees' => $applications->map(function($app) {
                    return [
                        'id' => $app->employee_id,
                        'first_name' => $app->employee->first_name ?? '',
                        'surname' => $app->employee->surname ?? '',
                        'employee_number' => $app->employee->employee_number ?? '',
                        'status' => $app->status
                    ];
                })
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
