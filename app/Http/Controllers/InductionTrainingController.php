<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InductionTrainingController extends Controller
{
    /**
     * Display the induction training dashboard.
     */
    public function index()
    {
        $employees = Employee::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('hris.induction-training.index', compact('employees'));
    }

    /**
     * Display training records for a specific employee.
     */
    public function employeeTraining(Employee $employee)
    {
        // In a real implementation, this would fetch training records from database
        // For now, we'll pass the employee and show a placeholder view
        return view('hris.induction-training.employee-training', compact('employee'));
    }

    /**
     * Store induction training records for an employee.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_registration_id' => 'required|exists:employee_registrations,id',
            'training_date' => 'required|date|before_or_equal:today',
            'training_type' => 'required|in:company_policies,safety_procedures,job_specific,compliance,other',
            'training_title' => 'required|string|max:255',
            'training_description' => 'required|string|max:2000',
            'trainer_name' => 'required|string|max:255',
            'training_duration_hours' => 'required|numeric|min:0.5|max:40',
            'training_materials_path' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
            'completion_certificate_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'assessment_score' => 'nullable|numeric|min:0|max:100',
            'assessment_passed' => 'required|boolean',
            'feedback_comments' => 'nullable|string|max:1000',
            'next_training_date' => 'nullable|date|after:training_date',
            'status' => 'required|in:completed,incomplete,scheduled',
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
            // In a real implementation, this would save to an induction_training table
            // For now, we'll simulate the process and return success
            
            // Handle file uploads
            $uploadedFiles = [];
            $fileFields = [
                'training_materials_path' => 'training_materials',
                'completion_certificate_path' => 'completion_certificate'
            ];

            foreach ($fileFields as $dbField => $fileField) {
                if ($request->hasFile($fileField)) {
                    $file = $request->file($fileField);
                    $fileName = time() . '_' . $fileField . '_' . $request->employee_registration_id . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('induction-training', $fileName, 'public');
                    $uploadedFiles[$dbField] = $filePath;
                }
            }

            // Simulate creating induction training record
            $trainingData = array_merge($request->all(), $uploadedFiles, [
                'created_by' => auth()->id(),
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Induction training record created successfully',
                'data' => $trainingData
            ]);

        } catch (\Exception $e) {
            \Log::error('Induction training creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update induction training records for an employee.
     */
    public function update(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'training_date' => 'required|date|before_or_equal:today',
            'training_type' => 'required|in:company_policies,safety_procedures,job_specific,compliance,other',
            'training_title' => 'required|string|max:255',
            'training_description' => 'required|string|max:2000',
            'trainer_name' => 'required|string|max:255',
            'training_duration_hours' => 'required|numeric|min:0.5|max:40',
            'assessment_score' => 'nullable|numeric|min:0|max:100',
            'assessment_passed' => 'required|boolean',
            'feedback_comments' => 'nullable|string|max:1000',
            'next_training_date' => 'nullable|date|after:training_date',
            'status' => 'required|in:completed,incomplete,scheduled',
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
            // In a real implementation, this would update the induction_training table
            $trainingData = array_merge($request->all(), [
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Induction training record updated successfully',
                'data' => $trainingData
            ]);

        } catch (\Exception $e) {
            \Log::error('Induction training update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate training completion certificate.
     */
    public function generateCertificate(Employee $employee)
    {
        try {
            // This would generate a PDF certificate using a library like DomPDF
            // For now, return a success response
            return response()->json([
                'success' => true,
                'message' => 'Training certificate generated successfully',
                'download_url' => '/induction-training/' . $employee->id . '/certificate'
            ]);

        } catch (\Exception $e) {
            \Log::error('Certificate generation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get training statistics.
     */
    public function statistics()
    {
        try {
            // In a real implementation, this would query the induction_training table
            $stats = [
                'total_employees' => Employee::where('status', 'approved')->count(),
                'employees_trained' => Employee::where('status', 'approved')->count(), // Placeholder
                'training_completion_rate' => 85.5, // Placeholder
                'average_training_hours' => 8.2, // Placeholder
                'training_types' => [
                    'company_policies' => 45,
                    'safety_procedures' => 38,
                    'job_specific' => 52,
                    'compliance' => 41,
                    'other' => 12
                ],
                'upcoming_trainings' => 15, // Placeholder
                'overdue_trainings' => 3, // Placeholder
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
     * Get employees requiring training.
     */
    public function requiringTraining()
    {
        try {
            // In a real implementation, this would find employees requiring training
            $employees = Employee::where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'employees' => $employees
            ]);

        } catch (\Exception $e) {
            \Log::error('Requiring training employees retrieval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Schedule training for employees.
     */
    public function scheduleTraining(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employee_registrations,id',
            'training_type' => 'required|in:company_policies,safety_procedures,job_specific,compliance,other',
            'training_title' => 'required|string|max:255',
            'scheduled_date' => 'required|date|after:today',
            'trainer_name' => 'required|string|max:255',
            'estimated_duration_hours' => 'required|numeric|min:0.5|max:40',
            'location' => 'required|string|max:255',
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
            // In a real implementation, this would create scheduled training records
            $scheduledData = array_merge($request->all(), [
                'scheduled_by' => auth()->id(),
                'scheduled_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Training scheduled successfully for ' . count($request->employee_ids) . ' employees',
                'data' => $scheduledData
            ]);

        } catch (\Exception $e) {
            \Log::error('Training scheduling failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload training materials.
     */
    public function uploadMaterials(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'materials_file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,zip|max:20480',
            'materials_description' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->hasFile('materials_file')) {
                $file = $request->file('materials_file');
                $fileName = time() . '_materials_' . $employee->id . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('induction-training-materials', $fileName, 'public');

                return response()->json([
                    'success' => true,
                    'message' => 'Training materials uploaded successfully',
                    'file_path' => $filePath
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Materials upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download training materials.
     */
    public function downloadMaterials(Employee $employee, $materialId)
    {
        try {
            // In a real implementation, this would fetch the materials from storage
            return response()->json([
                'success' => false,
                'message' => 'Materials download feature coming soon'
            ], 501);

        } catch (\Exception $e) {
            \Log::error('Materials download failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get training calendar.
     */
    public function calendar()
    {
        try {
            // In a real implementation, this would return training events for calendar
            $events = [
                [
                    'title' => 'Company Policies Training',
                    'start' => now()->addDays(5)->format('Y-m-d'),
                    'type' => 'company_policies',
                    'employees' => 12
                ],
                [
                    'title' => 'Safety Procedures Workshop',
                    'start' => now()->addDays(10)->format('Y-m-d'),
                    'type' => 'safety_procedures',
                    'employees' => 8
                ],
                [
                    'title' => 'Compliance Training',
                    'start' => now()->addDays(15)->format('Y-m-d'),
                    'type' => 'compliance',
                    'employees' => 15
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
