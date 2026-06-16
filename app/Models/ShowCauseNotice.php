<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShowCauseNotice extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_id',
        'sent_date',
        'response_deadline',
        'response_received_at',
        'response_text',
        'status',
    ];

    protected $casts = [
        'sent_date' => 'date',
        'response_deadline' => 'date',
        'response_received_at' => 'datetime',
    ];

    public function disciplinaryCase(): BelongsTo
    {
        return $this->belongsTo(DisciplinaryCase::class, 'case_id');
    }
}
