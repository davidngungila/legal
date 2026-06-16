<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExitSettlement extends Model
{
    use HasFactory;

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
}
