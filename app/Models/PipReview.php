<?php

namespace App\Models;

use App\Models\Traits\BelongsToCurrentClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PipReview extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'pip_id',
        'review_date',
        'reviewer_id',
        'progress_rating',
        'comments',
        'action_items',
    ];

    protected $casts = [
        'review_date' => 'date',
        'progress_rating' => 'decimal:2',
    ];

    public function pip(): BelongsTo
    {
        return $this->belongsTo(PerformanceImprovementPlan::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Reviews inherit their client from the parent PIP.
     */
    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->whereHas('pip', function ($q) use ($clientId) {
            $q->where('client_id', $clientId);
        });
    }
}
