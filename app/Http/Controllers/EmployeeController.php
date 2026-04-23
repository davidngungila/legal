<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Client;

class EmployeeController
{
    /**
     * Display employees index
     */
    public function index()
    {
        $clientId = session('current_client_id');
        $employees = Employee::where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('employees.index', compact('employees'));
    }

    /**
     * Show create employee form
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store new employee
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employees',
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'national_id' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
        ]);

        $clientId = session('current_client_id');
        $lastEmployee = Employee::where('client_id', $clientId)->orderBy('id', 'desc')->first();
        $employeeId = 'EMP' . str_pad($clientId, 2, '0', STR_PAD_LEFT) . str_pad(($lastEmployee ? $lastEmployee->id + 1 : 1), 3, '0', STR_PAD_LEFT);

        Employee::create([
            'client_id' => $clientId,
            'employee_id' => $employeeId,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'national_id' => $request->national_id,
            'position' => $request->position,
            'department' => $request->department,
            'employment_type' => $request->employment_type,
            'status' => 'active',
            'salary' => $request->salary,
            'hire_date' => $request->hire_date,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully!');
    }

    /**
     * Show employee details
     */
    public function show(Employee $employee)
    {
        $this->authorizeEmployeeAccess($employee);
        return view('employees.show', compact('employee'));
    }

    /**
     * Show edit employee form
     */
    public function edit(Employee $employee)
    {
        $this->authorizeEmployeeAccess($employee);
        return view('employees.edit', compact('employee'));
    }

    /**
     * Update employee
     */
    public function update(Request $request, Employee $employee)
    {
        $this->authorizeEmployeeAccess($employee);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'salary' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,terminated,on_leave',
            'address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
        ]);

        $employee->update($request->all());

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Employee updated successfully!');
    }

    /**
     * Delete employee
     */
    public function destroy(Employee $employee)
    {
        $this->authorizeEmployeeAccess($employee);
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully!');
    }

    /**
     * Generate contract for employee
     */
    public function generateContract(Employee $employee)
    {
        $this->authorizeEmployeeAccess($employee);
        
        // Contract generation logic would go here
        return response()->json([
            'success' => true,
            'message' => 'Contract generated successfully!'
        ]);
    }

    /**
     * Export employees
     */
    public function export()
    {
        $clientId = session('current_client_id');
        $employees = Employee::where('client_id', $clientId)->get();
        
        // Export logic would go here
        return response()->json($employees);
    }

    /**
     * Search employees
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $clientId = session('current_client_id');
        $query = $request->query;
        
        $employees = Employee::where('client_id', $clientId)
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('employee_id', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('employees.index', compact('employees', 'query'));
    }

    /**
     * Get employee statistics
     */
    public function statistics()
    {
        $clientId = session('current_client_id');
        
        $stats = [
            'total_employees' => Employee::where('client_id', $clientId)->count(),
            'active_employees' => Employee::where('client_id', $clientId)->where('status', 'active')->count(),
            'new_hires_this_month' => Employee::where('client_id', $clientId)
                ->where('hire_date', '>=', now()->startOfMonth())->count(),
            'employees_on_leave' => Employee::where('client_id', $clientId)->where('status', 'on_leave')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Authorize employee access
     */
    private function authorizeEmployeeAccess(Employee $employee)
    {
        if ($employee->client_id !== session('current_client_id')) {
            abort(403, 'Unauthorized access to this employee record.');
        }
    }
}
