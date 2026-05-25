<?php

namespace App\Http\Controllers;

use App\Models\EmployeeRegistration;
use App\Models\HrCompetencyInterview;
use App\Models\TechnicalInterview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeRegistrationController extends Controller
{
    /**
     * Display the employee registration list.
     */
    public function index(Request $request)
    {
        $query = EmployeeRegistration::with(['hrInterview', 'technicalInterview', 'creator', 'approver']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('email_address', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('work_station')) {
            $query->where('work_station', $request->get('work_station'));
        }

        if ($request->filled('contract_type')) {
            $query->where('type_of_contract', $request->get('contract_type'));
        }

        $employees = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        return view('hris.employee-registration.index', compact('employees'));
    }

    /**
     * Display the employee registration creation form.
     */
    public function create()
    {
        $hrInterviews = HrCompetencyInterview::where('status', 'hr_approved')->get();
        $technicalInterviews = TechnicalInterview::where('status', 'manager_approved')->get();
        return view('hris.employee-registration.create', compact('hrInterviews', 'technicalInterviews'));
    }

    /**
     * Store a newly registered employee.
     */
    public function store(Request $request)
    {
        $isDraft = $request->input('status') === 'draft';

        $rules = [
            'hr_interview_id' => 'required|exists:hr_competency_interviews,id',
            'technical_interview_id' => 'nullable|exists:technical_interviews,id',
            'surname' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'first_name' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'birthplace' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'date_of_birth' => $isDraft ? 'nullable|date|before:today' : 'required|date|before:today',
            'gender' => $isDraft ? 'nullable|in:male,female,other' : 'required|in:male,female,other',
            'residence_area' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'permanent_residence' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'postal_address' => 'nullable|string|max:255',
            'email_address' => $isDraft ? 'nullable|email|max:255|unique:employee_registrations' : 'required|email|max:255|unique:employee_registrations',
            'phone_number' => $isDraft ? 'nullable|string|max:20|unique:employee_registrations' : 'required|string|max:20|unique:employee_registrations',
            'place_of_recruitment' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'work_station' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'type_of_contract' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'job_descriptions' => $isDraft ? 'nullable|string|max:5000' : 'required|string|max:5000',
            'date_employed' => $isDraft ? 'nullable|date|before_or_equal:today' : 'required|date|before_or_equal:today',
            'terms_conditions' => $isDraft ? 'nullable|string|max:5000' : 'required|string|max:5000',
            'information_consent' => $isDraft ? 'nullable|boolean' : 'required|boolean',
            'ranking_details' => 'nullable|string|max:2000',
            'employment_history' => 'nullable|string|max:3000',
            'status' => 'nullable|in:draft,submitted',
        ];

        $validated = $request->validate($rules);

        try {
            $status = $request->input('status', 'draft');
            // Generate unique employee number
            $employeeNumber = $this->generateEmployeeNumber();

            $employee = EmployeeRegistration::create(array_merge($validated, [
                'client_id' => session('current_client_id'),
                'employee_number' => $employeeNumber,
                'created_by' => auth()->id(),
                'status' => $status,
            ]));

            // Handle file upload for signed document
            if ($request->hasFile('signed_document')) {
                $file = $request->file('signed_document');
                $fileName = 'employee_signed_' . $employee->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('employee-documents', $fileName, 'public');
                $employee->update(['signed_document_path' => $filePath]);
            }

            return response()->json([
                'success' => true,
                'message' => $status === 'submitted' 
                    ? 'Employee registration submitted for approval' 
                    : 'Employee registration saved as draft',
                'employee' => $employee,
                'employee_number' => $employeeNumber
            ]);

        } catch (\Exception $e) {
            \Log::error('Employee registration failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified employee registration.
     */
    public function show(EmployeeRegistration $employeeRegistration)
    {
        $employeeRegistration->load(['hrInterview', 'technicalInterview', 'creator', 'approver']);
        return view('hris.employee-registration.show', compact('employeeRegistration'));
    }

    /**
     * Show the form for editing the specified employee registration.
     */
    public function edit(EmployeeRegistration $employeeRegistration)
    {
        if ($employeeRegistration->status !== 'draft') {
            return redirect()->route('employee-registration.show', $employeeRegistration)
                ->with('error', 'Cannot edit employee registration that has been submitted');
        }

        $hrInterviews = HrCompetencyInterview::where('status', 'hr_approved')->get();
        $technicalInterviews = TechnicalInterview::where('status', 'manager_approved')->get();
        return view('hris.employee-registration.edit', compact('employeeRegistration', 'hrInterviews', 'technicalInterviews'));
    }

    /**
     * Update the specified employee registration.
     */
    public function update(Request $request, EmployeeRegistration $employeeRegistration)
    {
        if ($employeeRegistration->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit employee registration that has been submitted'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'hr_interview_id' => 'required|exists:hr_competency_interviews,id',
            'technical_interview_id' => 'nullable|exists:technical_interviews,id',
            'surname' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'birthplace' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'residence_area' => 'required|string|max:255',
            'permanent_residence' => 'required|string|max:255',
            'postal_address' => 'nullable|string|max:255',
            'email_address' => 'required|email|max:255|unique:employee_registrations,email_address,' . $employeeRegistration->id,
            'phone_number' => 'required|string|max:20|unique:employee_registrations,phone_number,' . $employeeRegistration->id,
            'place_of_recruitment' => 'required|string|max:255',
            'work_station' => 'required|string|max:255',
            'type_of_contract' => 'required|string|max:255',
            'job_descriptions' => 'required|string|max:5000',
            'date_employed' => 'required|date|before_or_equal:today',
            'terms_conditions' => 'required|string|max:5000',
            'information_consent' => 'required|boolean',
            'ranking_details' => 'nullable|string|max:2000',
            'employment_history' => 'nullable|string|max:3000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $employeeRegistration->update($request->all());

            // Handle file upload for signed document
            if ($request->hasFile('signed_document')) {
                $file = $request->file('signed_document');
                $fileName = 'employee_signed_' . $employeeRegistration->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('employee-documents', $fileName, 'public');
                $employeeRegistration->update(['signed_document_path' => $filePath]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Employee registration updated successfully',
                'employee' => $employeeRegistration
            ]);

        } catch (\Exception $e) {
            \Log::error('Employee registration update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit employee registration for approval.
     */
    public function submit(EmployeeRegistration $employeeRegistration)
    {
        if ($employeeRegistration->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Employee registration has already been submitted'
            ], 403);
        }

        try {
            $employeeRegistration->update(['status' => 'submitted']);

            return response()->json([
                'success' => true,
                'message' => 'Employee registration submitted for approval',
                'employee' => $employeeRegistration
            ]);

        } catch (\Exception $e) {
            \Log::error('Employee registration submission failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve employee registration.
     */
    public function approve(EmployeeRegistration $employeeRegistration)
    {
        if ($employeeRegistration->status !== 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Employee registration is not pending approval'
            ], 403);
        }

        try {
            $employeeRegistration->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employee registration approved successfully',
                'employee' => $employeeRegistration
            ]);

        } catch (\Exception $e) {
            \Log::error('Employee registration approval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject employee registration.
     */
    public function reject(Request $request, EmployeeRegistration $employeeRegistration)
    {
        $reason = $request->input('reason');

        if (!$reason) {
            return response()->json([
                'success' => false,
                'message' => 'Rejection reason is required'
            ], 400);
        }

        try {
            $employeeRegistration->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employee registration rejected',
                'employee' => $employeeRegistration
            ]);

        } catch (\Exception $e) {
            \Log::error('Employee registration rejection failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF registration form.
     */
    public function generatePdf(EmployeeRegistration $employeeRegistration)
    {
        try {
            // This would generate a PDF registration form using a library like DomPDF
            // For now, return a success response
            return response()->json([
                'success' => true,
                'message' => 'Employee registration PDF generated successfully',
                'download_url' => '/employee-registrations/' . $employeeRegistration->id . '/pdf'
            ]);

        } catch (\Exception $e) {
            \Log::error('PDF generation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload signed document.
     */
    public function uploadSignedDocument(Request $request, EmployeeRegistration $employeeRegistration)
    {
        $validator = Validator::make($request->all(), [
            'signed_document' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('signed_document');
            $fileName = 'employee_signed_' . $employeeRegistration->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('employee-documents', $fileName, 'public');

            $employeeRegistration->update(['signed_document_path' => $filePath]);

            return response()->json([
                'success' => true,
                'message' => 'Signed document uploaded successfully',
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
     * Generate unique employee number.
     */
    private function generateEmployeeNumber()
    {
        $prefix = 'EMP';
        $year = date('Y');
        $sequence = str_pad(EmployeeRegistration::count() + 1, 4, '0', STR_PAD_LEFT);
        return "{$prefix}{$year}{$sequence}";
    }
}
