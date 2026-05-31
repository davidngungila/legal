<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JobVacancyController extends Controller
{
    /**
     * Display the job vacancies list.
     */
    public function index()
    {
        $vacancies = JobVacancy::with(['initiator', 'supervisor', 'manager', 'hrManager'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('hris.job-vacancy.index', compact('vacancies'));
    }

    /**
     * Display the job vacancy creation form.
     */
    public function create()
    {
        return view('hris.job-vacancy.create');
    }

    /**
     * Store a newly created job vacancy.
     */
    public function store(Request $request)
    {
        $isDraft = $request->input('status') === 'draft';

        $rules = [
            'company_name' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'job_title' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'vacancy_type' => $isDraft ? 'nullable|in:new_position,replacement' : 'required|in:new_position,replacement',
            'position_vacant_date' => $isDraft ? 'nullable|date' : 'required|date',
            'application_date' => $isDraft ? 'nullable|date' : 'required|date',
            'application_deadline' => $isDraft ? 'nullable|date' : 'required|date|after:application_date',
            'department' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'workstation' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'replacement_reason' => $isDraft ? 'nullable|string|max:1000' : 'required_if:vacancy_type,replacement|string|max:1000',
            'job_description' => $isDraft ? 'nullable|string|max:5000' : 'required|string|max:5000',
            'min_age' => 'nullable|integer|min:18|max:65',
            'academic_qualifications' => 'nullable|string|max:2000',
            'professional_qualifications' => 'nullable|string|max:2000',
            'other_qualifications' => 'nullable|string|max:2000',
            'salary_range_min' => 'nullable|numeric|min:0',
            'salary_range_max' => 'nullable|numeric|min:0',
            'additional_comments' => 'nullable|string|max:2000',
            'status' => 'nullable|in:draft,submitted',
        ];

        $messages = [
            'application_deadline.after' => 'Application deadline must be after application date',
            'salary_range_max.min' => 'Maximum salary must be greater than or equal to minimum salary',
        ];

        $validated = $request->validate($rules, $messages);

        try {
            $status = $request->input('status', 'draft');
            
            $vacancy = JobVacancy::create(array_merge($validated, [
                'client_id' => session('current_client_id'),
                'status' => $status,
                'initiated_by' => auth()->id(),
                'application_date' => $status === 'submitted' ? now() : ($validated['application_date'] ?? now()),
            ]));

            return response()->json([
                'success' => true,
                'message' => $status === 'submitted' 
                    ? 'Job vacancy successfully submitted for approval' 
                    : 'Job vacancy saved as draft',
                'vacancy' => $vacancy
            ]);

        } catch (\Exception $e) {
            \Log::error('Job vacancy creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified job vacancy.
     */
    public function show(JobVacancy $jobVacancy)
    {
        $jobVacancy->load(['initiator', 'supervisor', 'manager', 'hrManager']);
        return view('hris.job-vacancy.show', compact('jobVacancy'));
    }

    /**
     * Show the form for editing the specified job vacancy.
     */
    public function edit(JobVacancy $jobVacancy)
    {
        if ($jobVacancy->status !== 'draft') {
            return redirect()->route('job-vacancy.show', $jobVacancy)
                ->with('error', 'Cannot edit vacancy that has been submitted');
        }

        return view('hris.job-vacancy.edit', compact('jobVacancy'));
    }

    /**
     * Update the specified job vacancy.
     */
    public function update(Request $request, JobVacancy $jobVacancy)
    {
        if ($jobVacancy->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit vacancy that has been submitted'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'vacancy_type' => 'required|in:new_position,replacement',
            'position_vacant_date' => 'required|date',
            'application_date' => 'required|date',
            'application_deadline' => 'required|date|after:application_date',
            'department' => 'required|string|max:255',
            'workstation' => 'required|string|max:255',
            'replacement_reason' => 'required_if:vacancy_type,replacement|string|max:1000',
            'job_description' => 'required|string|max:5000',
            'min_age' => 'nullable|integer|min:18|max:65',
            'academic_qualifications' => 'nullable|string|max:2000',
            'professional_qualifications' => 'nullable|string|max:2000',
            'other_qualifications' => 'nullable|string|max:2000',
            'salary_range_min' => 'nullable|numeric|min:0',
            'salary_range_max' => 'nullable|numeric|min:0',
            'additional_comments' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $jobVacancy->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Job vacancy updated successfully',
                'vacancy' => $jobVacancy
            ]);

        } catch (\Exception $e) {
            \Log::error('Job vacancy update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit job vacancy for approval.
     */
    public function submit(JobVacancy $jobVacancy)
    {
        if ($jobVacancy->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Job vacancy has already been submitted'
            ], 403);
        }

        try {
            $jobVacancy->update([
                'status' => 'submitted',
                'application_date' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Job vacancy submitted for approval',
                'vacancy' => $jobVacancy
            ]);

        } catch (\Exception $e) {
            \Log::error('Job vacancy submission failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve job vacancy (for supervisors, managers, HR).
     */
    public function approve(Request $request, JobVacancy $jobVacancy)
    {
        $level = $request->input('level'); // supervisor, manager, hr_manager
        $comments = $request->input('comments');

        try {
            $updateData = [];
            
            switch ($level) {
                case 'supervisor':
                    if ($jobVacancy->status !== 'submitted') {
                        return response()->json(['success' => false, 'message' => 'Invalid status for supervisor approval'], 403);
                    }
                    $updateData = [
                        'status' => 'supervisor_approved',
                        'supervisor_id' => auth()->id(),
                        'supervisor_approved_at' => now(),
                    ];
                    break;
                    
                case 'manager':
                    if ($jobVacancy->status !== 'supervisor_approved') {
                        return response()->json(['success' => false, 'message' => 'Invalid status for manager recommendation'], 403);
                    }
                    $updateData = [
                        'status' => 'manager_recommended',
                        'manager_id' => auth()->id(),
                        'manager_recommended_at' => now(),
                    ];
                    break;
                    
                case 'hr_manager':
                    if ($jobVacancy->status !== 'manager_recommended') {
                        return response()->json(['success' => false, 'message' => 'Invalid status for HR approval'], 403);
                    }
                    $updateData = [
                        'status' => 'hr_approved',
                        'hr_manager_id' => auth()->id(),
                        'hr_approved_at' => now(),
                    ];
                    break;
                    
                default:
                    return response()->json(['success' => false, 'message' => 'Invalid approval level'], 400);
            }

            $jobVacancy->update($updateData);

            return response()->json([
                'success' => true,
                'message' => "Job vacancy approved by {$level}",
                'vacancy' => $jobVacancy
            ]);

        } catch (\Exception $e) {
            \Log::error('Job vacancy approval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject job vacancy.
     */
    public function reject(Request $request, JobVacancy $jobVacancy)
    {
        $reason = $request->input('reason');

        if (!$reason) {
            return response()->json([
                'success' => false,
                'message' => 'Rejection reason is required'
            ], 400);
        }

        try {
            $jobVacancy->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Job vacancy rejected',
                'vacancy' => $jobVacancy
            ]);

        } catch (\Exception $e) {
            \Log::error('Job vacancy rejection failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload shortlisted file.
     */
    public function uploadShortlistedFile(Request $request, JobVacancy $jobVacancy)
    {
        $validator = Validator::make($request->all(), [
            'shortlisted_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('shortlisted_file');
            $fileName = 'shortlisted_' . $jobVacancy->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('job-vacancies', $fileName, 'public');

            $jobVacancy->update(['shortlisted_file_path' => $filePath]);

            return response()->json([
                'success' => true,
                'message' => 'Shortlisted file uploaded successfully',
                'file_path' => $filePath
            ]);

        } catch (\Exception $e) {
            \Log::error('File upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload signed file.
     */
    public function uploadSignedFile(Request $request, JobVacancy $jobVacancy)
    {
        $validator = Validator::make($request->all(), [
            'signed_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('signed_file');
            $fileName = 'signed_' . $jobVacancy->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('job-vacancies', $fileName, 'public');

            $jobVacancy->update(['signed_file_path' => $filePath]);

            return response()->json([
                'success' => true,
                'message' => 'Signed file uploaded successfully',
                'file_path' => $filePath
            ]);

        } catch (\Exception $e) {
            \Log::error('File upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Close job vacancy.
     */
    public function close(JobVacancy $jobVacancy)
    {
        try {
            $jobVacancy->update(['status' => 'closed']);

            return response()->json([
                'success' => true,
                'message' => 'Job vacancy closed successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Job vacancy closure failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified job vacancy.
     */
    public function destroy(JobVacancy $jobVacancy)
    {
        if (!in_array($jobVacancy->status, ['draft', 'rejected'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Only draft or rejected vacancies can be deleted'
            ], 403);
        }

        try {
            $paths = array_values(array_filter([
                $jobVacancy->shortlisted_file_path,
                $jobVacancy->signed_file_path,
            ]));

            if (!empty($paths)) {
                Storage::disk('public')->delete($paths);
            }

            $jobVacancy->delete();

            return response()->json([
                'success' => true,
                'message' => 'Job vacancy deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Job vacancy deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }
}
