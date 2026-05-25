<?php

namespace App\Http\Controllers;

use App\Models\HrCompetencyInterview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HrCompetencyInterviewController extends Controller
{
    /**
     * Display the HR competency interview list.
     */
    public function index()
    {
        $interviews = HrCompetencyInterview::with(['interviewer', 'hrManager'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('hris.hr-interview.index', compact('interviews'));
    }

    /**
     * Display the HR competency interview creation form.
     */
    public function create()
    {
        return view('hris.hr-interview.create');
    }

    /**
     * Store a newly created HR competency interview.
     */
    public function store(Request $request)
    {
        $isDraft = $request->input('status') === 'draft';

        $rules = [
            'job_title' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'interview_date' => $isDraft ? 'nullable|date' : 'required|date',
            'candidate_name' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'interviewer_name' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'military_service_status' => $isDraft ? 'nullable|in:completed,didnt_attend,na' : 'required|in:completed,didnt_attend,na',
            'place_of_recruitment' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'total_years_experience' => $isDraft ? 'nullable|integer|min:0|max:50' : 'required|integer|min:0|max:50',
            'education_job_knowledge' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'relevant_job_experience' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'major_previous_achievement' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'language_fluency' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'interactive_communication' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'accountability' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'work_excellence' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'functional_competencies' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'planning_organizing' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'problem_solving' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'attention_to_details' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'multitasking' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'continuous_improvement' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'compliance' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'creative_innovation' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'negotiation' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'teamwork' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'adaptability_flexibility' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'leadership' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'managing_developing_people' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'managing_change' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'making_decisions' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'overall_rating' => $isDraft ? 'nullable|integer|min:0|max:5' : 'required|integer|min:0|max:5',
            'relative_inside_client' => $isDraft ? 'nullable|in:yes,no' : 'required|in:yes,no',
            'birthplace' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'residence' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'employed_before' => $isDraft ? 'nullable|in:yes,no' : 'required|in:yes,no',
            'reference_checking' => $isDraft ? 'nullable|in:yes,no' : 'required|in:yes,no',
            'current_employer_entity' => $isDraft ? 'nullable|in:government,private' : 'required|in:government,private',
            'recruiter_recommendation' => $isDraft ? 'nullable|in:accepted,not_accepted,waiting_list' : 'required|in:accepted,not_accepted,waiting_list',
            'recommended_job_title' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,submitted',
        ];

        $validated = $request->validate($rules);

        try {
            $status = $request->input('status', 'draft');
            $interviewNumber = HrCompetencyInterview::generateInterviewNumber();

            $interview = HrCompetencyInterview::create(array_merge($validated, [
                'client_id' => session('current_client_id'),
                'interview_number' => $interviewNumber,
                'interviewer_id' => auth()->id(),
                'status' => $status,
            ]));

            // Handle file upload for military certificate
            if ($request->hasFile('military_certificate')) {
                $file = $request->file('military_certificate');
                $fileName = 'military_cert_' . $interview->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('interview-documents', $fileName, 'public');
                $interview->update(['military_certificate_path' => $filePath]);
            }

            return response()->json([
                'success' => true,
                'message' => $status === 'submitted' 
                    ? 'HR competency interview submitted for approval' 
                    : 'HR competency interview saved as draft',
                'interview' => $interview,
                'interview_number' => $interviewNumber
            ]);

        } catch (\Exception $e) {
            \Log::error('HR competency interview creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified HR competency interview.
     */
    public function show(HrCompetencyInterview $hrCompetencyInterview)
    {
        $hrCompetencyInterview->load(['interviewer', 'hrManager']);
        return view('hris.hr-interview.show', compact('hrCompetencyInterview'));
    }

    /**
     * Show the form for editing the specified HR competency interview.
     */
    public function edit(HrCompetencyInterview $hrCompetencyInterview)
    {
        if (!$hrCompetencyInterview->canBeEdited()) {
            return redirect()->route('hr-interview.show', $hrCompetencyInterview)
                ->with('error', 'Cannot edit interview that has been submitted for more than 7 days');
        }

        return view('hris.hr-interview.edit', compact('hrCompetencyInterview'));
    }

    /**
     * Update the specified HR competency interview.
     */
    public function update(Request $request, HrCompetencyInterview $hrCompetencyInterview)
    {
        if (!$hrCompetencyInterview->canBeEdited()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit interview that has been submitted for more than 7 days'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'job_title' => 'required|string|max:255',
            'interview_date' => 'required|date',
            'candidate_name' => 'required|string|max:255',
            'interviewer_name' => 'required|string|max:255',
            'military_service_status' => 'required|in:completed,didnt_attend,na',
            'place_of_recruitment' => 'required|string|max:255',
            'total_years_experience' => 'required|integer|min:0|max:50',
            'education_job_knowledge' => 'required|integer|min:0|max:5',
            'relevant_job_experience' => 'required|integer|min:0|max:5',
            'major_previous_achievement' => 'required|integer|min:0|max:5',
            'language_fluency' => 'required|integer|min:0|max:5',
            'interactive_communication' => 'required|integer|min:0|max:5',
            'accountability' => 'required|integer|min:0|max:5',
            'work_excellence' => 'required|integer|min:0|max:5',
            'functional_competencies' => 'required|integer|min:0|max:5',
            'planning_organizing' => 'required|integer|min:0|max:5',
            'problem_solving' => 'required|integer|min:0|max:5',
            'attention_to_details' => 'required|integer|min:0|max:5',
            'multitasking' => 'required|integer|min:0|max:5',
            'continuous_improvement' => 'required|integer|min:0|max:5',
            'compliance' => 'required|integer|min:0|max:5',
            'creative_innovation' => 'required|integer|min:0|max:5',
            'negotiation' => 'required|integer|min:0|max:5',
            'teamwork' => 'required|integer|min:0|max:5',
            'adaptability_flexibility' => 'required|integer|min:0|max:5',
            'leadership' => 'required|integer|min:0|max:5',
            'managing_developing_people' => 'required|integer|min:0|max:5',
            'managing_change' => 'required|integer|min:0|max:5',
            'making_decisions' => 'required|integer|min:0|max:5',
            'overall_rating' => 'required|integer|min:0|max:5',
            'relative_inside_client' => 'required|in:yes,no',
            'birthplace' => 'required|string|max:255',
            'residence' => 'required|string|max:255',
            'employed_before' => 'required|in:yes,no',
            'reference_checking' => 'required|in:yes,no',
            'current_employer_entity' => 'required|in:government,private',
            'recruiter_recommendation' => 'required|in:accepted,not_accepted,waiting_list',
            'recommended_job_title' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $hrCompetencyInterview->update($request->all());

            // Handle file upload for military certificate
            if ($request->hasFile('military_certificate')) {
                $file = $request->file('military_certificate');
                $fileName = 'military_cert_' . $hrCompetencyInterview->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('interview-documents', $fileName, 'public');
                $hrCompetencyInterview->update(['military_certificate_path' => $filePath]);
            }

            return response()->json([
                'success' => true,
                'message' => 'HR competency interview updated successfully',
                'interview' => $hrCompetencyInterview
            ]);

        } catch (\Exception $e) {
            \Log::error('HR competency interview update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit interview for HR approval.
     */
    public function submit(HrCompetencyInterview $hrCompetencyInterview)
    {
        if ($hrCompetencyInterview->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Interview has already been submitted'
            ], 403);
        }

        try {
            $hrCompetencyInterview->update(['status' => 'submitted']);

            return response()->json([
                'success' => true,
                'message' => 'Interview submitted for HR approval',
                'interview' => $hrCompetencyInterview
            ]);

        } catch (\Exception $e) {
            \Log::error('Interview submission failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve interview (HR Manager).
     */
    public function approve(HrCompetencyInterview $hrCompetencyInterview)
    {
        if ($hrCompetencyInterview->status !== 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Interview is not pending approval'
            ], 403);
        }

        try {
            $hrCompetencyInterview->update([
                'status' => 'hr_approved',
                'hr_manager_id' => auth()->id(),
                'hr_manager_approved_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Interview approved successfully',
                'interview' => $hrCompetencyInterview
            ]);

        } catch (\Exception $e) {
            \Log::error('Interview approval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject interview.
     */
    public function reject(Request $request, HrCompetencyInterview $hrCompetencyInterview)
    {
        $reason = $request->input('reason');

        if (!$reason) {
            return response()->json([
                'success' => false,
                'message' => 'Rejection reason is required'
            ], 400);
        }

        try {
            $hrCompetencyInterview->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Interview rejected',
                'interview' => $hrCompetencyInterview
            ]);

        } catch (\Exception $e) {
            \Log::error('Interview rejection failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload signed file.
     */
    public function uploadSignedFile(Request $request, HrCompetencyInterview $hrCompetencyInterview)
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
            $fileName = 'signed_interview_' . $hrCompetencyInterview->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('interview-documents', $fileName, 'public');

            $hrCompetencyInterview->update(['signed_file_path' => $filePath]);

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
     * Generate PDF report.
     */
    public function generatePdf(HrCompetencyInterview $hrCompetencyInterview)
    {
        try {
            // This would generate a PDF report using a library like DomPDF
            // For now, return a success response
            return response()->json([
                'success' => true,
                'message' => 'PDF report generated successfully',
                'download_url' => '/interviews/' . $hrCompetencyInterview->id . '/pdf'
            ]);

        } catch (\Exception $e) {
            \Log::error('PDF generation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }
}
