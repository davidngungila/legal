<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToCurrentClient;

class CalibrationSession extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'cycle_id',
        'facilitated_by',
        'session_date',
        'notes',
        'status',
    ];

    protected $casts = [
        'session_date' => 'date',
    ];

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(PerformanceCycle::class);
    }

    public function facilitatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'facilitated_by');
    }
}
