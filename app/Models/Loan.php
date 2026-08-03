<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\BelongsToCurrentClient;

class Loan extends Model
{
    use BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'employee_id',
        'loan_type',
        'principal_amount',
        'interest_rate',
        'installment_amount',
        'total_repayable',
        'remaining_balance',
        'start_date',
        'end_date',
        'status',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'total_repayable' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->where('client_id', $clientId);
    }
}
