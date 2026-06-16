<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToCurrentClient;

class LeaveBalance extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'employee_id',
        'leave_type_id',
        'month',
        'year',
        'opening_balance',
        'accrued',
        'taken',
        'closing_balance',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'accrued' => 'decimal:2',
        'taken' => 'decimal:2',
        'closing_balance' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }
}
