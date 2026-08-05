<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogger;
use App\Models\Client;
use App\Models\PerformanceCycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PerformanceCycleController extends Controller
{
    public function index()
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);

        $cycles = PerformanceCycle::withCount(['employeeGoals', 'performanceReviews'])
            ->where('client_id', $clientId)
            ->orderBy('period_start', 'desc')
            ->paginate(10);

        $stats = [
            'total' => PerformanceCycle::where('client_id', $clientId)->count(),
            'active' => PerformanceCycle::where('client_id', $clientId)->where('status', 'active')->count(),
            'draft' => PerformanceCycle::where('client_id', $clientId)->where('status', 'draft')->count(),
            'closed' => PerformanceCycle::where('client_id', $clientId)->where('status', 'closed')->count(),
        ];

        return view('performance.cycles.index', compact('currentClient', 'cycles', 'stats'));
    }

    public function store(Request $request)
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $validator = Validator::make($request->all(), [
            'cycle_name' => 'required|string|max:255',
            'cycle_type' => 'required|string|max:50',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'employee_category' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:draft,active,closed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $cycle = PerformanceCycle::create([
                'client_id' => $clientId,
                'cycle_name' => $request->cycle_name,
                'cycle_type' => $request->cycle_type,
                'period_start' => $request->period_start,
                'period_end' => $request->period_end,
                'employee_category' => $request->employee_category,
                'status' => $request->status ?: 'draft',
            ]);

            AuditLogger::log(
                'performance_cycle.created',
                $cycle,
                'Performance',
                "Performance cycle created: {$cycle->cycle_name}"
            );

            return redirect()->route('performance.cycles.index')->with('success', 'Performance cycle created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create performance cycle: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, PerformanceCycle $cycle)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $cycle->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), [
            'cycle_name' => 'required|string|max:255',
            'cycle_type' => 'required|string|max:50',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'employee_category' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:draft,active,closed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $old = $cycle->toArray();
            $cycle->update([
                'cycle_name' => $request->cycle_name,
                'cycle_type' => $request->cycle_type,
                'period_start' => $request->period_start,
                'period_end' => $request->period_end,
                'employee_category' => $request->employee_category,
                'status' => $request->status ?: 'draft',
            ]);

            AuditLogger::log(
                'performance_cycle.updated',
                $cycle,
                'Performance',
                "Performance cycle updated: {$cycle->cycle_name}",
                $old,
                $cycle->toArray()
            );

            return redirect()->route('performance.cycles.index')->with('success', 'Performance cycle updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update performance cycle: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(PerformanceCycle $cycle)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $cycle->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        try {
            AuditLogger::log(
                'performance_cycle.deleted',
                $cycle,
                'Performance',
                "Performance cycle deleted: {$cycle->cycle_name}"
            );
            $cycle->delete();

            return redirect()->route('performance.cycles.index')->with('success', 'Performance cycle deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete performance cycle: ' . $e->getMessage());
        }
    }
}
