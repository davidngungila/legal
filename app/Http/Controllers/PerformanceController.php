<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogger;
use App\Models\AppraisalRating;
use App\Models\Client;
use App\Models\Kpi;
use App\Models\PerformanceCycle;
use App\Models\PerformanceReview;
use App\Models\Employee;
use App\Models\EmployeeGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);

        // Get performance data
        $cycles = PerformanceCycle::with(['performanceReviews' => function ($q) {
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
        if (! $clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'review_date' => 'required|date',
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string',
            'cycle_id' => 'nullable|exists:performance_cycles,id',
        ]);

        $review = PerformanceReview::create([
            'client_id' => $clientId,
            'employee_id' => $validated['employee_id'],
            'reviewer_id' => auth()->id(),
            'review_date' => $validated['review_date'],
            'rating' => $validated['rating'],
            'comments' => $validated['comments'],
            'cycle_id' => $validated['cycle_id'] ?? null,
            'status' => 'scheduled',
        ]);

        AuditLogger::log(
            'performance_review.scheduled',
            $review,
            'Performance',
            "Performance review scheduled for employee #{$review->employee_id}"
        );

        return back()->with('success', 'Performance review scheduled!');
    }

    public function updateStatus(Request $request, PerformanceReview $review)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $review->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $status = $request->get('status', 'completed');
        $old = $review->toArray();
        $review->update([
            'status' => $status,
            'completed_at' => $status === 'completed' || $status === 'finalized' ? now() : $review->completed_at,
        ]);

        AuditLogger::log(
            'performance_review.status_updated',
            $review,
            'Performance',
            "Review #{$review->id} status updated to {$status}",
            $old,
            $review->toArray()
        );

        return back()->with('success', 'Review status updated!');
    }

    public function show(PerformanceReview $review)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $review->client_id != $clientId) {
            return redirect()->route('performance.index')->with('error', 'Invalid request.');
        }

        $currentClient = Client::find($clientId);
        $review->load(['employee', 'reviewer', 'cycle', 'appraisalRatings.kpi']);
        $goals = $review->goalsWithKpis();

        return view('performance.reviews.show', compact('currentClient', 'review', 'goals'));
    }

    public function storeRatings(Request $request, PerformanceReview $review)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $review->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $kpis = $request->get('kpis', []);

        try {
            foreach ($kpis as $kpiId => $scores) {
                $kpi = Kpi::find($kpiId);
                if (! $kpi) {
                    continue;
                }

                AppraisalRating::updateOrCreate(
                    ['appraisal_id' => $review->id, 'kpi_id' => $kpiId],
                    [
                        'self_score' => $scores['self_score'] ?? null,
                        'supervisor_score' => $scores['supervisor_score'] ?? null,
                        'calibrated_score' => $scores['calibrated_score'] ?? null,
                        'comments' => $scores['comments'] ?? null,
                    ]
                );
            }

            // Roll up weighted averages into the review ratings (0-5 scale)
            $ratings = AppraisalRating::with('kpi')->where('appraisal_id', $review->id)->get();

            $rollup = function ($field) use ($ratings) {
                $totalWeight = 0;
                $weighted = 0;
                foreach ($ratings as $rating) {
                    if ($rating->{$field} === null || ! $rating->kpi) {
                        continue;
                    }
                    $weight = (float) ($rating->kpi->weight ?? 0);
                    $totalWeight += $weight;
                    $weighted += (float) $rating->{$field} * $weight;
                }
                return $totalWeight > 0 ? round($weighted / $totalWeight, 2) : null;
            };

            $final = $rollup('calibrated_score') ?? $rollup('supervisor_score') ?? $rollup('self_score');

            $old = $review->toArray();
            $review->update([
                'self_rating' => $rollup('self_score'),
                'supervisor_rating' => $rollup('supervisor_score'),
                'calibrated_rating' => $rollup('calibrated_score'),
                'final_rating' => $final,
                'rating' => $final ? round($final) : $review->rating,
                'status' => $review->status === 'scheduled' || $review->status === 'draft' ? 'submitted' : $review->status,
            ]);

            AuditLogger::log(
                'performance_review.scored',
                $review,
                'Performance',
                "Appraisal scores recorded for review #{$review->id}",
                $old,
                $review->toArray()
            );

            return back()->with('success', 'Appraisal scores saved successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to save appraisal scores: ' . $e->getMessage())->withInput();
        }
    }

    public function analytics(Request $request)
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);

        $cycleId = $request->get('cycle_id');
        $reviewsQuery = PerformanceReview::with(['employee', 'cycle'])
            ->where('client_id', $clientId);
        if ($cycleId) {
            $reviewsQuery->where('cycle_id', $cycleId);
        }
        $reviews = $reviewsQuery->get();

        $cycles = PerformanceCycle::where('client_id', $clientId)->orderBy('period_start', 'desc')->get();

        // Rating distribution (0-5)
        $distribution = [];
        foreach ([5, 4, 3, 2, 1] as $r) {
            $distribution[$r] = $reviews->where('rating', $r)->count();
        }

        // Average rating per cycle
        $cycleTrend = $cycles->map(function ($cycle) use ($reviews) {
            $cycleReviews = $reviews->where('cycle_id', $cycle->id);
            return [
                'name' => $cycle->cycle_name,
                'avg' => round($cycleReviews->avg('rating') ?? 0, 2),
                'count' => $cycleReviews->count(),
            ];
        })->filter(fn ($c) => $c['count'] > 0)->values();

        // Department breakdown (from final ratings)
        $departments = $reviews->groupBy(fn ($r) => $r->employee?->department ?? 'Unknown')
            ->map(function ($group) {
                $avg = round($group->avg('rating') ?? 0, 2);
                return ['department' => $group->first()->employee?->department ?? 'Unknown', 'avg' => $avg, 'count' => $group->count()];
            })
            ->values();

        // Status breakdown
        $statuses = $reviews->groupBy('status')->map->count();

        // Top performers
        $topPerformers = $reviews->where('rating', '>=', 4)->sortByDesc('rating')->take(5)->values();

        // Scoring completeness (reviews with final_rating)
        $scoredCount = $reviews->whereNotNull('final_rating')->count();
        $completionRate = $reviews->count() > 0 ? round($scoredCount / $reviews->count() * 100, 1) : 0;

        return view('performance.analytics', compact(
            'currentClient', 'cycles', 'reviews', 'distribution', 'cycleTrend', 'departments', 'statuses', 'topPerformers', 'completionRate', 'cycleId'
        ));
    }
}
