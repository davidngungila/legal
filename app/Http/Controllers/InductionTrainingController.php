<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogger;
use App\Models\Employee;
use App\Models\InductionTraining;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class InductionTrainingController extends Controller
{
    /**
     * Display the induction training dashboard.
     */
    public function index()
    {
        $employees = Employee::where('status', 'active')
            ->with('inductionTrainings')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $allEmployees = Employee::where('status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('hris.induction-training.index', compact('employees', 'allEmployees'));
    }

    /**
     * Display training records for a specific employee.
     */
    public function employeeTraining(Employee $employee)
    {
        $trainings = InductionTraining::where('employee_id', $employee->id)
            ->orderBy('training_date', 'desc')
            ->get();

        return view('hris.induction-training.employee-training', compact('employee', 'trainings'));
    }

    /**
     * Store induction training records for an employee.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'training_date' => 'required|date',
            'training_type' => 'required|in:company_policies,safety_procedures,job_specific,compliance,other',
            'training_title' => 'required|string|max:255',
            'training_description' => 'required|string|max:2000',
            'trainer_name' => 'required|string|max:255',
            'training_duration_hours' => 'required|numeric|min:0.5|max:40',
            'training_materials' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
            'completion_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
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
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $uploadedFiles = $this->storeUploadedFiles($request);

            $training = InductionTraining::create(array_merge(
                $request->except(['training_materials', 'completion_certificate']),
                $uploadedFiles,
                [
                    'client_id' => session('current_client_id'),
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]
            ));

            AuditLogger::log(
                'induction_training.created',
                $training,
                'Induction Training',
                "Induction training '{$training->training_title}' recorded for employee #{$training->employee_id}",
                null,
                $training->toArray()
            );

            return response()->json([
                'success' => true,
                'message' => 'Induction training record created successfully',
                'data' => $training,
            ]);

        } catch (\Exception $e) {
            \Log::error('Induction training creation failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update induction training records for an employee.
     */
    public function update(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'training_id' => 'required|exists:induction_trainings,id',
            'training_date' => 'required|date',
            'training_type' => 'required|in:company_policies,safety_procedures,job_specific,compliance,other',
            'training_title' => 'required|string|max:255',
            'training_description' => 'required|string|max:2000',
            'trainer_name' => 'required|string|max:255',
            'training_duration_hours' => 'required|numeric|min:0.5|max:40',
            'training_materials' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
            'completion_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
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
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $training = InductionTraining::where('employee_id', $employee->id)
                ->where('id', $request->training_id)
                ->first();

            if (! $training) {
                return response()->json([
                    'success' => false,
                    'message' => 'Training record not found for this employee',
                ], 404);
            }

            $uploadedFiles = $this->storeUploadedFiles($request);

            $oldValues = $training->toArray();

            $training->update(array_merge(
                $request->except(['training_id', 'training_materials', 'completion_certificate']),
                $uploadedFiles,
                ['updated_by' => auth()->id()]
            ));

            AuditLogger::log(
                'induction_training.updated',
                $training,
                'Induction Training',
                "Induction training '{$training->training_title}' updated for employee #{$employee->id}",
                $oldValues,
                $training->toArray()
            );

            return response()->json([
                'success' => true,
                'message' => 'Induction training record updated successfully',
                'data' => $training,
            ]);

        } catch (\Exception $e) {
            \Log::error('Induction training update failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate training completion certificate (PDF download).
     */
    public function generateCertificate(Employee $employee)
    {
        try {
            $training = InductionTraining::where('employee_id', $employee->id)
                ->where('status', 'completed')
                ->orderBy('training_date', 'desc')
                ->first();

            if (! $training) {
                return response()->json([
                    'success' => false,
                    'message' => 'No completed training found for this employee',
                ], 404);
            }

            AuditLogger::log(
                'induction_training.certificate_generated',
                $training,
                'Induction Training',
                "Certificate generated for employee #{$employee->id} - {$training->training_title}",
                null,
                ['training_id' => $training->id]
            );

            $pdf = Pdf::loadView('hris.induction-training.certificate', [
                'employee' => $employee,
                'training' => $training,
                'clientName' => session('current_client') ? session('current_client')->name : 'Orvion HRIS',
            ]);

            return $pdf->download('certificate-'.$employee->employee_id.'.pdf');

        } catch (\Exception $e) {
            \Log::error('Certificate generation failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get training statistics.
     */
    public function statistics()
    {
        try {
            $clientId = session('current_client_id');
            if (! $clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a client first.',
                ], 400);
            }

            $trainings = InductionTraining::where('client_id', $clientId)->get();
            $activeEmployees = Employee::where('status', 'active')->get();

            $completedTrainings = $trainings->where('status', 'completed');
            $trainedEmployeeIds = $completedTrainings->pluck('employee_id')->unique();

            $totalEmployees = $activeEmployees->count();
            $employeesTrained = $activeEmployees->whereIn('id', $trainedEmployeeIds)->count();

            $completed = $completedTrainings->count();
            $total = $trainings->count();

            $stats = [
                'total_employees' => $totalEmployees,
                'employees_trained' => $employeesTrained,
                'training_completion_rate' => $total > 0 ? round($completed / $total * 100, 1) : 0,
                'average_training_hours' => $completed > 0 ? round($completedTrainings->avg('training_duration_hours'), 1) : 0,
                'training_types' => $trainings->groupBy('training_type')->map->count()->toArray(),
                'upcoming_trainings' => $trainings->where('status', 'scheduled')
                    ->where('training_date', '>=', now()->toDateString())
                    ->count(),
                'overdue_trainings' => $trainings->where('status', 'scheduled')
                    ->where('training_date', '<', now()->toDateString())
                    ->count(),
                'required_modules' => 4,
            ];

            return response()->json([
                'success' => true,
                'statistics' => $stats,
            ]);

        } catch (\Exception $e) {
            \Log::error('Statistics retrieval failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get employees requiring training.
     */
    public function requiringTraining()
    {
        try {
            $clientId = session('current_client_id');
            if (! $clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a client first.',
                ], 400);
            }

            $trainedEmployeeIds = InductionTraining::where('client_id', $clientId)
                ->where('status', 'completed')
                ->distinct()
                ->pluck('employee_id');

            $employees = Employee::where('status', 'active')
                ->whereNotIn('id', $trainedEmployeeIds)
                ->orderBy('first_name')
                ->get()
                ->map(fn ($e) => [
                    'id' => $e->id,
                    'first_name' => $e->first_name,
                    'last_name' => $e->last_name,
                    'full_name' => $e->full_name,
                    'employee_id' => $e->employee_id,
                    'position' => $e->position,
                    'department' => $e->department,
                ]);

            return response()->json([
                'success' => true,
                'employees' => $employees,
            ]);

        } catch (\Exception $e) {
            \Log::error('Requiring training employees retrieval failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Schedule training for employees (creates scheduled induction records).
     */
    public function scheduleTraining(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
            'training_type' => 'required|in:company_policies,safety_procedures,job_specific,compliance,other',
            'training_title' => 'required|string|max:255',
            'scheduled_date' => 'required|date|after:today',
            'trainer_name' => 'required|string|max:255',
            'estimated_duration_hours' => 'required|numeric|min:0.5|max:40',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $clientId = session('current_client_id');

            $created = collect($request->employee_ids)->map(function ($employeeId) use ($request, $clientId) {
                return InductionTraining::create([
                    'client_id' => $clientId,
                    'employee_id' => $employeeId,
                    'training_date' => $request->scheduled_date,
                    'training_type' => $request->training_type,
                    'training_title' => $request->training_title,
                    'training_description' => $request->training_title,
                    'trainer_name' => $request->trainer_name,
                    'training_duration_hours' => $request->estimated_duration_hours,
                    'status' => 'scheduled',
                    'notes' => trim(($request->notes ?? '').($request->location ? "\nLocation: ".$request->location : '')),
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            });

            AuditLogger::log(
                'induction_training.scheduled',
                $created->first(),
                'Induction Training',
                "Training '{$request->training_title}' scheduled for {$created->count()} employees on {$request->scheduled_date}",
                null,
                ['employee_ids' => $request->employee_ids, 'training_type' => $request->training_type, 'scheduled_date' => $request->scheduled_date]
            );

            return response()->json([
                'success' => true,
                'message' => 'Training scheduled successfully for '.$created->count().' employees',
                'data' => ['count' => $created->count()],
            ]);

        } catch (\Exception $e) {
            \Log::error('Training scheduling failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload training materials and attach them to a training record.
     */
    public function uploadMaterials(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'materials_file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,zip|max:20480',
            'materials_description' => 'required|string|max:500',
            'training_id' => 'nullable|exists:induction_trainings,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            if (! $request->hasFile('materials_file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file uploaded',
                ], 400);
            }

            $file = $request->file('materials_file');
            $fileName = time().'_materials_'.$employee->id.'.'.$file->getClientOriginalExtension();
            $filePath = $file->storeAs('induction-training-materials', $fileName, 'public');

            $trainingId = null;

            // Attach materials to a specific training, or the employee's latest training
            if ($request->training_id) {
                $training = InductionTraining::where('employee_id', $employee->id)
                    ->where('id', $request->training_id)
                    ->first();
            } else {
                $training = InductionTraining::where('employee_id', $employee->id)
                    ->orderBy('training_date', 'desc')
                    ->first();
            }

            if ($training) {
                $training->update([
                    'training_materials_path' => $filePath,
                    'updated_by' => auth()->id(),
                ]);
                $trainingId = $training->id;
            }

            AuditLogger::log(
                'induction_training.materials_uploaded',
                $training,
                'Induction Training',
                "Training materials uploaded for employee #{$employee->id}".($trainingId ? " (training #{$trainingId})" : ''),
                null,
                ['file_path' => $filePath]
            );

            return response()->json([
                'success' => true,
                'message' => 'Training materials uploaded successfully',
                'file_path' => $filePath,
                'training_id' => $trainingId,
            ]);

        } catch (\Exception $e) {
            \Log::error('Materials upload failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download training materials.
     */
    public function downloadMaterials(Employee $employee, $materialId)
    {
        try {
            $training = InductionTraining::where('employee_id', $employee->id)
                ->where('id', $materialId)
                ->first();

            if (! $training || ! $training->training_materials_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Materials not found for this training record',
                ], 404);
            }

            $fullPath = storage_path('app/public/'.$training->training_materials_path);

            if (! file_exists($fullPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Materials file is missing on disk',
                ], 404);
            }

            return response()->download($fullPath, basename($training->training_materials_path));

        } catch (\Exception $e) {
            \Log::error('Materials download failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get training calendar (upcoming scheduled trainings).
     */
    public function calendar()
    {
        try {
            $clientId = session('current_client_id');
            if (! $clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a client first.',
                ], 400);
            }

            $scheduled = InductionTraining::where('client_id', $clientId)
                ->where('status', 'scheduled')
                ->where('training_date', '>=', now()->toDateString())
                ->with('employee')
                ->orderBy('training_date', 'asc')
                ->get();

            $events = $scheduled->groupBy('training_date')->map(function ($group, $date) {
                return [
                    'training_id' => $group->first()->id,
                    'title' => $group->first()->training_title,
                    'start' => $date instanceof Carbon ? $date->format('Y-m-d') : date('Y-m-d', strtotime($date)),
                    'type' => $group->first()->training_type,
                    'employees_count' => $group->count(),
                    'trainer' => $group->first()->trainer_name,
                    'employee_names' => $group->map(fn ($t) => $t->employee ? $t->employee->full_name : 'Employee')->values(),
                ];
            })->values();

            return response()->json([
                'success' => true,
                'events' => $events,
            ]);

        } catch (\Exception $e) {
            \Log::error('Calendar retrieval failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Persist uploaded training files and return their storage paths.
     */
    private function storeUploadedFiles(Request $request): array
    {
        $uploadedFiles = [];
        $fileFields = [
            'training_materials' => 'training_materials_path',
            'completion_certificate' => 'completion_certificate_path',
        ];

        foreach ($fileFields as $fileInput => $dbField) {
            if ($request->hasFile($fileInput)) {
                $file = $request->file($fileInput);
                $fileName = time().'_'.$fileInput.'_'.($request->employee_id ?? 'employee').'.'.$file->getClientOriginalExtension();
                $uploadedFiles[$dbField] = $file->storeAs('induction-training', $fileName, 'public');
            }
        }

        return $uploadedFiles;
    }
}
