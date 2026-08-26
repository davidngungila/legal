<?php

namespace App\Http\Controllers;

use App\Models\TechnicalInterview;
use App\Models\HrCompetencyInterview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class TechnicalInterviewController extends Controller
{
    /**
     * Display the technical interview list.
     */
    public function index()
    {
        $interviews = TechnicalInterview::with(['hrInterview', 'interviewer', 'departmentManager'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('hris.technical-interview.index', compact('interviews'));
    }

    /**
     * Display the technical interview creation form.
     */
    public function create()
    {
        $hrInterviews = HrCompetencyInterview::whereIn('status', ['submitted', 'hr_approved'])->get();
        return view('hris.technical-interview.create', compact('hrInterviews'));
    }

    /**
     * Store a newly created technical interview.
     */
    public function store(Request $request)
    {
        $isDraft = $request->input('status') === 'draft';

        $rules = [
            'hr_interview_id' => 'required|exists:hr_competency_interviews,id',
            'candidate_name' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'job_title' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'interview_date' => $isDraft ? 'nullable|date' : 'required|date',
            'interviewer_name' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'business_process_knowledge' => $isDraft ? 'nullable|string|max:5000' : 'required|string|max:5000',
            'technical_skills_assessment' => $isDraft ? 'nullable|string|max:5000' : 'required|string|max:5000',
            'physical_capabilities' => 'nullable|string|max:5000',
            'practical_test_results' => 'nullable|string|max:5000',
            'other_technical_areas' => 'nullable|string|max:5000',
            'technical_result' => $isDraft ? 'nullable|in:pass,fail,na' : 'required|in:pass,fail,na',
            'technical_comments' => 'nullable|string|max:2000',
            'status' => 'nullable|in:draft,submitted',
        ];

        $validated = $request->validate($rules);

        try {
            $status = $request->input('status', 'draft');
            $interviewNumber = TechnicalInterview::generateInterviewNumber();

            $interview = TechnicalInterview::create(array_merge($validated, [
                'client_id' => session('current_client_id'),
                'interview_number' => $interviewNumber,
                'interviewer_id' => auth()->id(),
                'status' => $status,
                'interviewer_completed_at' => $status === 'submitted' ? now() : null,
            ]));

            // Handle base64 signature
            if ($request->filled('interviewer_signature')) {
                $signatureData = preg_replace('#^data:image/\w+;base64,#i', '', $request->input('interviewer_signature'));
                $signatureImage = base64_decode($signatureData);
                if ($signatureImage !== false) {
                    $fileName = 'tech_int_sig_' . $interview->id . '_' . time() . '.png';
                    $path = 'signatures/technical/' . $fileName;
                    Storage::disk('public')->put($path, $signatureImage);
                    $interview->update(['interviewer_signature_path' => $path]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => $status === 'submitted' 
                    ? 'Technical interview submitted for approval' 
                    : 'Technical interview saved as draft',
                'interview' => $interview,
                'interview_number' => $interviewNumber
            ]);

        } catch (\Exception $e) {
            \Log::error('Technical interview creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified technical interview.
     */
    public function show(TechnicalInterview $technicalInterview)
    {
        $technicalInterview->load(['hrInterview', 'interviewer', 'departmentManager']);
        return view('hris.technical-interview.show', compact('technicalInterview'));
    }

    /**
     * Generate PDF for technical interview.
     */
    public function generatePdf(TechnicalInterview $technicalInterview)
    {
        $technicalInterview->load(['hrInterview', 'interviewer', 'departmentManager']);
        
        $pdf = Pdf::loadView('hris.technical-interview.pdf', compact('technicalInterview'))
            ->setPaper('a4')
            ->setOption('margin-top', '20mm')
            ->setOption('margin-bottom', '20mm')
            ->setOption('margin-left', '15mm')
            ->setOption('margin-right', '15mm');
        
        return $pdf->stream('technical-interview-' . $technicalInterview->interview_number . '.pdf');
    }

    /**
     * Download PDF for technical interview.
     */
    public function downloadPdf(TechnicalInterview $technicalInterview)
    {
        $technicalInterview->load(['hrInterview', 'interviewer', 'departmentManager']);
        
        $pdf = Pdf::loadView('hris.technical-interview.pdf', compact('technicalInterview'))
            ->setPaper('a4')
            ->setOption('margin-top', '20mm')
            ->setOption('margin-bottom', '20mm')
            ->setOption('margin-left', '15mm')
            ->setOption('margin-right', '15mm');
        
        return $pdf->download('technical-interview-' . $technicalInterview->interview_number . '.pdf');
    }

    /**
     * Show the form for editing the specified technical interview.
     */
    public function edit(TechnicalInterview $technicalInterview)
    {
        if (!$technicalInterview->canBeEdited()) {
            return redirect()->route('technical-interview.show', $technicalInterview)
                ->with('error', 'Cannot edit interview that has been submitted for more than 7 days');
        }

        $hrInterviews = HrCompetencyInterview::where('status', 'hr_approved')->get();
        return view('hris.technical-interview.edit', compact('technicalInterview', 'hrInterviews'));
    }

    /**
     * Update the specified technical interview.
     */
    public function update(Request $request, TechnicalInterview $technicalInterview)
    {
        if (!$technicalInterview->canBeEdited()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit interview that has been submitted for more than 7 days'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'hr_interview_id' => 'required|exists:hr_competency_interviews,id',
            'candidate_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'interview_date' => 'required|date',
            'interviewer_name' => 'required|string|max:255',
            'business_process_knowledge' => 'required|string|max:5000',
            'technical_skills_assessment' => 'required|string|max:5000',
            'physical_capabilities' => 'nullable|string|max:5000',
            'practical_test_results' => 'nullable|string|max:5000',
            'other_technical_areas' => 'nullable|string|max:5000',
            'technical_result' => 'required|in:pass,fail,na',
            'technical_comments' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $technicalInterview->update($request->except('interviewer_signature'));

            // Handle base64 signature
            if ($request->filled('interviewer_signature')) {
                $signatureData = preg_replace('#^data:image/\w+;base64,#i', '', $request->input('interviewer_signature'));
                $signatureImage = base64_decode($signatureData);
                if ($signatureImage !== false) {
                    $fileName = 'tech_int_sig_' . $technicalInterview->id . '_' . time() . '.png';
                    $path = 'signatures/technical/' . $fileName;
                    Storage::disk('public')->put($path, $signatureImage);
                    $technicalInterview->update(['interviewer_signature_path' => $path]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Technical interview updated successfully',
                'interview' => $technicalInterview
            ]);

        } catch (\Exception $e) {
            \Log::error('Technical interview update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit interview for department manager approval.
     */
    public function submit(TechnicalInterview $technicalInterview)
    {
        if ($technicalInterview->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Interview has already been submitted'
            ], 403);
        }

        if (!$technicalInterview->isComplete()) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete all required assessment fields before submitting'
            ], 400);
        }

        try {
            $technicalInterview->update([
                'status' => 'submitted',
                'interviewer_completed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Technical interview submitted for manager approval',
                'interview' => $technicalInterview
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
     * Approve interview (Department Manager).
     */
    public function approve(Request $request, TechnicalInterview $technicalInterview)
    {
        if ($technicalInterview->status !== 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Interview is not pending approval'
            ], 403);
        }

        try {
            $technicalInterview->update([
                'status' => 'manager_approved',
                'manager_approval' => 'approved',
                'department_manager_id' => auth()->id(),
                'manager_approved_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Technical interview approved successfully',
                'interview' => $technicalInterview
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
    public function reject(Request $request, TechnicalInterview $technicalInterview)
    {
        $reason = $request->input('reason');
        $comments = $request->input('comments');

        if (!$reason) {
            return response()->json([
                'success' => false,
                'message' => 'Rejection reason is required'
            ], 400);
        }

        try {
            $technicalInterview->update([
                'status' => 'rejected',
                'manager_approval' => 'rejected',
                'manager_comments' => $comments,
                'rejection_reason' => $reason,
                'department_manager_id' => auth()->id(),
                'manager_approved_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Technical interview rejected',
                'interview' => $technicalInterview
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
     * Upload assessment report file.
     */
    public function uploadAssessmentReport(Request $request, TechnicalInterview $technicalInterview)
    {
        $validator = Validator::make($request->all(), [
            'assessment_report' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('assessment_report');
            $fileName = 'assessment_report_' . $technicalInterview->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('technical-interviews', $fileName, 'public');

            $technicalInterview->update(['assessment_report_path' => $filePath]);

            return response()->json([
                'success' => true,
                'message' => 'Assessment report uploaded successfully',
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
    public function uploadSignedFile(Request $request, TechnicalInterview $technicalInterview)
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
            $fileName = 'signed_interview_' . $technicalInterview->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('technical-interviews', $fileName, 'public');

            $technicalInterview->update(['signed_file_path' => $filePath]);

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
}
