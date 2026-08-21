<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToCurrentClient;

class PolicyAcknowledgment extends Model
{
    use BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'employee_id',
        'document_id',
        'policy_name',
        'policy_version',
        'acknowledged',
        'acknowledged_at',
        'signature_data',
        'notes',
    ];

    protected $casts = [
        'acknowledged' => 'boolean',
        'acknowledged_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function scopeAcknowledged($query)
    {
        return $query->where('acknowledged', true);
    }

    public function scopePending($query)
    {
        return $query->where('acknowledged', false);
    }

    public static function getRequiredPolicies(): array
    {
        return [
            'code_of_conduct' => 'Code of Conduct',
            'data_protection' => 'Data Protection & Privacy Policy',
            'health_safety' => 'Health & Safety Policy',
            'anti_harassment' => 'Anti-Harassment & Discrimination Policy',
            'social_media' => 'Social Media & IT Usage Policy',
            'confidentiality' => 'Confidentiality & Non-Disclosure Agreement',
            'disciplinary' => 'Disciplinary Procedure Policy',
            'grievance' => 'Grievance Procedure Policy',
        ];
    }
}