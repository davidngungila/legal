<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'case_number',
        'title',
        'description',
        'case_type',
        'status',
        'priority',
        'assigned_to',
        'opened_date',
        'closed_date',
        'next_hearing_date',
        'court',
        'judge',
        'opposing_party',
        'opposing_counsel',
        'outcome',
        'settlement_amount',
        'notes',
    ];

    protected $casts = [
        'opened_date' => 'date',
        'closed_date' => 'date',
        'next_hearing_date' => 'date',
        'settlement_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the client that owns the legal case.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the user assigned to the case.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the activities for the legal case.
     */
    public function activities()
    {
        return $this->hasMany(CaseActivity::class);
    }

    /**
     * Scope a query to only include active cases.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include closed cases.
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope a query to only include high priority cases.
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    /**
     * Scope a query to filter by case type.
     */
    public function scopeByCaseType($query, $type)
    {
        return $query->where('case_type', $type);
    }

    /**
     * Check if the case is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if the case is closed.
     */
    public function isClosed()
    {
        return $this->status === 'closed';
    }

    /**
     * Get the status badge HTML.
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>',
            'closed' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Closed</span>',
            'pending' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>',
            'suspended' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Suspended</span>',
        ];

        return $badges[$this->status] ?? $badges['pending'];
    }

    /**
     * Get the priority badge HTML.
     */
    public function getPriorityBadgeAttribute()
    {
        $badges = [
            'high' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">High</span>',
            'medium' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Medium</span>',
            'low' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Low</span>',
        ];

        return $badges[$this->priority] ?? $badges['medium'];
    }

    /**
     * Get formatted settlement amount.
     */
    public function getFormattedSettlementAmountAttribute()
    {
        return $this->settlement_amount ? 'TZS ' . number_format($this->settlement_amount, 2) : 'N/A';
    }
}
