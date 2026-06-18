<?php

namespace App\Http\Controllers;

use App\Models\PerformanceCycle;
use App\Models\PerformanceReview;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = \App\Models\Client::find($clientId);

        // Get performance data
        $cycles = PerformanceCycle::with(['performanceReviews' => function($q) {
            $q->with('employee');
        }])->where('client_id', $clientId)->latest()->paginate(10);

        $reviews = PerformanceReview::with(['employee', 'reviewer'])->where('client_id', $clientId)->latest()->paginate(20);

        $employees = Employee::where('client_id', $clientId)->where('status', 'active')->get();

        $filterPeriod = $request->get('period', 'quarter'); // quarter, sixmonths, year
        $filterDepartment = $request->get('department', 'all');

        // Calculate stats
        $totalReviews = $reviews->total();
        $completedReviews = $reviews->where('status', 'completed')->count();
        $pendingReviews = $reviews->where('status', 'pending')->count();

        // Average rating
        $avgRating = $reviews->avg('rating') ?? 0;

        // Top performers (rating >= 4)
        $topPerformers = $reviews->where('rating', '>=', 4)->take(6)->values();

        return view('performance.index', compact('currentClient', 'cycles', 'reviews', 'employees', 'totalReviews', 'completedReviews', 'pendingReviews', 'avgRating', 'topPerformers', 'filterPeriod', 'filterDepartment'));
    }

    public function store(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'review_date' => 'required|date',
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string',
        ]);

        $review = PerformanceReview::create([
            'client_id' => $clientId,
            'employee_id' => $validated['employee_id'],
            'reviewer_id' => auth()->id(),
            'review_date' => $validated['review_date'],
            'rating' => $validated['rating'],
            'comments' => $validated['comments'],
            'status' => 'scheduled',
        ]);

        return back()->with('success', 'Performance review scheduled!');
    }

    public function updateStatus(Request $request, PerformanceReview $review)
    {
        $clientId = session('current_client_id');
        if (!$clientId || $review->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $review->update(['status' => $request->get('status', 'completed')]);
        return back()->with('success', 'Review status updated!');
    }

    public function goals()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = \App\Models\Client::find($clientId);
        return view('performance.goals', ['currentClient' => $currentClient]);
    }

    public function pip()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = \App\Models\Client::find($clientId);
        return view('performance.pip', ['currentClient' => $currentClient]);
    }

    public function analytics()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = \App\Models\Client::find($clientId);
        return view('performance.analytics', ['currentClient' => $currentClient]);
    }
}
