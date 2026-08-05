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
}
