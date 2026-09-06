<?php

namespace App\Models;

use App\Models\Traits\BelongsToCurrentClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RetrenchmentEmployee extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'retrenchment_case_id',
        'employee_id',
        'selection_score',
        'selected',
        'redundancy_pay',
        'final_settlement',
    ];

    protected $casts = [
        'selected' => 'boolean',
        'selection_score' => 'decimal:2',
        'redundancy_pay' => 'decimal:2',
        'final_settlement' => 'decimal:2',
    ];

    public function retrenchmentCase(): BelongsTo
    {
        return $this->belongsTo(RetrenchmentCase::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Entries inherit their client from the parent retrenchment case.
     */
    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->whereHas('retrenchmentCase', function ($q) use ($clientId) {
            $q->where('client_id', $clientId);
        });
    }
}
