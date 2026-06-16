<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisciplinaryHearing extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_id',
        'hearing_date',
        'hearing_time',
        'venue',
        'notice_sent_at',
        'committee_members',
        'employee_representative',
        'proceedings_notes',
    ];

    protected $casts = [
        'hearing_date' => 'date',
        'hearing_time' => 'datetime:H:i',
        'notice_sent_at' => 'datetime',
    ];

    public function disciplinaryCase(): BelongsTo
    {
        return $this->belongsTo(DisciplinaryCase::class, 'case_id');
    }
}
