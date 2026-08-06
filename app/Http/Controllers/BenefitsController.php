<?php

namespace App\Http\Controllers;

use App\Models\BenefitEnrollment;
use App\Models\BenefitPlan;
use App\Models\Client;
use App\Models\Employee;
use App\Models\LifeEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BenefitsController extends Controller
{
    public function lifeEvents()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = \App\Models\Client::find($clientId);

        $lifeEvents = LifeEvent::with('employee')
            ->where('client_id', $clientId)
            ->orderBy('event_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $employees = Employee::where('client_id', $clientId)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'employee_id', 'first_name', 'last_name']);

        $stats = [
            'total' => $lifeEvents->count(),
            'pending' => $lifeEvents->where('status', 'pending')->count(),
            'processing' => $lifeEvents->where('status', 'processing')->count(),
            'completed' => $lifeEvents->where('status', 'completed')->count(),
        ];

        return view('benefits.life-events', compact('currentClient', 'lifeEvents', 'employees', 'stats'));
    }

    public function storeLifeEvent(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'event_type' => 'required|string|max:50',
            'event_date' => 'required|date',
            'description' => 'nullable|string',
            'status' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $employee = Employee::where('client_id', $clientId)->findOrFail($request->employee_id);

            LifeEvent::create([
                'client_id' => $clientId,
                'employee_id' => $employee->id,
                'event_type' => $request->event_type,
                'event_date' => $request->event_date,
                'description' => $request->description,
                'status' => $request->status ?: 'pending',
            ]);

            return redirect()->route('benefits.life-events')->with('success', 'Life event recorded successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to record life event: ' . $e->getMessage())->withInput();
        }
    }

    public function updateLifeEvent(Request $request, $id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $event = LifeEvent::where('client_id', $clientId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'event_type' => 'required|string|max:50',
            'event_date' => 'required|date',
            'description' => 'nullable|string',
            'status' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $employee = Employee::where('client_id', $clientId)->findOrFail($request->employee_id);

            $event->update([
                'employee_id' => $employee->id,
                'event_type' => $request->event_type,
                'event_date' => $request->event_date,
                'description' => $request->description,
                'status' => $request->status ?: 'pending',
            ]);

            return redirect()->route('benefits.life-events')->with('success', 'Life event updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update life event: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyLifeEvent($id)
    {
        $clientId = session('current_client_id');
        $event = LifeEvent::where('client_id', $clientId)->findOrFail($id);
        $event->delete();

        return redirect()->route('benefits.life-events')->with('success', 'Life event deleted successfully!');
    }

    public function plans()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);

        $plans = BenefitPlan::orderBy('category')->orderBy('name')->get();

        $employees = Employee::where('client_id', $clientId)
            ->where('status', 'active')
            ->get(['id', 'benefits']);

        $grouped = $plans->groupBy('category');

        $stats = [
            'total_plans' => $plans->count(),
            'active_plans' => $plans->where('status', 'active')->count(),
            'total_employees' => $employees->count(),
            'employees_with_benefits' => $employees->filter(fn ($e) => is_array($e->benefits) && count($e->benefits) > 0)->count(),
        ];

        $categoryCoverage = [];
        foreach (array_keys(BenefitPlan::CATEGORIES) as $category) {
            $names = collect($grouped->get($category, []))->pluck('name')
                ->map(fn ($n) => mb_strtolower((string) $n))
                ->filter()
                ->values();

            $categoryCoverage[$category] = $employees->filter(function ($e) use ($names) {
                if (!is_array($e->benefits) || $names->isEmpty()) {
                    return false;
                }
                foreach ($e->benefits as $benefit) {
                    foreach ($names as $name) {
                        if (mb_strpos(mb_strtolower((string) $benefit), $name) !== false) {
                            return true;
                        }
                    }
                }
                return false;
            })->count();
        }

        return view('benefits.plans', compact('currentClient', 'plans', 'grouped', 'stats', 'categoryCoverage'));
    }

    public function storePlan(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|in:' . implode(',', array_keys(BenefitPlan::CATEGORIES)),
            'provider' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'cost_period' => 'nullable|in:' . implode(',', array_keys(BenefitPlan::COST_PERIODS)),
            'coverage' => 'nullable|string|max:100',
            'mandatory' => 'nullable',
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            BenefitPlan::create([
                'client_id' => $clientId,
                'name' => $request->name,
                'category' => $request->category,
                'provider' => $request->provider,
                'description' => $request->description,
                'cost' => $request->cost ?: 0,
                'cost_period' => $request->cost_period ?: 'monthly',
                'coverage' => $request->coverage,
                'mandatory' => $request->has('mandatory'),
                'status' => $request->status ?: 'active',
            ]);

            return redirect()->route('benefits.plans')->with('success', 'Benefit plan created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create benefit plan: ' . $e->getMessage())->withInput();
        }
    }

    public function updatePlan(Request $request, $id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $plan = BenefitPlan::where('client_id', $clientId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|in:' . implode(',', array_keys(BenefitPlan::CATEGORIES)),
            'provider' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'cost_period' => 'nullable|in:' . implode(',', array_keys(BenefitPlan::COST_PERIODS)),
            'coverage' => 'nullable|string|max:100',
            'mandatory' => 'nullable',
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $plan->update([
                'name' => $request->name,
                'category' => $request->category,
                'provider' => $request->provider,
                'description' => $request->description,
                'cost' => $request->cost ?: 0,
                'cost_period' => $request->cost_period ?: 'monthly',
                'coverage' => $request->coverage,
                'mandatory' => $request->has('mandatory'),
                'status' => $request->status ?: 'active',
            ]);

            return redirect()->route('benefits.plans')->with('success', 'Benefit plan updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update benefit plan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyPlan($id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $plan = BenefitPlan::where('client_id', $clientId)->findOrFail($id);
        $plan->delete();

        return redirect()->route('benefits.plans')->with('success', 'Benefit plan deleted successfully!');
    }

    public function enrollment()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);

        $enrollments = BenefitEnrollment::with(['employee', 'plan'])
            ->orderBy('created_at', 'desc')
            ->get();

        $employees = Employee::where('client_id', $clientId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'employee_id', 'first_name', 'last_name']);

        $plans = BenefitPlan::orderBy('category')->orderBy('name')->get();

        $stats = [
            'total' => $enrollments->count(),
            'enrolled' => $enrollments->where('status', 'enrolled')->count(),
            'pending' => $enrollments->where('status', 'pending')->count(),
            'waived' => $enrollments->where('status', 'waived')->count(),
        ];

        $planCoverage = [];
        foreach ($plans as $plan) {
            $planCoverage[$plan->id] = $enrollments->where('plan_id', $plan->id)->where('status', '!=', 'terminated')->count();
        }

        return view('benefits.enrollment', compact('currentClient', 'enrollments', 'employees', 'plans', 'stats', 'planCoverage'));
    }

    public function storeEnrollment(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'plan_id' => 'required|exists:benefit_plans,id',
            'effective_date' => 'nullable|date',
            'employee_cost' => 'nullable|numeric|min:0',
            'employer_cost' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:' . implode(',', array_keys(BenefitEnrollment::STATUSES)),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $employee = Employee::where('client_id', $clientId)->find($request->employee_id);
        $plan = BenefitPlan::where('client_id', $clientId)->find($request->plan_id);

        if (!$employee || !$plan) {
            return redirect()->back()->with('error', 'Invalid employee or plan for this client.')->withInput();
        }

        $existing = BenefitEnrollment::where('client_id', $clientId)
            ->where('employee_id', $request->employee_id)
            ->where('plan_id', $request->plan_id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'This employee is already enrolled in that plan.')->withInput();
        }

        try {
            BenefitEnrollment::create([
                'client_id' => $clientId,
                'employee_id' => $request->employee_id,
                'plan_id' => $request->plan_id,
                'effective_date' => $request->effective_date ?: now()->toDateString(),
                'employee_cost' => $request->employee_cost ?: 0,
                'employer_cost' => $request->employer_cost ?: 0,
                'status' => $request->status ?: 'enrolled',
            ]);

            return redirect()->route('benefits.enrollment')->with('success', 'Enrollment created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create enrollment: ' . $e->getMessage())->withInput();
        }
    }

    public function updateEnrollment(Request $request, $id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $enrollment = BenefitEnrollment::where('client_id', $clientId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'plan_id' => 'required|exists:benefit_plans,id',
            'effective_date' => 'nullable|date',
            'employee_cost' => 'nullable|numeric|min:0',
            'employer_cost' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:' . implode(',', array_keys(BenefitEnrollment::STATUSES)),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $enrollment->update([
                'employee_id' => $request->employee_id,
                'plan_id' => $request->plan_id,
                'effective_date' => $request->effective_date,
                'employee_cost' => $request->employee_cost ?: 0,
                'employer_cost' => $request->employer_cost ?: 0,
                'status' => $request->status ?: 'enrolled',
            ]);

            return redirect()->route('benefits.enrollment')->with('success', 'Enrollment updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update enrollment: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyEnrollment($id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $enrollment = BenefitEnrollment::where('client_id', $clientId)->findOrFail($id);
        $enrollment->delete();

        return redirect()->route('benefits.enrollment')->with('success', 'Enrollment removed successfully!');
    }
}
