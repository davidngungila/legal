<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SelfService;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SelfServiceController extends Controller
{
    /**
     * Display the self-service dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $currentClient = session('current_client');
        
        // Get employee data for current user and client
        $employee = null;
        $recentRequests = collect(); // Initialize as empty collection
        $recentPayslips = collect(); // Initialize as empty collection
        
        if ($currentClient) {
            // Find employee for current user in this client
            $employee = Employee::where('client_id', $currentClient['id'])
                ->where('email', $user->email)
                ->first();
            
            if ($employee) {
                // Get recent self-service requests
                $recentRequests = SelfService::where('client_id', $currentClient['id'])
                    ->where('employee_id', $employee->id)
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
                
                // Get recent payslips
                $recentPayslips = Payroll::where('client_id', $currentClient['id'])
                    ->where('employee_id', $employee->id)
                    ->orderBy('pay_date', 'desc')
                    ->take(3)
                    ->get();
            }
        }
        
        return view('selfservice.index', compact('employee', 'recentRequests', 'recentPayslips'));
    }
    
    /**
     * Show leave request form.
     */
    public function leave()
    {
        $currentClient = session('current_client');
        $employee = null;
        
        if ($currentClient) {
            $user = Auth::user();
            $employee = Employee::where('client_id', $currentClient['id'])
                ->where('email', $user->email)
                ->with(['selfServiceRequests' => function($query) {
                    $query->where('request_type', 'leave')->orderBy('created_at', 'desc');
                }])
                ->first();
        }
        
        return view('selfservice.leave', compact('employee'));
    }
    
    /**
     * Store leave request.
     */
    public function storeLeave(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
        ]);
        
        $currentClient = session('current_client');
        $user = Auth::user();
        
        $employee = Employee::where('client_id', $currentClient['id'])
            ->where('email', $user->email)
            ->first();
        
        if (!$employee) {
            return back()->with('error', 'Employee record not found for current client.');
        }
        
        $daysRequested = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) + 1;
        
        SelfService::create([
            'client_id' => $currentClient['id'],
            'employee_id' => $employee->id,
            'request_type' => 'leave',
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_requested' => $daysRequested,
            'status' => 'pending',
            'request_date' => now(),
        ]);
        
        return back()->with('success', 'Leave request submitted successfully!');
    }
    
    /**
     * Show payslip download page.
     */
    public function payslip()
    {
        $currentClient = session('current_client');
        $employee = null;
        $payslips = collect(); // Initialize as empty collection
        
        if ($currentClient) {
            $user = Auth::user();
            $employee = Employee::where('client_id', $currentClient['id'])
                ->where('email', $user->email)
                ->first();
            
            if ($employee) {
                $payslips = Payroll::where('client_id', $currentClient['id'])
                    ->where('employee_id', $employee->id)
                    ->orderBy('pay_date', 'desc')
                    ->get();
            }
        }
        
        return view('selfservice.payslip', compact('employee', 'payslips'));
    }
    
    /**
     * Request payslip.
     */
    public function requestPayslip(Request $request)
    {
        $request->validate([
            'payroll_period' => 'required|string',
        ]);
        
        $currentClient = session('current_client');
        $user = Auth::user();
        
        $employee = Employee::where('client_id', $currentClient['id'])
            ->where('email', $user->email)
            ->first();
        
        if (!$employee) {
            return back()->with('error', 'Employee record not found for current client.');
        }
        
        SelfService::create([
            'client_id' => $currentClient['id'],
            'employee_id' => $employee->id,
            'request_type' => 'payslip',
            'title' => 'Payslip Request - ' . $request->payroll_period,
            'description' => 'Request for payslip for ' . $request->payroll_period,
            'status' => 'pending',
            'request_date' => now(),
        ]);
        
        return back()->with('success', 'Payslip request submitted successfully!');
    }
    
    /**
     * Show contract view page.
     */
    public function contract()
    {
        $currentClient = session('current_client');
        $employee = null;
        
        if ($currentClient) {
            $user = Auth::user();
            $employee = Employee::where('client_id', $currentClient['id'])
                ->where('email', $user->email)
                ->first();
        }
        
        return view('selfservice.contract', compact('employee'));
    }
    
    /**
     * Request contract copy.
     */
    public function requestContract(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);
        
        $currentClient = session('current_client');
        $user = Auth::user();
        
        $employee = Employee::where('client_id', $currentClient['id'])
            ->where('email', $user->email)
            ->first();
        
        if (!$employee) {
            return back()->with('error', 'Employee record not found for current client.');
        }
        
        SelfService::create([
            'client_id' => $currentClient['id'],
            'employee_id' => $employee->id,
            'request_type' => 'contract',
            'title' => 'Employment Contract Copy',
            'description' => 'Request for copy of employment contract: ' . $request->reason,
            'status' => 'pending',
            'request_date' => now(),
        ]);
        
        return back()->with('success', 'Contract request submitted successfully!');
    }
    
    /**
     * Show complaint form.
     */
    public function complaint()
    {
        $currentClient = session('current_client');
        $employee = null;
        
        if ($currentClient) {
            $user = Auth::user();
            $employee = Employee::where('client_id', $currentClient['id'])
                ->where('email', $user->email)
                ->with(['selfServiceRequests' => function($query) {
                    $query->where('request_type', 'complaint')->orderBy('created_at', 'desc');
                }])
                ->first();
        }
        
        return view('selfservice.complaint', compact('employee'));
    }
    
    /**
     * Store complaint.
     */
    public function storeComplaint(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        
        $currentClient = session('current_client');
        $user = Auth::user();
        
        $employee = Employee::where('client_id', $currentClient['id'])
            ->where('email', $user->email)
            ->first();
        
        if (!$employee) {
            return back()->with('error', 'Employee record not found for current client.');
        }
        
        SelfService::create([
            'client_id' => $currentClient['id'],
            'employee_id' => $employee->id,
            'request_type' => 'complaint',
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending',
            'request_date' => now(),
        ]);
        
        return back()->with('success', 'Complaint submitted successfully!');
    }
    
    /**
     * Show profile update form.
     */
    public function profile()
    {
        $user = Auth::user();
        $currentClient = session('current_client');
        $employee = null;
        
        if ($currentClient) {
            $employee = Employee::where('client_id', $currentClient['id'])
                ->where('email', $user->email)
                ->first();
        }
        
        return view('selfservice.profile', compact('user', 'employee'));
    }
    
    /**
     * Update profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        // Update user profile
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
        ]);
        
        // Update employee record if exists
        $currentClient = session('current_client');
        if ($currentClient) {
            $employee = Employee::where('client_id', $currentClient['id'])
                ->where('email', $user->email)
                ->first();
            
            if ($employee) {
                $employee->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'address' => $request->address,
                    'city' => $request->city,
                    'country' => $request->country,
                    'postal_code' => $request->postal_code,
                    'emergency_contact_name' => $request->emergency_contact_name,
                    'emergency_contact_phone' => $request->emergency_contact_phone,
                ]);
            }
        }
        
        return back()->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Show expense claim form.
     */
    public function expense()
    {
        $currentClient = session('current_client');
        $employee = null;
        
        if ($currentClient) {
            $user = Auth::user();
            $employee = Employee::where('client_id', $currentClient['id'])
                ->where('email', $user->email)
                ->first();
        }
        
        return view('selfservice.expense', compact('employee'));
    }
    
    /**
     * Store expense claim.
     */
    public function storeExpense(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);
        
        $currentClient = session('current_client');
        $user = Auth::user();
        
        $employee = Employee::where('client_id', $currentClient['id'])
            ->where('email', $user->email)
            ->first();
        
        if (!$employee) {
            return back()->with('error', 'Employee record not found for current client.');
        }
        
        SelfService::create([
            'client_id' => $currentClient['id'],
            'employee_id' => $employee->id,
            'request_type' => 'expense_claim',
            'title' => $request->title,
            'description' => $request->description,
            'amount' => $request->amount,
            'status' => 'pending',
            'request_date' => now(),
        ]);
        
        return back()->with('success', 'Expense claim submitted successfully!');
    }
}
