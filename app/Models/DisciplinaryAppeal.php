<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisciplinaryAppeal extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_id',
        'appeal_filed_at',
        'appeal_by',
        'appeal_grounds',
        'appeal_decision',
        'decision_date',
        'appeal_authority_id',
    ];

    protected $casts = [
        'appeal_filed_at' => 'datetime',
        'decision_date' => 'date',
    ];

    public function disciplinaryCase(): BelongsTo
    {
        return $this->belongsTo(DisciplinaryCase::class, 'case_id');
    }

    public function appealBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'appeal_by');
    }

    public function appealAuthority(): BelongsTo
    {
        return $this->belongsTo(User::class, 'appeal_authority_id');
    }
}
