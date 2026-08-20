<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Traits\BelongsToCurrentClient;

class PerformanceReview extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'employee_id',
        'reviewer_id',
        'review_date',
        'rating',
        'comments',
        'goals',
        'status',
        'cycle_id',
        'self_rating',
        'supervisor_rating',
        'calibrated_rating',
        'final_rating',
        'completed_at',
    ];

    protected $casts = [
        'review_date' => 'date',
        'rating' => 'integer',
        'self_rating' => 'decimal:2',
        'supervisor_rating' => 'decimal:2',
        'calibrated_rating' => 'decimal:2',
        'final_rating' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the employee being reviewed.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the reviewer.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(PerformanceCycle::class);
    }

    /**
     * Per-KPI scores recorded during the appraisal.
     */
    public function appraisalRatings(): HasMany
    {
        return $this->hasMany(AppraisalRating::class, 'appraisal_id');
    }

    /**
     * Employee goals linked to the review's cycle (used for KPI scoring).
     */
    public function employeeGoals()
    {
        return $this->hasMany(EmployeeGoal::class, 'employee_id', 'employee_id')
            ->when($this->cycle_id, fn ($q) => $q->where('cycle_id', $this->cycle_id));
    }

    /**
     * All goals for the reviewed employee in the review cycle.
     */
    public function goalsWithKpis()
    {
        return EmployeeGoal::with('kpis')
            ->where('client_id', $this->client_id)
            ->where('employee_id', $this->employee_id)
            ->when($this->cycle_id, fn ($q) => $q->where('cycle_id', $this->cycle_id))
            ->get();
    }

    /**
     * Schedule monthly performance review for probationary employee
     * BR-PERF-001
     */
    public static function scheduleProbationReview($employee, $reviewDate)
    {
        return self::create([
            'client_id' => $employee->client_id,
            'employee_id' => $employee->id,
            'reviewer_id' => $employee->manager_id,
            'review_date' => $reviewDate,
            'rating' => null,
            'comments' => null,
            'goals' => null,
            'status' => 'pending',
            'cycle_id' => null,
            'self_rating' => null,
            'supervisor_rating' => null,
            'calibrated_rating' => null,
            'final_rating' => null,
            'completed_at' => null,
        ]);
    }

    /**
     * Schedule quarterly performance review for confirmed employee
     * BR-PERF-002
     */
    public static function scheduleQuarterlyReview($employee, $cycleId, $reviewDate)
    {
        return self::create([
            'client_id' => $employee->client_id,
            'employee_id' => $employee->id,
            'reviewer_id' => $employee->manager_id,
            'review_date' => $reviewDate,
            'rating' => null,
            'comments' => null,
            'goals' => null,
            'status' => 'pending',
            'cycle_id' => $cycleId,
            'self_rating' => null,
            'supervisor_rating' => null,
            'calibrated_rating' => null,
            'final_rating' => null,
            'completed_at' => null,
        ]);
    }

    /**
     * Check if performance review is complete for payroll integration
     * BR-PERF-003
     */
    public function isComplete()
    {
        return $this->status === 'completed' && $this->completed_at !== null;
    }

    /**
     * Check if employee has incomplete appraisal for current period
     * BR-PERF-003
     */
    public static function hasIncompleteAppraisal($employeeId, $periodStart, $periodEnd)
    {
        return self::where('employee_id', $employeeId)
            ->whereBetween('review_date', [$periodStart, $periodEnd])
            ->where('status', '!=', 'completed')
            ->exists();
    }

    /**
     * Get employees with incomplete appraisals for payroll period
     * BR-PERF-003
     */
    public static function getEmployeesWithIncompleteAppraisals($clientId, $periodStart, $periodEnd)
    {
        return self::where('client_id', $clientId)
            ->whereBetween('review_date', [$periodStart, $periodEnd])
            ->where('status', '!=', 'completed')
            ->with('employee')
            ->get()
            ->pluck('employee')
            ->unique('id');
    }

    /**
     * Check if PIP should be triggered based on performance scores
     * BR-PERF-005
     */
    public function shouldTriggerPip()
    {
        if (!$this->isComplete() || $this->final_rating === null) {
            return false;
        }

        // Check if score is below 2.0
        if ($this->final_rating < 2.0) {
            // Check if previous 2 consecutive quarters were also below 2.0
            $previousReviews = self::where('employee_id', $this->employee_id)
                ->where('id', '<', $this->id)
                ->where('status', 'completed')
                ->orderBy('review_date', 'desc')
                ->take(2)
                ->get();

            if ($previousReviews->count() >= 2) {
                $allBelowThreshold = $previousReviews->every(function ($review) {
                    return $review->final_rating !== null && $review->final_rating < 2.0;
                });

                if ($allBelowThreshold) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Create PIP for employee based on poor performance
     * BR-PERF-005
     */
    public function createPip()
    {
        $employee = Employee::find($this->employee_id);
        
        if (!$employee) {
            throw new \Exception('Employee not found');
        }

        return PerformanceImprovementPlan::create([
            'client_id' => $this->client_id,
            'employee_id' => $this->employee_id,
            'trigger_appraisal_id' => $this->id,
            'pip_objectives' => 'Improve performance to meet minimum standards',
            'start_date' => now(),
            'end_date' => now()->addMonths(3),
            'review_frequency' => 'biweekly',
            'status' => 'active',
            'outcome' => null,
        ]);
    }

    /**
     * Get performance trend for employee (last N reviews)
     */
    public static function getPerformanceTrend($employeeId, $limit = 4)
    {
        return self::where('employee_id', $employeeId)
            ->where('status', 'completed')
            ->whereNotNull('final_rating')
            ->orderBy('review_date', 'desc')
            ->take($limit)
            ->get()
            ->sortBy('review_date');
    }

    /**
     * Check if performance is declining
     */
    public static function isPerformanceDeclining($employeeId)
    {
        $trend = self::getPerformanceTrend($employeeId, 3);
        
        if ($trend->count() < 2) {
            return false;
        }

        $ratings = $trend->pluck('final_rating')->values();
        
        // Check if each subsequent rating is lower than the previous
        for ($i = 0; $i < count($ratings) - 1; $i++) {
            if ($ratings[$i] >= $ratings[$i + 1]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Scope to only include pending reviews
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to only include completed reviews
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to only include overdue reviews
     */
    public function scopeOverdue($query)
    {
        return $query->where('review_date', '<', now())
            ->where('status', 'pending');
    }

    /**
     * Scope to only include reviews due within next 7 days
     */
    public function scopeDueSoon($query)
    {
        return $query->whereBetween('review_date', [now(), now()->addDays(7)])
            ->where('status', 'pending');
    }
}
