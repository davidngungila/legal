<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Contract;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the employees.
     */
    public function index(Request $request)
    {
        $currentClient = session('current_client');
        $currentUser = Auth::user();
        
        // Start query with client filter
        $query = Employee::forCurrentClient()
            ->with(['client', 'contracts']);

        // Apply filters if provided
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('employee_id', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->get('department'));
        }

        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->get('employment_type'));
        }

        if ($request->filled('status')) {
            $query->where('status', strtolower($request->get('status')));
        }

        // Get employees with pagination
        $employees = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Get statistics (always for the current client)
        $stats = Employee::getEmployeeStats();

        return view('employees.index', compact('employees', 'stats', 'currentClient', 'currentUser'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $currentClient = session('current_client');
        $departments = $this->getDepartments();
        $positions = $this->getPositions();
        $employmentTypes = $this->getEmploymentTypes();

        return view('employees.create', compact('currentClient', 'departments', 'positions', 'employmentTypes'));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        $clientId = session('current_client_id');
        
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'string',
                'max:255',
                Rule::unique('employees')->where(function ($query) use ($clientId) {
                    return $query->where('client_id', $clientId);
                })
            ],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('employees')->where(function ($query) use ($clientId) {
                    return $query->where('client_id', $clientId);
                })
            ],
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(16)->format('Y-m-d'),
                'after_or_equal:' . now()->subYears(100)->format('Y-m-d')
            ],
            'gender' => 'required|in:male,female,other',
            'national_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('employees')->where(function ($query) use ($clientId) {
                    return $query->where('client_id', $clientId);
                })
            ],
            'passport_number' => 'nullable|string|max:50',
            'tin_number' => 'nullable|string|max:30',
            'nssf_number' => 'nullable|string|max:30',
            'nhif_number' => 'nullable|string|max:30',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:employees,id',
            'hire_date' => 'required|date',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'status' => 'required|in:active,inactive,terminated,on_leave',
            'salary' => 'required|numeric|min:0',
            'currency' => 'required|in:TZS,USD,EUR,GBP',
            'payment_frequency' => 'required|in:monthly,bi-weekly,weekly',
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
            'work_schedule' => 'nullable|string|max:255',
            'education_level' => 'nullable|string|max:255',
            'professional_qualifications' => 'nullable|array',
            'certifications' => 'nullable|array',
            'skills' => 'nullable|array',
            'languages' => 'nullable|array',
            'notes' => 'nullable|string|max:2000',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Force client_id from session
        $validated['client_id'] = $clientId;
        // Add created by user
        $validated['created_by'] = Auth::id();

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('employees/photos', 'public');
            $validated['profile_photo'] = $path;
        }

        $employee = Employee::create($validated);

        return redirect()->route('employees.index')
                     ->with('success', 'Employee "' . $employee->full_name . '" has been added successfully.');
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        // Verify employee belongs to current client
        $currentClient = session('current_client');
        if (!$currentClient || $employee->client_id != $currentClient->id) {
            abort(403, 'Unauthorized access to employee record.');
        }

        $employee->load(['client', 'contracts', 'documents', 'performanceReviews']);

        return view('employees.show', compact('employee', 'currentClient'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        // Verify employee belongs to current client
        $currentClient = session('current_client');
        if (!$currentClient || $employee->client_id != $currentClient->id) {
            abort(403, 'Unauthorized access to employee record.');
        }

        $departments = $this->getDepartments();
        $positions = $this->getPositions();
        $employmentTypes = $this->getEmploymentTypes();

        return view('employees.edit', compact('employee', 'currentClient', 'departments', 'positions', 'employmentTypes'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        // Verify employee belongs to current client
        $currentClient = session('current_client');
        if (!$currentClient || $employee->client_id != $currentClient->id) {
            abort(403, 'Unauthorized access to employee record.');
        }

        $clientId = session('current_client_id');
        
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'string',
                'max:255',
                Rule::unique('employees')->ignore($employee->id)->where(function ($query) use ($clientId) {
                    return $query->where('client_id', $clientId);
                })
            ],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('employees')->ignore($employee->id)->where(function ($query) use ($clientId) {
                    return $query->where('client_id', $clientId);
                })
            ],
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'national_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('employees')->ignore($employee->id)->where(function ($query) use ($clientId) {
                    return $query->where('client_id', $clientId);
                })
            ],
            'passport_number' => 'nullable|string|max:50',
            'tin_number' => 'nullable|string|max:30',
            'nssf_number' => 'nullable|string|max:30',
            'nhif_number' => 'nullable|string|max:30',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:employees,id',
            'hire_date' => 'required|date',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'status' => 'required|in:active,inactive,terminated,on_leave',
            'salary' => 'required|numeric|min:0',
            'currency' => 'required|in:TZS,USD,EUR,GBP',
            'payment_frequency' => 'required|in:monthly,bi-weekly,weekly',
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
            'work_schedule' => 'nullable|string|max:255',
            'education_level' => 'nullable|string|max:255',
            'professional_qualifications' => 'nullable|array',
            'certifications' => 'nullable|array',
            'skills' => 'nullable|array',
            'languages' => 'nullable|array',
            'notes' => 'nullable|string|max:2000',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Add updated by user
        $validated['updated_by'] = Auth::id();

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($employee->profile_photo) {
                Storage::disk('public')->delete($employee->profile_photo);
            }
            $path = $request->file('profile_photo')->store('employees/photos', 'public');
            $validated['profile_photo'] = $path;
        }

        $employee->update($validated);

        return redirect()->route('employees.index')
                     ->with('success', 'Employee "' . $employee->full_name . '" has been updated successfully.');
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee)
    {
        // Verify employee belongs to current client
        $currentClient = session('current_client');
        if (!$currentClient || $employee->client_id != $currentClient->id) {
            abort(403, 'Unauthorized access to employee record.');
        }

        $employee->delete();

        return redirect()->route('employees.index')
                     ->with('success', 'Employee "' . $employee->full_name . '" has been deleted successfully.');
    }

    /**
     * Generate contract for the specified employee.
     */
    public function generateContract(Employee $employee)
    {
        // Verify employee belongs to current client
        $currentClient = session('current_client');
        if ($employee->client_id != $currentClient->id) {
            abort(403, 'Unauthorized access to employee record.');
        }

        // Check if employee has active contract
        $activeContract = $employee->activeContract()->first();
        
        if ($activeContract) {
            return redirect()->route('employees.show', $employee->id)
                ->with('warning', 'Employee already has an active contract!');
        }

        // Create new contract
        $contract = Contract::createContract([
            'client_id' => $employee->client_id,
            'employee_id' => $employee->id,
            'contract_type' => 'permanent',
            'start_date' => now()->format('Y-m-d'),
            'salary' => $employee->salary,
            'currency' => $employee->currency,
            'payment_frequency' => $employee->payment_frequency,
            'work_schedule' => $employee->work_schedule ?? 'Monday - Friday, 9:00 AM - 5:00 PM',
            'work_location' => $currentClient->name,
            'job_description' => $this->generateJobDescription($employee),
            'status' => 'pending_signature',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('contracts.show', $contract->id)
                     ->with('success', 'Contract has been generated for "' . $employee->full_name . '" and is pending signature.');
    }

    /**
     * Export employees data.
     */
    public function export(Request $request)
    {
        $currentClient = session('current_client');
        
        $employees = Employee::forCurrentClient()
            ->with(['client', 'contracts'])
            ->get();

        $filename = 'employees_' . $currentClient->name . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Employee ID',
            'Full Name',
            'Email',
            'Phone',
            'Position',
            'Department',
            'Employment Type',
            'Start Date',
            'Salary',
            'Status',
            'Client',
        ];

        $data = $employees->map(function ($employee) {
            return [
                $employee->employee_id,
                $employee->full_name,
                $employee->email,
                $employee->phone,
                $employee->position,
                $employee->department,
                $employee->employment_type,
                $employee->hire_date->format('Y-m-d'),
                $employee->formatted_salary,
                $employee->status,
                $employee->client->name,
            ];
        })->toArray();

        $callback = function() use ($data, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Search employees.
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $currentClient = session('current_client');
        
        if (empty($query)) {
            return redirect()->route('employees.index');
        }

        $employees = Employee::forCurrentClient()
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', '%' . $query . '%')
                  ->orWhere('last_name', 'like', '%' . $query . '%')
                  ->orWhere('email', 'like', '%' . $query . '%')
                  ->orWhere('employee_id', 'like', '%' . $query . '%')
                  ->orWhere('position', 'like', '%' . $query . '%')
                  ->orWhere('department', 'like', '%' . $query . '%');
            })
            ->with(['client', 'contracts'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('employees.search', compact('employees', 'query', 'currentClient'));
    }

    /**
     * Get employee statistics.
     */
    public function statistics()
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'No client selected'], 400);
        }

        $stats = Employee::getEmployeeStats();
        $recentHires = Employee::forCurrentClient()
            ->where('hire_date', '>=', now()->subDays(30))
            ->with('client')
            ->orderBy('hire_date', 'desc')
            ->limit(5)
            ->get();

        $upcomingExpiries = Contract::getExpiringSoonContracts(30);

        return response()->json([
            'stats' => $stats,
            'recent_hires' => $recentHires,
            'upcoming_expiries' => $upcomingExpiries,
        ]);
    }

    /**
     * Get departments list.
     */
    private function getDepartments(): array
    {
        return [
            'IT' => 'Information Technology',
            'HR' => 'Human Resources',
            'Finance' => 'Finance',
            'Operations' => 'Operations',
            'Sales' => 'Sales',
            'Marketing' => 'Marketing',
            'Legal' => 'Legal',
            'Administration' => 'Administration',
        ];
    }

    /**
     * Get positions list.
     */
    private function getPositions(): array
    {
        return [
            'CEO' => 'Chief Executive Officer',
            'CTO' => 'Chief Technology Officer',
            'CFO' => 'Chief Financial Officer',
            'COO' => 'Chief Operating Officer',
            'HR Manager' => 'Human Resources Manager',
            'IT Manager' => 'Information Technology Manager',
            'Finance Manager' => 'Finance Manager',
            'Operations Manager' => 'Operations Manager',
            'Sales Manager' => 'Sales Manager',
            'Marketing Manager' => 'Marketing Manager',
            'Legal Manager' => 'Legal Manager',
            'Senior Developer' => 'Senior Software Developer',
            'Developer' => 'Software Developer',
            'Designer' => 'UI/UX Designer',
            'Analyst' => 'Business Analyst',
            'Accountant' => 'Senior Accountant',
            'Administrator' => 'System Administrator',
            'Consultant' => 'IT Consultant',
            'Specialist' => 'HR Specialist',
            'Coordinator' => 'Project Coordinator',
            'Assistant' => 'Administrative Assistant',
        ];
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
     * Generate job description.
     */
    private function generateJobDescription(Employee $employee): string
    {
        $descriptions = [
            'IT' => 'Develop and maintain software systems, provide technical support, and ensure network security.',
            'HR' => 'Manage employee relations, oversee recruitment, handle benefits administration, and ensure compliance with labor laws.',
            'Finance' => 'Manage financial records, prepare reports, handle budget planning, and ensure financial compliance.',
            'Operations' => 'Oversee daily operations, improve processes, manage logistics, and ensure operational efficiency.',
            'Sales' => 'Develop and implement sales strategies, manage client relationships, and achieve revenue targets.',
            'Marketing' => 'Create and execute marketing campaigns, manage brand presence, and analyze market trends.',
            'Legal' => 'Provide legal counsel, ensure regulatory compliance, and manage legal documentation.',
            'Administration' => 'Provide administrative support, manage office operations, and coordinate executive activities.',
        ];

        return $descriptions[$employee->department] ?? 'Perform assigned duties and responsibilities as required by the position.';
    }
}
