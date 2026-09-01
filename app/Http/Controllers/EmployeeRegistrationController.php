<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeRegistration;
use App\Models\Department;
use App\Models\Position;
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

        $departments = Department::forCurrentClient()->where('is_active', true)->get();
        $positions = Position::forCurrentClient()->where('is_active', true)->get();
        $employmentTypes = $this->getEmploymentTypes();
        $managers = Employee::forCurrentClient()->where('status', 'active')->get(['id', 'first_name', 'last_name', 'position']);

        // Define role hierarchy (same as EmployeeController)
        $roleHierarchy = [
            'super_admin' => ['super_admin', 'lead_hr_admin', 'hr_officer', 'finance_payroll_officer', 'line_manager', 'employee', 'external_auditor'],
            'lead_hr_admin' => ['hr_officer', 'finance_payroll_officer', 'line_manager', 'employee', 'external_auditor'],
            'hr_officer' => ['line_manager', 'employee'],
            'finance_payroll_officer' => ['employee'],
            'line_manager' => ['employee'],
            'employee' => [],
            'external_auditor' => [],
        ];

        $currentUser = auth()->user();
        $userRoleNames = $currentUser->roles->pluck('name')->toArray();

        $allowedRoles = [];
        foreach ($userRoleNames as $roleName) {
            if (isset($roleHierarchy[$roleName])) {
                $allowedRoles = array_merge($allowedRoles, $roleHierarchy[$roleName]);
            }
        }
        $allowedRoles = array_unique($allowedRoles);

        if (empty($allowedRoles)) {
            $allowedRoles = array_keys($roleHierarchy);
        }

        $allowedRoles = array_diff($allowedRoles, ['super_admin']);

        $roles = \App\Models\Role::where('is_active', true)->whereIn('name', $allowedRoles)->get();

        return view('hris.employee-registration.create', compact(
            'hrInterviews',
            'technicalInterviews',
            'departments',
            'positions',
            'employmentTypes',
            'managers',
            'roles'
        ));
    }

    /**
     * Get employment types.
     */
    private function getEmploymentTypes(): array
    {
        return [
            'full_time' => 'Full-Time',
            'part_time' => 'Part-Time',
            'contract' => 'Contract',
            'intern' => 'Intern',
        ];
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
            'signature_date' => 'nullable|date',
            'employee_signature' => 'nullable|string',
            'status' => 'nullable|in:draft,submitted',
            // Employee fields (mirroring EmployeeController::store)
            'national_id' => $isDraft ? 'nullable|string|max:50' : 'required|string|max:50',
            'passport_number' => 'nullable|string|max:50',
            'tin_number' => 'nullable|string|max:30',
            'nssf_number' => 'nullable|string|max:30',
            'nhif_number' => 'nullable|string|max:30',
            'department' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'position' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
            'employment_type' => $isDraft ? 'nullable|in:full_time,part_time,contract,intern' : 'required|in:full_time,part_time,contract,intern',
            'employee_status' => $isDraft ? 'nullable|in:active,inactive,terminated,on_leave,probation' : 'required|in:active,inactive,terminated,on_leave,probation',
            'role' => 'nullable|string|max:255|exists:roles,name',
            'manager_id' => 'nullable|exists:employees,id',
            'work_schedule' => 'nullable|string|max:255',
            'education_level' => 'nullable|string|max:255',
            'skills' => 'nullable|string|max:1000',
            'languages' => 'nullable|string|max:1000',
            'professional_qualifications' => 'nullable|string|max:1000',
            'certifications' => 'nullable|string|max:1000',
            'salary' => $isDraft ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'currency' => $isDraft ? 'nullable|in:TZS,USD,EUR,GBP' : 'required|in:TZS,USD,EUR,GBP',
            'payment_frequency' => $isDraft ? 'nullable|in:monthly,bi-weekly,weekly' : 'required|in:monthly,bi-weekly,weekly',
            'bank_account' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            // Login credentials
            'login_email' => $isDraft ? 'nullable|email|max:255' : 'required|email|max:255|unique:users,email',
            'password' => $isDraft ? 'nullable|string|min:8' : 'required|string|min:8|confirmed',
            'password_confirmation' => 'nullable|string|min:8',
        ];

        $validated = $request->validate($rules);

        // Process comma-separated array fields (same as EmployeeController)
        $arrayFields = ['skills', 'languages', 'professional_qualifications', 'certifications'];
        foreach ($arrayFields as $field) {
            if ($request->filled($field)) {
                $values = array_map('trim', explode(',', $request->input($field)));
                $validated[$field] = array_filter($values);
            } else {
                $validated[$field] = null;
            }
        }

        // Hash the login password before persisting
        if ($request->filled('password')) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($request->input('password'));
        } else {
            unset($validated['password']);
        }
        $validated = \Illuminate\Support\Arr::except($validated, ['password_confirmation']);

        // Normalize information consent to a real boolean (absent/off => false)
        $validated['information_consent'] = filter_var($request->input('information_consent', ''), FILTER_VALIDATE_BOOLEAN);

        // Compute age from date of birth
        if (!empty($validated['date_of_birth'])) {
            $validated['age'] = \Carbon\Carbon::parse($validated['date_of_birth'])->age;
        }

        try {
            $status = $request->input('status', 'draft');
            // Generate unique employee number
            $employeeNumber = $this->generateEmployeeNumber();

            $employee = EmployeeRegistration::create(array_merge($validated, [
                'client_id' => session('current_client_id'),
                'employee_number' => $employeeNumber,
                'created_by' => auth()->id(),
                'status' => $status,
                'signature_date' => $validated['signature_date'] ?? null,
            ]));

            // Handle base64 signature
            if ($request->filled('employee_signature')) {
                $signaturePath = $this->saveBase64Signature($request->input('employee_signature'), 'signatures/employee', 'emp_sig', $employee->id);
                if ($signaturePath) {
                    $employee->update(['employee_signature_path' => $signaturePath]);
                }
            }

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

        $departments = Department::forCurrentClient()->where('is_active', true)->get();
        $positions = Position::forCurrentClient()->where('is_active', true)->get();
        $employmentTypes = $this->getEmploymentTypes();
        $managers = Employee::forCurrentClient()->where('status', 'active')->get(['id', 'first_name', 'last_name', 'position']);

        // Define role hierarchy (same as create)
        $roleHierarchy = [
            'super_admin' => ['super_admin', 'lead_hr_admin', 'hr_officer', 'finance_payroll_officer', 'line_manager', 'employee', 'external_auditor'],
            'lead_hr_admin' => ['hr_officer', 'finance_payroll_officer', 'line_manager', 'employee', 'external_auditor'],
            'hr_officer' => ['line_manager', 'employee'],
            'finance_payroll_officer' => ['employee'],
            'line_manager' => ['employee'],
            'employee' => [],
            'external_auditor' => [],
        ];

        $currentUser = auth()->user();
        $userRoleNames = $currentUser->roles->pluck('name')->toArray();

        $allowedRoles = [];
        foreach ($userRoleNames as $roleName) {
            if (isset($roleHierarchy[$roleName])) {
                $allowedRoles = array_merge($allowedRoles, $roleHierarchy[$roleName]);
            }
        }
        $allowedRoles = array_unique($allowedRoles);

        if (empty($allowedRoles)) {
            $allowedRoles = array_keys($roleHierarchy);
        }

        $allowedRoles = array_diff($allowedRoles, ['super_admin']);

        // Include employee's current role if not in allowed list
        if ($employeeRegistration->role && !in_array($employeeRegistration->role, $allowedRoles)) {
            $allowedRoles[] = $employeeRegistration->role;
        }

        $roles = \App\Models\Role::where('is_active', true)->whereIn('name', $allowedRoles)->get();

        return view('hris.employee-registration.edit', compact(
            'employeeRegistration',
            'hrInterviews',
            'technicalInterviews',
            'departments',
            'positions',
            'employmentTypes',
            'managers',
            'roles'
        ));
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
            'surname' => 'nullable|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'birthplace' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'residence_area' => 'nullable|string|max:255',
            'permanent_residence' => 'nullable|string|max:255',
            'postal_address' => 'nullable|string|max:255',
            'email_address' => 'nullable|email|max:255|unique:employee_registrations,email_address,' . $employeeRegistration->id,
            'phone_number' => 'nullable|string|max:20|unique:employee_registrations,phone_number,' . $employeeRegistration->id,
            'place_of_recruitment' => 'nullable|string|max:255',
            'work_station' => 'nullable|string|max:255',
            'type_of_contract' => 'nullable|string|max:255',
            'job_descriptions' => 'nullable|string|max:5000',
            'date_employed' => 'nullable|date|before_or_equal:today',
            'terms_conditions' => 'nullable|string|max:5000',
            'information_consent' => 'nullable|boolean',
            'ranking_details' => 'nullable|string|max:2000',
            'employment_history' => 'nullable|string|max:3000',
            // Employee fields
            'national_id' => 'nullable|string|max:50',
            'passport_number' => 'nullable|string|max:50',
            'tin_number' => 'nullable|string|max:30',
            'nssf_number' => 'nullable|string|max:30',
            'nhif_number' => 'nullable|string|max:30',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'employment_type' => 'nullable|in:full_time,part_time,contract,intern',
            'employee_status' => 'nullable|in:active,inactive,terminated,on_leave,probation',
            'role' => 'nullable|string|max:255|exists:roles,name',
            'manager_id' => 'nullable|exists:employees,id',
            'work_schedule' => 'nullable|string|max:255',
            'education_level' => 'nullable|string|max:255',
            'skills' => 'nullable|string|max:1000',
            'languages' => 'nullable|string|max:1000',
            'professional_qualifications' => 'nullable|string|max:1000',
            'certifications' => 'nullable|string|max:1000',
            'salary' => 'nullable|numeric|min:0',
            'currency' => 'nullable|in:TZS,USD,EUR,GBP',
            'payment_frequency' => 'nullable|in:monthly,bi-weekly,weekly',
            'bank_account' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'login_email' => 'nullable|email|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'password_confirmation' => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->except(['employee_signature', 'password_confirmation']);

            // Process comma-separated array fields
            $arrayFields = ['skills', 'languages', 'professional_qualifications', 'certifications'];
            foreach ($arrayFields as $field) {
                if ($request->filled($field)) {
                    $values = array_map('trim', explode(',', $request->input($field)));
                    $data[$field] = array_filter($values);
                } else {
                    $data[$field] = null;
                }
            }

            // Hash the login password if provided
            if ($request->filled('password')) {
                $data['password'] = \Illuminate\Support\Facades\Hash::make($request->input('password'));
            } else {
                unset($data['password']);
            }

            // Normalize information consent to a real boolean (absent/off => false)
            $data['information_consent'] = filter_var($request->input('information_consent', ''), FILTER_VALIDATE_BOOLEAN);

            // Compute age from date of birth
            if (!empty($data['date_of_birth'])) {
                $data['age'] = \Carbon\Carbon::parse($data['date_of_birth'])->age;
            }

            $employeeRegistration->update($data);

            // Handle base64 signature
            if ($request->filled('employee_signature')) {
                $signaturePath = $this->saveBase64Signature($request->input('employee_signature'), 'signatures/employee', 'emp_sig', $employeeRegistration->id);
                if ($signaturePath) {
                    $employeeRegistration->update(['employee_signature_path' => $signaturePath]);
                }
            }

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
     *
     * On approval an Employee record and a User account (login credentials)
     * are created and linked to the registration via employee_id.
     */
    public function approve(EmployeeRegistration $employeeRegistration)
    {
        if ($employeeRegistration->status !== 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Employee registration is not pending approval'
            ], 403);
        }

        $clientId = $employeeRegistration->client_id;

        try {
            $employee = \DB::transaction(function () use ($employeeRegistration, $clientId) {
                $registration = $employeeRegistration;

                $employee = Employee::create([
                    'client_id' => $clientId,
                    'employee_id' => $this->generateEmployeeId($clientId),
                    'first_name' => $registration->first_name,
                    'last_name' => $registration->surname,
                    'email' => $registration->login_email ?: $registration->email_address,
                    'phone' => $registration->phone_number,
                    'gender' => $registration->gender,
                    'date_of_birth' => $registration->date_of_birth,
                    'national_id' => $registration->national_id,
                    'passport_number' => $registration->passport_number,
                    'tin_number' => $registration->tin_number,
                    'nssf_number' => $registration->nssf_number,
                    'nhif_number' => $registration->nhif_number,
                    'position' => $registration->position ?: 'General Staff',
                    'department' => $registration->department ?: 'General',
                    'manager_id' => $registration->manager_id,
                    'hire_date' => $registration->date_employed,
                    'employment_type' => $registration->employment_type ?: 'full_time',
                    'status' => $registration->employee_status ?: 'active',
                    'role' => $registration->role,
                    'salary' => $registration->salary,
                    'currency' => $registration->currency ?: 'TZS',
                    'payment_frequency' => $registration->payment_frequency ?: 'monthly',
                    'bank_account' => $registration->bank_account,
                    'bank_name' => $registration->bank_name,
                    'bank_branch' => $registration->bank_branch,
                    'address' => $registration->address,
                    'city' => $registration->city,
                    'region' => $registration->region,
                    'postal_code' => $registration->postal_code,
                    'country' => $registration->country,
                    'emergency_contact_name' => $registration->emergency_contact_name,
                    'emergency_contact_phone' => $registration->emergency_contact_phone,
                    'emergency_contact_relationship' => $registration->emergency_contact_relationship,
                    'work_schedule' => $registration->work_schedule,
                    'education_level' => $registration->education_level,
                    'skills' => $registration->skills,
                    'languages' => $registration->languages,
                    'professional_qualifications' => $registration->professional_qualifications,
                    'certifications' => $registration->certifications,
                    'created_by' => $registration->created_by,
                ]);

                // Create the login user account for the employee
                $user = \App\Models\User::create([
                    'first_name' => $registration->first_name,
                    'last_name' => $registration->surname,
                    'email' => $registration->login_email ?: $registration->email_address,
                    'password' => $registration->password ?: \Illuminate\Support\Facades\Hash::make('password'),
                    'is_active' => true,
                    'current_client_id' => $clientId,
                    'employee_id' => $employee->id,
                    'department' => $registration->department,
                    'position' => $registration->position,
                ]);

                $user->clients()->attach($clientId, [
                    'role' => 'employee',
                    'is_active' => true,
                    'joined_at' => now(),
                ]);

                if ($registration->role) {
                    $role = \App\Models\Role::where('name', $registration->role)->first();
                    if ($role) {
                        $user->roles()->attach($role);
                    }
                } else {
                    $employeeRole = \App\Models\Role::where('name', 'employee')->first();
                    if ($employeeRole) {
                        $user->roles()->attach($employeeRole);
                    }
                }

                $registration->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'employee_id' => $employee->id,
                ]);

                return $employee;
            });

            return response()->json([
                'success' => true,
                'message' => 'Employee registration approved successfully. Employee "' . $employee->full_name . '" (' . $employee->employee_id . ') and login account "' . ($employeeRegistration->login_email ?: $employeeRegistration->email_address) . '" created.',
                'employee' => $employeeRegistration,
                'employee_id' => $employee->id,
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
     * Generate unique employee ID (globally unique).
     * Mirrors EmployeeController::generateEmployeeId.
     */
    private function generateEmployeeId($clientId)
    {
        $prefix = 'EMP';
        $year = now()->year;

        return \DB::transaction(function () use ($prefix, $year) {
            $existingIds = \DB::table('employees')->pluck('employee_id')->toArray();

            $nextNumber = 1;
            $found = false;
            $maxAttempts = 10000;

            while (!$found && $nextNumber <= $maxAttempts) {
                $newNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                $proposedId = $prefix . $year . $newNumber;

                if (!in_array($proposedId, $existingIds)) {
                    $found = true;
                    return $proposedId;
                }

                $nextNumber++;
            }

            return $prefix . $year . uniqid('', true);
        });
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
     * Save base64 signature to storage.
     */
    private function saveBase64Signature(string $base64Data, string $directory, string $prefix, int $id): ?string
    {
        try {
            $signatureData = preg_replace('#^data:image/\w+;base64,#i', '', $base64Data);
            $signatureImage = base64_decode($signatureData);

            if ($signatureImage === false) {
                return null;
            }

            $fileName = $prefix . '_' . $id . '_' . time() . '.png';
            $path = $directory . '/' . $fileName;
            Storage::disk('public')->put($path, $signatureImage);

            return $path;
        } catch (\Exception $e) {
            \Log::error('Signature save failed: ' . $e->getMessage());
            return null;
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
