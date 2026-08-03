<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\BelongsToCurrentClient;

class SalaryStructure extends Model
{
    use BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'name',
        'position',
        'min_salary',
        'mid_salary',
        'max_salary',
        'currency',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'min_salary' => 'decimal:2',
        'mid_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->where('client_id', $clientId);
    }
}
