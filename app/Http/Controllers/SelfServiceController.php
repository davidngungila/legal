<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SelfService;
use App\Models\Employee;
use App\Models\User;

class SelfServiceController
{
    /**
     * Display self-service index
     */
    public function index()
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        $requests = SelfService::where('employee_id', $employee->id ?? null)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('selfservice.index', compact('requests'));
    }

    /**
     * Show leave request form
     */
    public function leave()
    {
        return view('selfservice.leave');
    }

    /**
     * Store leave request
     */
    public function storeLeave(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $employee = Employee::where('user_id', auth()->id())->first();
        
        SelfService::create([
            'employee_id' => $employee->id,
            'client_id' => session('current_client_id'),
            'request_type' => 'leave',
            'title' => 'Leave Request',
            'description' => $request->reason,
            'details' => json_encode([
                'leave_type' => $request->leave_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]),
            'status' => 'pending',
        ]);

        return redirect()->route('selfservice.index')
            ->with('success', 'Leave request submitted successfully!');
    }

    /**
     * Show payslip request form
     */
    public function payslip()
    {
        return view('selfservice.payslip');
    }

    /**
     * Request payslip
     */
    public function requestPayslip(Request $request)
    {
        $request->validate([
            'month' => 'required|string',
            'year' => 'required|integer',
        ]);

        $employee = Employee::where('user_id', auth()->id())->first();
        
        SelfService::create([
            'employee_id' => $employee->id,
            'client_id' => session('current_client_id'),
            'request_type' => 'payslip',
            'title' => 'Payslip Request',
            'description' => "Payslip for {$request->month} {$request->year}",
            'details' => json_encode([
                'month' => $request->month,
                'year' => $request->year,
            ]),
            'status' => 'pending',
        ]);

        return redirect()->route('selfservice.index')
            ->with('success', 'Payslip request submitted successfully!');
    }

    /**
     * Show contract request form
     */
    public function contract()
    {
        return view('selfservice.contract');
    }

    /**
     * Request contract
     */
    public function requestContract(Request $request)
    {
        $request->validate([
            'contract_type' => 'required|string',
            'reason' => 'required|string',
        ]);

        $employee = Employee::where('user_id', auth()->id())->first();
        
        SelfService::create([
            'employee_id' => $employee->id,
            'client_id' => session('current_client_id'),
            'request_type' => 'contract',
            'title' => 'Contract Request',
            'description' => $request->reason,
            'details' => json_encode([
                'contract_type' => $request->contract_type,
            ]),
            'status' => 'pending',
        ]);

        return redirect()->route('selfservice.index')
            ->with('success', 'Contract request submitted successfully!');
    }

    /**
     * Show complaint form
     */
    public function complaint()
    {
        return view('selfservice.complaint');
    }

    /**
     * Store complaint
     */
    public function storeComplaint(Request $request)
    {
        $request->validate([
            'complaint_type' => 'required|string',
            'description' => 'required|string',
            'severity' => 'required|string',
        ]);

        $employee = Employee::where('user_id', auth()->id())->first();
        
        SelfService::create([
            'employee_id' => $employee->id,
            'client_id' => session('current_client_id'),
            'request_type' => 'complaint',
            'title' => 'Complaint: ' . $request->complaint_type,
            'description' => $request->description,
            'details' => json_encode([
                'complaint_type' => $request->complaint_type,
                'severity' => $request->severity,
            ]),
            'status' => 'pending',
        ]);

        return redirect()->route('selfservice.index')
            ->with('success', 'Complaint submitted successfully!');
    }

    /**
     * Show profile form
     */
    public function profile()
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        return view('selfservice.profile', compact('employee'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
        ]);

        $employee = Employee::where('user_id', auth()->id())->first();
        $employee->update($request->only([
            'phone', 'address', 'emergency_contact_name', 'emergency_contact_phone'
        ]));

        return redirect()->route('selfservice.profile')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Show expense form
     */
    public function expense()
    {
        return view('selfservice.expense');
    }

    /**
     * Store expense
     */
    public function storeExpense(Request $request)
    {
        $request->validate([
            'expense_type' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $employee = Employee::where('user_id', auth()->id())->first();
        
        SelfService::create([
            'employee_id' => $employee->id,
            'client_id' => session('current_client_id'),
            'request_type' => 'expense',
            'title' => 'Expense Claim: ' . $request->expense_type,
            'description' => $request->description,
            'details' => json_encode([
                'expense_type' => $request->expense_type,
                'amount' => $request->amount,
            ]),
            'status' => 'pending',
        ]);

        return redirect()->route('selfservice.index')
            ->with('success', 'Expense claim submitted successfully!');
    }
}
