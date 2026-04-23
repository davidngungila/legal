<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\Swift\Flash;

class ContractController extends Controller
{
    /**
     * Display a listing of the contracts.
     */
    public function index()
    {
        $currentClient = session('current_client');
        $currentUser = Auth::user();
        
        // Get contracts for current client
        $contracts = Contract::forCurrentClient()
            ->with(['employee', 'client'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get statistics
        $stats = Contract::getContractStats();

        return view('contracts.index', compact('contracts', 'stats', 'currentClient', 'currentUser'));
    }

    /**
     * Show the form for creating a new contract.
     */
    public function create()
    {
        $currentClient = session('current_client');
        
        // Get employees for current client
        $employees = Employee::forCurrentClient()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $contractTypes = Contract::CONTRACT_TYPES;
        $statuses = Contract::STATUSES;

        return view('contracts.create', compact('currentClient', 'employees', 'contractTypes', 'statuses'));
    }

    /**
     * Store a newly created contract in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'employee_id' => 'required|exists:employees,id',
            'contract_type' => 'required|in:permanent,fixed_term,probation,internship,consultant,part_time,temporary',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'probation_end_date' => 'nullable|date|after:start_date|before:end_date',
            'salary' => 'required|numeric|min:0',
            'currency' => 'required|in:TZS,USD,EUR,GBP',
            'payment_frequency' => 'required|in:monthly,bi-weekly,weekly',
            'work_schedule' => 'nullable|string|max:255',
            'work_location' => 'nullable|string|max:255',
            'job_description' => 'nullable|string|max:2000',
            'responsibilities' => 'nullable|array',
            'terms_and_conditions' => 'nullable|array',
            'status' => 'required|in:draft,pending_signature,active,probation,suspended,terminated,expired,renewed',
            'auto_renewal' => 'boolean',
        ]);

        // Add created by user
        $validated['created_by'] = Auth::id();

        $contract = Contract::create($validated);

        Flash::success('Contract created successfully!');
        
        return redirect()->route('contracts.index')
                     ->with('success', 'Contract "' . $contract->formatted_contract_number . '" has been created successfully.');
    }

    /**
     * Display the specified contract.
     */
    public function show(Contract $contract)
    {
        // Verify contract belongs to current client
        $currentClient = session('current_client');
        if ($contract->client_id != $currentClient->id) {
            abort(403, 'Unauthorized access to contract record.');
        }

        $contract->load(['employee', 'client', 'amendments']);

        return view('contracts.show', compact('contract', 'currentClient'));
    }

    /**
     * Show the form for editing the specified contract.
     */
    public function edit(Contract $contract)
    {
        // Verify contract belongs to current client
        $currentClient = session('current_client');
        if ($contract->client_id != $currentClient->id) {
            abort(403, 'Unauthorized access to contract record.');
        }

        $employees = Employee::forCurrentClient()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $contractTypes = Contract::CONTRACT_TYPES;
        $statuses = Contract::STATUSES;

        return view('contracts.edit', compact('contract', 'currentClient', 'employees', 'contractTypes', 'statuses'));
    }

    /**
     * Update the specified contract in storage.
     */
    public function update(Request $request, Contract $contract)
    {
        // Verify contract belongs to current client
        $currentClient = session('current_client');
        if ($contract->client_id != $currentClient->id) {
            abort(403, 'Unauthorized access to contract record.');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'contract_type' => 'required|in:permanent,fixed_term,probation,internship,consultant,part_time,temporary',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'probation_end_date' => 'nullable|date|after:start_date|before:end_date',
            'salary' => 'required|numeric|min:0',
            'currency' => 'required|in:TZS,USD,EUR,GBP',
            'payment_frequency' => 'required|in:monthly,bi-weekly,weekly',
            'work_schedule' => 'nullable|string|max:255',
            'work_location' => 'nullable|string|max:255',
            'job_description' => 'nullable|string|max:2000',
            'responsibilities' => 'nullable|array',
            'terms_and_conditions' => 'nullable|array',
            'status' => 'required|in:draft,pending_signature,active,probation,suspended,terminated,expired,renewed',
            'auto_renewal' => 'boolean',
        ]);

        // Add updated by user
        $validated['updated_by'] = Auth::id();

        $contract->update($validated);

        Flash::success('Contract updated successfully!');
        
        return redirect()->route('contracts.index')
                     ->with('success', 'Contract "' . $contract->formatted_contract_number . '" has been updated successfully.');
    }

    /**
     * Remove the specified contract from storage.
     */
    public function destroy(Contract $contract)
    {
        // Verify contract belongs to current client
        $currentClient = session('current_client');
        if ($contract->client_id != $currentClient->id) {
            abort(403, 'Unauthorized access to contract record.');
        }

        $contract->delete();

        Flash::success('Contract deleted successfully!');
        
        return redirect()->route('contracts.index')
                     ->with('success', 'Contract "' . $contract->formatted_contract_number . '" has been deleted successfully.');
    }

    /**
     * Sign the specified contract.
     */
    public function sign(Request $request, Contract $contract)
    {
        // Verify contract belongs to current client
        $currentClient = session('current_client');
        if ($contract->client_id != $currentClient->id) {
            abort(403, 'Unauthorized access to contract record.');
        }

        $validated = $request->validate([
            'signature_data' => 'required|string',
            'signature_type' => 'required|in:employee,employer',
        ]);

        $contract->update([
            'status' => 'active',
            'signed_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        Flash::success('Contract signed successfully!');
        
        return redirect()->route('contracts.show', $contract->id)
                     ->with('success', 'Contract has been signed successfully.');
    }

    /**
     * Terminate the specified contract.
     */
    public function terminate(Request $request, Contract $contract)
    {
        // Verify contract belongs to current client
        $currentClient = session('current_client');
        if ($contract->client_id != $currentClient->id) {
            abort(403, 'Unauthorized access to contract record.');
        }

        $validated = $request->validate([
            'termination_reason' => 'required|string|max:255',
        ]);

        $contract->update([
            'status' => 'terminated',
            'terminated_at' => now(),
            'termination_reason' => $validated['termination_reason'],
            'updated_by' => Auth::id(),
        ]);

        Flash::success('Contract terminated successfully!');
        
        return redirect()->route('contracts.show', $contract->id)
                     ->with('success', 'Contract has been terminated successfully.');
    }

    /**
     * Renew the specified contract.
     */
    public function renew(Request $request, Contract $contract)
    {
        // Verify contract belongs to current client
        $currentClient = session('current_client');
        if ($contract->client_id != $currentClient->id) {
            abort(403, 'Unauthorized access to contract record.');
        }

        $validated = $request->validate([
            'new_end_date' => 'required|date|after:end_date',
        ]);

        $contract->update([
            'end_date' => $validated['new_end_date'],
            'renewal_count' => $contract->renewal_count + 1,
            'last_renewal_date' => now(),
            'status' => 'renewed',
            'updated_by' => Auth::id(),
        ]);

        Flash::success('Contract renewed successfully!');
        
        return redirect()->route('contracts.show', $contract->id)
                     ->with('success', 'Contract has been renewed successfully.');
    }

    /**
     * Download the specified contract.
     */
    public function download(Contract $contract)
    {
        // Verify contract belongs to current client
        $currentClient = session('current_client');
        if ($contract->client_id != $currentClient->id) {
            abort(403, 'Unauthorized access to contract record.');
        }

        // Generate contract content
        $content = $contract->generateContractContent();
        
        // Create PDF or text file
        $filename = 'contract_' . $contract->formatted_contract_number . '_' . date('Y-m-d') . '.txt';
        
        return response()->streamDownload(function() use ($content) {
            echo $content;
        }, $filename);
    }

    /**
     * Get contracts expiring soon.
     */
    public function expiringSoon()
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'No client selected'], 400);
        }

