<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Employee;

class ContractController
{
    /**
     * Display contracts index
     */
    public function index()
    {
        $clientId = session('current_client_id');
        $contracts = Contract::where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('contracts.index', compact('contracts'));
    }

    /**
     * Show create contract form
     */
    public function create()
    {
        $clientId = session('current_client_id');
        $employees = Employee::where('client_id', $clientId)->get();
        
        return view('contracts.create', compact('employees'));
    }

    /**
     * Store new contract
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'contract_type' => 'required|in:permanent,fixed_term,probation,internship,consultant,part_time,temporary',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'probation_end_date' => 'nullable|date|after:start_date',
            'salary' => 'required|numeric|min:0',
            'currency' => 'required|string',
            'payment_frequency' => 'required|in:monthly,bi-weekly,weekly',
            'work_schedule' => 'nullable|string',
            'work_location' => 'nullable|string',
            'job_description' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
        ]);

        $clientId = session('current_client_id');
        $lastContract = Contract::where('client_id', $clientId)->orderBy('id', 'desc')->first();
        $contractNumber = 'CNT' . str_pad($clientId, 2, '0', STR_PAD_LEFT) . str_pad(($lastContract ? $lastContract->id + 1 : 1), 3, '0', STR_PAD_LEFT);

        Contract::create([
            'client_id' => $clientId,
            'employee_id' => $request->employee_id,
            'contract_number' => $contractNumber,
            'contract_type' => $request->contract_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'probation_end_date' => $request->probation_end_date,
            'salary' => $request->salary,
            'currency' => $request->currency,
            'payment_frequency' => $request->payment_frequency,
            'work_schedule' => $request->work_schedule,
            'work_location' => $request->work_location,
            'job_description' => $request->job_description,
            'responsibilities' => $request->responsibilities ? json_encode($request->responsibilities) : null,
            'terms_and_conditions' => $request->terms_and_conditions,
            'status' => 'draft',
        ]);

        return redirect()->route('contracts.index')
            ->with('success', 'Contract created successfully!');
    }

    /**
     * Show contract details
     */
    public function show(Contract $contract)
    {
        $this->authorizeContractAccess($contract);
        return view('contracts.show', compact('contract'));
    }

    /**
     * Show edit contract form
     */
    public function edit(Contract $contract)
    {
        $this->authorizeContractAccess($contract);
        $clientId = session('current_client_id');
        $employees = Employee::where('client_id', $clientId)->get();
        
        return view('contracts.edit', compact('contract', 'employees'));
    }

    /**
     * Update contract
     */
    public function update(Request $request, Contract $contract)
    {
        $this->authorizeContractAccess($contract);

        $request->validate([
            'contract_type' => 'required|in:permanent,fixed_term,probation,internship,consultant,part_time,temporary',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'probation_end_date' => 'nullable|date|after:start_date',
            'salary' => 'required|numeric|min:0',
            'currency' => 'required|string',
            'payment_frequency' => 'required|in:monthly,bi-weekly,weekly',
            'work_schedule' => 'nullable|string',
            'work_location' => 'nullable|string',
            'job_description' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
        ]);

        $contract->update($request->all());

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contract updated successfully!');
    }

    /**
     * Delete contract
     */
    public function destroy(Contract $contract)
    {
        $this->authorizeContractAccess($contract);
        $contract->delete();

        return redirect()->route('contracts.index')
            ->with('success', 'Contract deleted successfully!');
    }

    /**
     * Sign contract
     */
    public function sign(Request $request, Contract $contract)
    {
        $this->authorizeContractAccess($contract);

        $request->validate([
            'signature_type' => 'required|in:digital,manual',
            'signature_data' => 'required_if:signature_type,digital',
        ]);

        $contract->update([
            'status' => 'signed',
            'signed_at' => now(),
            'signature_type' => $request->signature_type,
            'signature_data' => $request->signature_data,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contract signed successfully!'
        ]);
    }

    /**
     * Terminate contract
     */
    public function terminate(Request $request, Contract $contract)
    {
        $this->authorizeContractAccess($contract);

        $request->validate([
            'termination_reason' => 'required|string',
            'termination_date' => 'required|date',
        ]);

        $contract->update([
            'status' => 'terminated',
            'terminated_at' => now(),
            'termination_reason' => $request->termination_reason,
            'termination_date' => $request->termination_date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contract terminated successfully!'
        ]);
    }

    /**
     * Renew contract
     */
    public function renew(Request $request, Contract $contract)
    {
        $this->authorizeContractAccess($contract);

        $request->validate([
            'new_end_date' => 'required|date|after:' . $contract->end_date,
            'new_salary' => 'required|numeric|min:0',
        ]);

        $contract->update([
            'end_date' => $request->new_end_date,
            'salary' => $request->new_salary,
            'renewal_count' => $contract->renewal_count + 1,
            'last_renewal_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contract renewed successfully!'
        ]);
    }

    /**
     * Download contract
     */
    public function download(Contract $contract)
    {
        $this->authorizeContractAccess($contract);
        
        // Generate PDF download
        return response()->download(storage_path('app/contracts/' . $contract->contract_number . '.pdf'));
    }

    /**
     * Get contracts expiring soon
     */
    public function expiringSoon()
    {
        $clientId = session('current_client_id');
        $contracts = Contract::where('client_id', $clientId)
            ->where('end_date', '<=', now()->addDays(90))
            ->where('end_date', '>=', now())
            ->where('status', '!=', 'terminated')
            ->orderBy('end_date', 'asc')
            ->get();

        return response()->json($contracts);
    }

    /**
     * Get contract statistics
     */
    public function statistics()
    {
        $clientId = session('current_client_id');
        
        $stats = [
            'total_contracts' => Contract::where('client_id', $clientId)->count(),
            'active_contracts' => Contract::where('client_id', $clientId)->where('status', 'active')->count(),
            'expiring_soon' => Contract::where('client_id', $clientId)
                ->where('end_date', '<=', now()->addDays(30))
                ->where('end_date', '>=', now())
                ->count(),
            'terminated_contracts' => Contract::where('client_id', $clientId)->where('status', 'terminated')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Authorize contract access
     */
    private function authorizeContractAccess(Contract $contract)
    {
        if ($contract->client_id !== session('current_client_id')) {
            abort(403, 'Unauthorized access to this contract record.');
        }
    }
}
