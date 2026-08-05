<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogger;
use App\Models\Client;
use App\Models\TrainingPlan;
use App\Models\TrainingSession;
use App\Models\TrainingEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrainingPlanController extends Controller
{
    public function index(Request $request)
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);

        $query = TrainingPlan::withCount('sessions')
            ->where('client_id', $clientId);

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $plans = $query->orderByDesc('created_at')->paginate(10);

        $stats = [
            'total' => TrainingPlan::where('client_id', $clientId)->count(),
            'draft' => TrainingPlan::where('client_id', $clientId)->where('status', 'draft')->count(),
            'approved' => TrainingPlan::where('client_id', $clientId)->where('status', 'approved')->count(),
            'completed' => TrainingPlan::where('client_id', $clientId)->where('status', 'completed')->count(),
            'total_budget' => TrainingPlan::where('client_id', $clientId)->sum('budget'),
        ];

        $departments = \App\Models\Employee::where('client_id', $clientId)
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->select('department')->distinct()->orderBy('department')->pluck('department')->values()->all();

        return view('training.plans', compact('currentClient', 'plans', 'stats', 'departments'));
    }

    public function store(Request $request)
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_department' => 'nullable|string|max:255',
            'target_category' => 'nullable|string|max:255',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
            'budget' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'status' => 'nullable|string|in:draft,approved,completed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $plan = TrainingPlan::create([
                'client_id' => $clientId,
                'name' => $request->name,
                'description' => $request->description,
                'target_department' => $request->target_department,
                'target_category' => $request->target_category,
                'period_start' => $request->period_start,
                'period_end' => $request->period_end,
                'budget' => $request->budget ?: 0,
                'currency' => $request->currency ?: 'TZS',
                'status' => $request->status ?: 'draft',
                'created_by' => auth()->id(),
            ]);

            AuditLogger::log(
                'training_plan.created',
                $plan,
                'Training Plans',
                "Training plan created: {$plan->name}"
            );

            return redirect()->route('training.plans')->with('success', 'Training plan created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create training plan: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, TrainingPlan $plan)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $plan->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_department' => 'nullable|string|max:255',
            'target_category' => 'nullable|string|max:255',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
            'budget' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'status' => 'nullable|string|in:draft,approved,completed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $old = $plan->toArray();
            $plan->update([
                'name' => $request->name,
                'description' => $request->description,
                'target_department' => $request->target_department,
                'target_category' => $request->target_category,
                'period_start' => $request->period_start,
                'period_end' => $request->period_end,
                'budget' => $request->budget ?: 0,
                'currency' => $request->currency ?: 'TZS',
                'status' => $request->status ?: 'draft',
                'updated_by' => auth()->id(),
            ]);

            AuditLogger::log(
                'training_plan.updated',
                $plan,
                'Training Plans',
                "Training plan updated: {$plan->name}",
                $old,
                $plan->toArray()
            );

            return redirect()->route('training.plans')->with('success', 'Training plan updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update training plan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(TrainingPlan $plan)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $plan->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        try {
            if (TrainingSession::where('plan_id', $plan->id)->exists()) {
                return back()->with('error', 'Cannot delete a training plan that has sessions. Delete its sessions first.');
            }

            AuditLogger::log(
                'training_plan.deleted',
                $plan,
                'Training Plans',
                "Training plan deleted: {$plan->name}"
            );
            $plan->delete();

            return redirect()->route('training.plans')->with('success', 'Training plan deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete training plan: ' . $e->getMessage());
        }
    }
}
