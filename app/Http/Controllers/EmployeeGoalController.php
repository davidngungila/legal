<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogger;
use App\Models\Client;
use App\Models\Employee;
use App\Models\EmployeeGoal;
use App\Models\Kpi;
use App\Models\PerformanceCycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeGoalController extends Controller
{
    public function index(Request $request)
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);

        $query = EmployeeGoal::with(['employee', 'cycle', 'kpis', 'approvedBy'])
            ->where('client_id', $clientId);

        if ($request->filled('cycle_id')) {
            $query->where('cycle_id', $request->get('cycle_id'));
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->get('employee_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $goals = $query->orderBy('created_at', 'desc')->paginate(10);

        $cycles = PerformanceCycle::where('client_id', $clientId)->orderBy('period_start', 'desc')->get();
        $employees = Employee::where('client_id', $clientId)->where('status', 'active')->orderBy('first_name')->get();

        $stats = [
            'total' => EmployeeGoal::where('client_id', $clientId)->count(),
            'approved' => EmployeeGoal::where('client_id', $clientId)->where('status', 'approved')->count(),
            'submitted' => EmployeeGoal::where('client_id', $clientId)->where('status', 'submitted')->count(),
            'draft' => EmployeeGoal::where('client_id', $clientId)->where('status', 'draft')->count(),
        ];

        return view('performance.goals.index', compact('currentClient', 'goals', 'cycles', 'employees', 'stats'));
    }

    public function store(Request $request)
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'cycle_id' => 'nullable|exists:performance_cycles,id',
            'goal_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:draft,submitted,approved',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            Employee::where('client_id', $clientId)->findOrFail($request->employee_id);

            $goal = EmployeeGoal::create([
                'client_id' => $clientId,
                'employee_id' => $request->employee_id,
                'cycle_id' => $request->cycle_id ?: null,
                'goal_title' => $request->goal_title,
                'description' => $request->description,
                'kpi_count' => 0,
                'weight_total' => 0,
                'status' => $request->status ?: 'draft',
            ]);

            AuditLogger::log(
                'goal.created',
                $goal,
                'Performance Goals',
                "Goal created: {$goal->goal_title}"
            );

            return redirect()->route('performance.goals.index')->with('success', 'Goal created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create goal: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, EmployeeGoal $goal)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $goal->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), [
            'goal_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:draft,submitted,approved',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $old = $goal->toArray();
            $goal->update([
                'goal_title' => $request->goal_title,
                'description' => $request->description,
                'status' => $request->status ?: 'draft',
                'approved_by' => $request->status === 'approved' ? ($goal->approved_by ?: auth()->id()) : null,
            ]);

            AuditLogger::log(
                'goal.updated',
                $goal,
                'Performance Goals',
                "Goal updated: {$goal->goal_title}",
                $old,
                $goal->toArray()
            );

            return redirect()->route('performance.goals.index')->with('success', 'Goal updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update goal: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(EmployeeGoal $goal)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $goal->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        try {
            AuditLogger::log(
                'goal.deleted',
                $goal,
                'Performance Goals',
                "Goal deleted: {$goal->goal_title}"
            );
            $goal->delete();

            return redirect()->route('performance.goals.index')->with('success', 'Goal deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete goal: ' . $e->getMessage());
        }
    }

    public function storeKpi(Request $request, EmployeeGoal $goal)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $goal->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), [
            'kpi_description' => 'required|string',
            'target' => 'nullable|string|max:255',
            'weight' => 'required|numeric|min:0|max:100',
            'measurement_unit' => 'nullable|string|max:100',
            'deadline' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $existingWeight = (float) $goal->kpis()->sum('weight');
            if ($existingWeight + (float) $request->weight > 100) {
                return back()->with('error', 'Total KPI weight cannot exceed 100%. Current total is ' . $existingWeight . '%.');
            }

            $kpi = $goal->kpis()->create([
                'kpi_description' => $request->kpi_description,
                'target' => $request->target,
                'weight' => $request->weight,
                'measurement_unit' => $request->measurement_unit,
                'deadline' => $request->deadline,
            ]);

            $goal->update([
                'kpi_count' => $goal->kpis()->count(),
                'weight_total' => $goal->kpis()->sum('weight'),
            ]);

            AuditLogger::log(
                'kpi.created',
                $kpi,
                'Performance Goals',
                "KPI created for goal: {$goal->goal_title}"
            );

            return redirect()->route('performance.goals.index', ['expand' => $goal->id])->with('success', 'KPI added successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add KPI: ' . $e->getMessage())->withInput();
        }
    }

    public function updateKpi(Request $request, EmployeeGoal $goal, Kpi $kpi)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $goal->client_id != $clientId || $kpi->goal_id != $goal->id) {
            return back()->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), [
            'kpi_description' => 'required|string',
            'target' => 'nullable|string|max:255',
            'weight' => 'required|numeric|min:0|max:100',
            'measurement_unit' => 'nullable|string|max:100',
            'deadline' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $otherWeight = (float) $goal->kpis()->where('id', '!=', $kpi->id)->sum('weight');
            if ($otherWeight + (float) $request->weight > 100) {
                return back()->with('error', 'Total KPI weight cannot exceed 100%. Current total is ' . $otherWeight . '%.');
            }

            $old = $kpi->toArray();
            $kpi->update([
                'kpi_description' => $request->kpi_description,
                'target' => $request->target,
                'weight' => $request->weight,
                'measurement_unit' => $request->measurement_unit,
                'deadline' => $request->deadline,
            ]);

            $goal->update([
                'kpi_count' => $goal->kpis()->count(),
                'weight_total' => $goal->kpis()->sum('weight'),
            ]);

            AuditLogger::log(
                'kpi.updated',
                $kpi,
                'Performance Goals',
                "KPI updated for goal: {$goal->goal_title}",
                $old,
                $kpi->toArray()
            );

            return redirect()->route('performance.goals.index', ['expand' => $goal->id])->with('success', 'KPI updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update KPI: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyKpi(EmployeeGoal $goal, Kpi $kpi)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $goal->client_id != $clientId || $kpi->goal_id != $goal->id) {
            return back()->with('error', 'Invalid request.');
        }

        try {
            AuditLogger::log(
                'kpi.deleted',
                $kpi,
                'Performance Goals',
                "KPI deleted for goal: {$goal->goal_title}"
            );
            $kpi->delete();

            $goal->update([
                'kpi_count' => $goal->kpis()->count(),
                'weight_total' => $goal->kpis()->sum('weight'),
            ]);

            return redirect()->route('performance.goals.index', ['expand' => $goal->id])->with('success', 'KPI deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete KPI: ' . $e->getMessage());
        }
    }
}
