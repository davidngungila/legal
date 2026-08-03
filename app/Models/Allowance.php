<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\BelongsToCurrentClient;

class Allowance extends Model
{
    use BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'employee_id',
        'name',
        'type',
        'amount',
        'percentage',
        'currency',
        'frequency',
        'is_taxable',
        'is_active',
        'effective_date',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'is_taxable' => 'boolean',
        'is_active' => 'boolean',
        'effective_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->where('client_id', $clientId);
    }
}
