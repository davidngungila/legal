<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogger;
use App\Models\Client;
use App\Models\Employee;
use App\Models\PerformanceImprovementPlan;
use App\Models\PerformanceReview;
use App\Models\PipReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PerformancePipController extends Controller
{
    public function index(Request $request)
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);

        $query = PerformanceImprovementPlan::with(['employee', 'triggerAppraisal', 'pipReviews.reviewer'])
            ->where('client_id', $clientId);

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $plans = $query->orderBy('created_at', 'desc')->paginate(10);

        $employees = Employee::where('client_id', $clientId)->where('status', 'active')->orderBy('first_name')->get();
        $reviews = PerformanceReview::with('employee')
            ->where('client_id', $clientId)
            ->whereIn('status', ['completed', 'finalized'])
            ->orderBy('review_date', 'desc')
            ->get();

        $stats = [
            'total' => PerformanceImprovementPlan::where('client_id', $clientId)->count(),
            'active' => PerformanceImprovementPlan::where('client_id', $clientId)->where('status', 'active')->count(),
            'completed' => PerformanceImprovementPlan::where('client_id', $clientId)->where('status', 'completed')->count(),
            'terminated' => PerformanceImprovementPlan::where('client_id', $clientId)->where('status', 'terminated')->count(),
        ];

        return view('performance.pip.index', compact('currentClient', 'plans', 'employees', 'reviews', 'stats'));
    }

    public function store(Request $request)
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'trigger_appraisal_id' => 'nullable|exists:performance_reviews,id',
            'pip_objectives' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'review_frequency' => 'nullable|string|max:50',
            'status' => 'nullable|string|in:active,completed,terminated',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            Employee::where('client_id', $clientId)->findOrFail($request->employee_id);

            $plan = PerformanceImprovementPlan::create([
                'client_id' => $clientId,
                'employee_id' => $request->employee_id,
                'trigger_appraisal_id' => $request->trigger_appraisal_id ?: null,
                'pip_objectives' => $request->pip_objectives,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'review_frequency' => $request->review_frequency ?: 'biweekly',
                'status' => $request->status ?: 'active',
                'outcome' => null,
            ]);

            AuditLogger::log(
                'pip.created',
                $plan,
                'Performance PIP',
                "PIP created for employee #{$plan->employee_id}"
            );

            return redirect()->route('performance.pip.index')->with('success', 'Performance Improvement Plan created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create PIP: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, PerformanceImprovementPlan $plan)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $plan->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), [
            'pip_objectives' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'review_frequency' => 'nullable|string|max:50',
            'status' => 'nullable|string|in:active,completed,terminated',
            'outcome' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $old = $plan->toArray();
            $plan->update([
                'pip_objectives' => $request->pip_objectives,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'review_frequency' => $request->review_frequency ?: 'biweekly',
                'status' => $request->status ?: 'active',
                'outcome' => $request->outcome,
            ]);

            AuditLogger::log(
                'pip.updated',
                $plan,
                'Performance PIP',
                "PIP #{$plan->id} updated",
                $old,
                $plan->toArray()
            );

            return redirect()->route('performance.pip.index', ['expand' => $plan->id])->with('success', 'PIP updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update PIP: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(PerformanceImprovementPlan $plan)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $plan->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        try {
            AuditLogger::log(
                'pip.deleted',
                $plan,
                'Performance PIP',
                "PIP #{$plan->id} deleted"
            );
            $plan->delete();

            return redirect()->route('performance.pip.index')->with('success', 'PIP deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete PIP: ' . $e->getMessage());
        }
    }

    public function storeReview(Request $request, PerformanceImprovementPlan $plan)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $plan->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), [
            'review_date' => 'required|date',
            'progress_rating' => 'required|numeric|min:0|max:100',
            'comments' => 'nullable|string',
            'action_items' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $review = PipReview::create([
                'pip_id' => $plan->id,
                'review_date' => $request->review_date,
                'reviewer_id' => auth()->id(),
                'progress_rating' => $request->progress_rating,
                'comments' => $request->comments,
                'action_items' => $request->action_items,
            ]);

            AuditLogger::log(
                'pip.review_created',
                $review,
                'Performance PIP',
                "Progress review added to PIP #{$plan->id}"
            );

            return redirect()->route('performance.pip.index', ['expand' => $plan->id])->with('success', 'Progress review added successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add progress review: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyReview(PerformanceImprovementPlan $plan, PipReview $review)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $plan->client_id != $clientId || $review->pip_id != $plan->id) {
            return back()->with('error', 'Invalid request.');
        }

        try {
            AuditLogger::log(
                'pip.review_deleted',
                $review,
                'Performance PIP',
                "Progress review removed from PIP #{$plan->id}"
            );
            $review->delete();

            return redirect()->route('performance.pip.index', ['expand' => $plan->id])->with('success', 'Progress review deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete progress review: ' . $e->getMessage());
        }
    }
}
