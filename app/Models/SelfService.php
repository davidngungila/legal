<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToCurrentClient;

class SelfService extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'employee_id',
        'request_type',
        'title',
        'description',
        'status',
        'request_date',
        'start_date',
        'end_date',
        'days_requested',
        'amount',
        'attachment_path',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejection_reason',
        'processed_at',
        'employee_notes',
    ];

    protected $casts = [
        'request_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the client that owns the self-service request.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the employee that owns the self-service request.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user who approved the request.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the formatted status badge.
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>',
            'approved' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>',
            'rejected' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>',
            'processed' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Processed</span>',
        ];

        return $badges[$this->status] ?? $badges['pending'];
    }

    /**
     * Get the formatted request type badge.
     */
    public function getRequestTypeBadgeAttribute()
    {
        $badges = [
            'leave' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Leave</span>',
            'payslip' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Payslip</span>',
            'contract' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Contract</span>',
            'complaint' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Complaint</span>',
            'document_update' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Document Update</span>',
            'expense_claim' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">Expense Claim</span>',
        ];

        return $badges[$this->request_type] ?? $badges['leave'];
    }

    /**
     * Filter self-service requests by current client.
     */
    protected static function filterByClient(\Illuminate\Database\Eloquent\Builder $builder, $clientId)
    {
        $builder->where('client_id', $clientId);
    }

    /**
     * Get self-service requests for the current client.
     */
    public static function forCurrentClient()
    {
        $clientId = app('current_client_id');
        if (!$clientId) {
            return static::query();
        }

        return static::where('client_id', $clientId);
    }

    /**
     * Scope to only include requests of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('request_type', $type);
    }

    /**
     * Scope to only include requests with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to only include approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to only include requests in a date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('request_date', [$startDate, $endDate]);
    }

    /**
     * Check if the request is approved.
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the request is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the request is rejected.
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Approve the request.
     */
    public function approve($approverId, $notes = null)
    {
        $this->status = 'approved';
        $this->approved_by = $approverId;
        $this->approved_at = now();
        $this->approval_notes = $notes;
        $this->save();
        
        return $this;
    }

    /**
     * Reject the request.
     */
    public function reject($reason)
    {
        $this->status = 'rejected';
        $this->rejection_reason = $reason;
        $this->save();
        
        return $this;
    }

    /**
     * Process the request.
     */
    public function process()
    {
        $this->status = 'processed';
        $this->processed_at = now();
        $this->save();
        
        return $this;
    }
}
