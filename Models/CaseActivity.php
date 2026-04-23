<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseActivity extends Model
{
    protected $fillable = [
        'legal_case_id',
        'user_id',
        'action',
        'description',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function legalCase(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionColorAttribute()
    {
        return match($this->action) {
            'created' => 'green',
            'updated' => 'blue',
            'assigned' => 'purple',
            'status_changed' => 'orange',
            'evidence_added' => 'indigo',
            'note_added' => 'gray',
            'meeting_scheduled' => 'yellow',
            'investigation_started' => 'red',
            'investigation_completed' => 'green',
            'resolved' => 'green',
            'closed' => 'gray',
            default => 'gray'
        };
    }

    public function getActionIconAttribute()
    {
        return match($this->action) {
            'created' => 'plus-circle',
            'updated' => 'edit',
            'assigned' => 'user-plus',
            'status_changed' => 'arrow-right-circle',
            'evidence_added' => 'folder-plus',
            'note_added' => 'file-text',
            'meeting_scheduled' => 'calendar',
            'investigation_started' => 'search',
            'investigation_completed' => 'check-circle',
            'resolved' => 'check-square',
            'closed' => 'x-circle',
            default => 'activity'
        };
    }
}
