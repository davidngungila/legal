<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppraisalRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'appraisal_id',
        'kpi_id',
        'self_score',
        'supervisor_score',
        'calibrated_score',
        'comments',
    ];

    protected $casts = [
        'self_score' => 'decimal:2',
        'supervisor_score' => 'decimal:2',
        'calibrated_score' => 'decimal:2',
    ];

    public function appraisal(): BelongsTo
    {
        return $this->belongsTo(PerformanceReview::class, 'appraisal_id');
    }

    public function kpi(): BelongsTo
    {
        return $this->belongsTo(Kpi::class);
    }
}
