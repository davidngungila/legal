<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToCurrentClient;

class BenefitPlan extends Model
{
    use BelongsToCurrentClient;

    const CATEGORIES = [
        'health' => 'Health Insurance',
        'retirement' => 'Retirement',
        'wellness' => 'Wellness',
        'additional' => 'Additional Benefits',
    ];

    const COST_PERIODS = [
        'monthly' => 'per month',
        'yearly' => 'per year',
        'one_time' => 'one-time',
        'none' => '',
    ];

    protected $fillable = [
        'client_id',
        'name',
        'category',
        'provider',
        'description',
        'cost',
        'cost_period',
        'coverage',
        'mandatory',
        'status',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'mandatory' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->where('client_id', $clientId);
    }
}
