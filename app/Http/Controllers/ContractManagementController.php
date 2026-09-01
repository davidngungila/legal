<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogger;
use App\Models\Client;
use App\Models\Employee;
use App\Models\EmploymentContract;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContractManagementController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('employment_contract.view');
        $clientId = session('current_client_id');
        if (! $clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);

        $query = EmploymentContract::with(['employee'])
            ->where('client_id', $clientId);

        if ($request->filled('search')) {
            $query->search($request->get('search'));
        }

        if ($request->filled('status') && $request->get('status') !== 'all') {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('contract_type') && $request->get('contract_type') !== 'all') {
            $query->where('contract_type', $request->get('contract_type'));
        }

        $sortField = in_array($request->get('sort'), ['effective_date', 'expiry_date', 'basic_salary', 'created_at'])
            ? $request->get('sort')
            : 'created_at';
        $sortDir = $request->get('dir') === 'asc' ? 'asc' : 'desc';

        $contracts = $query->orderBy($sortField, $sortDir)->paginate(12)->withQueryString();

        $stats = EmploymentContract::getContractStats();
        $attention = EmploymentContract::getRequiringAttention();
        $events = EmploymentContract::getCalendarEvents();

        return view('hris.contract-management.index', compact(
            'currentClient', 'contracts', 'stats', 'attention', 'events'
        ));
    }

    public function employeeContracts(Request $request)
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return response()->json(['success' => false, 'message' => 'Please select a client first.'], 422);
        }

        $employeeId = $request->get('employee_id');
        if ($employeeId) {
            $employee = Employee::where('client_id', $clientId)->find($employeeId);
            if (! $employee) {
                return response()->json(['success' => false, 'message' => 'Employee not found.'], 404);
            }

            $contracts = EmploymentContract::with(['employee'])
                ->where('client_id', $clientId)
                ->where('employee_id', $employee->id)
                ->orderByDesc('effective_date')
                ->get();
        } else {
            $contracts = EmploymentContract::with(['employee'])
                ->where('client_id', $clientId)
                ->orderByDesc('created_at')
                ->get();
        }

        return response()->json(['success' => true, 'contracts' => $contracts]);
    }

    public function statistics()
    {
        return response()->json([
            'success' => true,
            'statistics' => EmploymentContract::getContractStats(),
        ]);
    }

    public function requiringAttention()
    {
        return response()->json([
            'success' => true,
            'attention' => EmploymentContract::getRequiringAttention(),
        ]);
    }

    public function calendar()
    {
        return response()->json([
            'success' => true,
            'events' => EmploymentContract::getCalendarEvents(),
        ]);
    }

    public function generateReport(Request $request)
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $status = $request->get('status');
        $format = $request->get('format', 'pdf');

        $query = EmploymentContract::with(['employee'])
            ->where('client_id', $clientId)
            ->orderBy('expiry_date');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $contracts = $query->get();
        $stats = EmploymentContract::getContractStats();
        $currentClient = Client::find($clientId);

        if ($format === 'pdf') {
            $reportStatus = $status ?: 'all';

            AuditLogger::log(
                'employment_contract.report_generated',
                null,
                'Employment Contracts',
                "Contract management report generated ({$reportStatus})"
            );

            $pdf = Pdf::loadView('hris.employment-contracts.pdf-report', compact('contracts', 'stats', 'currentClient'))
                ->setPaper('a4')
                ->setOption('margin-top', '16mm')
                ->setOption('margin-bottom', '18mm')
                ->setOption('margin-left', '14mm')
                ->setOption('margin-right', '14mm');

            return $pdf->download('employment-contracts-report-' . now()->format('Y-m-d') . '.pdf');
        }

        return back()->with('error', 'Unsupported report format.');
    }

    public function activate(EmploymentContract $contract)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $contract->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        try {
            $old = $contract->toArray();
            $contract->update([
                'status' => 'active',
                'activated_at' => now(),
                'updated_by' => auth()->id(),
            ]);

            AuditLogger::log(
                'employment_contract.activated',
                $contract,
                'Employment Contracts',
                "Employment contract {$contract->formatted_contract_number} activated",
                $old,
                $contract->toArray()
            );

            return back()->with('success', 'Employment contract activated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to activate employment contract: ' . $e->getMessage());
        }
    }

    public function terminate(Request $request, EmploymentContract $contract)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $contract->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), [
            'termination_date' => 'required|date|before_or_equal:today',
            'termination_reason' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $old = $contract->toArray();
            $contract->update([
                'status' => 'terminated',
                'terminated_at' => now()->setDateFrom($request->termination_date),
                'termination_reason' => $request->termination_reason,
                'notes' => ($contract->notes ? $contract->notes . "\n" : '')
                    . "Terminated {$request->termination_date} - {$request->termination_reason}",
                'updated_by' => auth()->id(),
            ]);

            AuditLogger::log(
                'employment_contract.terminated',
                $contract,
                'Employment Contracts',
                "Employment contract {$contract->formatted_contract_number} terminated",
                $old,
                $contract->toArray()
            );

            return back()->with('success', 'Employment contract terminated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to terminate employment contract: ' . $e->getMessage());
        }
    }

    public function renew(Request $request, EmploymentContract $contract)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $contract->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), [
            'new_effective_date' => 'required|date',
            'new_expiry_date' => 'nullable|date|after:new_effective_date',
            'renewal_reason' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $old = $contract->toArray();
            $contract->update([
                'status' => 'renewed',
                'effective_date' => $request->new_effective_date,
                'expiry_date' => $request->new_expiry_date ?: $contract->expiry_date,
                'renewal_count' => $contract->renewal_count + 1,
                'last_renewal_date' => now()->toDateString(),
                'notes' => ($contract->notes ? $contract->notes . "\n" : '')
                    . "Renewed on " . now()->format('Y-m-d') . " - {$request->renewal_reason}",
                'updated_by' => auth()->id(),
            ]);

            AuditLogger::log(
                'employment_contract.renewed',
                $contract,
                'Employment Contracts',
                "Employment contract {$contract->formatted_contract_number} renewed (count {$contract->renewal_count})",
                $old,
                $contract->toArray()
            );

            return back()->with('success', 'Employment contract renewed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to renew employment contract: ' . $e->getMessage());
        }
    }
}
