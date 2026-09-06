<?php

namespace App\Models;

use App\Models\Traits\BelongsToCurrentClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExitSettlement extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'exit_case_id',
        'final_salary',
        'leave_pay',
        'notice_pay',
        'bonus_pay',
        'other_payments',
        'total_settlement',
        'status',
    ];

    protected $casts = [
        'final_salary' => 'decimal:2',
        'leave_pay' => 'decimal:2',
        'notice_pay' => 'decimal:2',
        'bonus_pay' => 'decimal:2',
        'other_payments' => 'decimal:2',
        'total_settlement' => 'decimal:2',
    ];

    public function exitCase(): BelongsTo
    {
        return $this->belongsTo(ExitCase::class);
    }

    /**
     * Settlements inherit their client from the parent exit case.
     */
    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->whereHas('exitCase', function ($q) use ($clientId) {
            $q->where('client_id', $clientId);
        });
    }
}
