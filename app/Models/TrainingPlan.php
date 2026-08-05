<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\BelongsToCurrentClient;
use Illuminate\Database\Eloquent\Builder;

class TrainingPlan extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'name',
        'description',
        'target_department',
        'target_category',
        'period_start',
        'period_end',
        'budget',
        'currency',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'budget' => 'decimal:2',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class, 'plan_id');
    }

    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->where((new static)->getTable() . '.client_id', $clientId);
    }
}