        $contracts = Contract::getExpiringSoonContracts(30);

        return response()->json([
            'contracts' => $contracts,
            'count' => $contracts->count(),
        ]);
    }

    /**
     * Get contract statistics.
     */
    public function statistics()
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'No client selected'], 400);
        }

        $stats = Contract::getContractStats();
        $expiringContracts = Contract::getExpiringSoonContracts(30);

        return response()->json([
            'stats' => $stats,
            'expiring_contracts' => $expiringContracts,
        ]);
    }

    /**
     * Generate contract document.
     */
    public function generateDocument(Contract $contract)
    {
        // Verify contract belongs to current client
        $currentClient = session('current_client');
        if ($contract->client_id != $currentClient->id) {
            abort(403, 'Unauthorized access to contract record.');
        }

        // Generate contract content
        $content = $contract->generateContractContent();
        
        // Create PDF using a library like DomPDF or similar
        // For now, return as text
        $filename = 'contract_' . $contract->formatted_contract_number . '_' . date('Y-m-d') . '.txt';
        
        return response()->streamDownload(function() use ($content) {
            echo $content;
        }, $filename);
    }

    /**
     * Preview contract document.
     */
    public function preview(Contract $contract)
    {
        // Verify contract belongs to current client
        $currentClient = session('current_client');
        if ($contract->client_id != $currentClient->id) {
            abort(403, 'Unauthorized access to contract record.');
        }

        // Generate contract content
        $content = $contract->generateContractContent();

        return response()->json([
            'content' => $content,
            'contract' => $contract,
        ]);
    }
}
