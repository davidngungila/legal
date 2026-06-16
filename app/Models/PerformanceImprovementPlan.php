<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\BelongsToCurrentClient;

class PerformanceImprovementPlan extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'employee_id',
        'trigger_appraisal_id',
        'pip_objectives',
        'start_date',
        'end_date',
        'review_frequency',
        'status',
        'outcome',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function triggerAppraisal(): BelongsTo
    {
        return $this->belongsTo(PerformanceReview::class, 'trigger_appraisal_id');
    }

    public function pipReviews(): HasMany
    {
        return $this->hasMany(PipReview::class);
    }
}
