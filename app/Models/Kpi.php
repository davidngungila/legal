<?php

namespace App\Models;

use App\Models\Traits\BelongsToCurrentClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kpi extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'goal_id',
        'kpi_description',
        'target',
        'weight',
        'measurement_unit',
        'deadline',
    ];

    protected $casts = [
        'deadline' => 'date',
        'weight' => 'decimal:2',
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(EmployeeGoal::class);
    }

    /**
     * KPIs inherit their client from the parent goal.
     */
    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->whereHas('goal', function ($q) use ($clientId) {
            $q->where('client_id', $clientId);
        });
    }
}
