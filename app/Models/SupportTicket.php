<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'client_id',
    'ticket_number',
    'subject',
    'description',
    'category',
    'priority',
    'status',
    'assigned_to',
    'resolution',
    'resolved_at',
    'closed_at',
    'created_at',
    'updated_at'
])]
#[Hidden([])]
class SupportTicket extends Model
{
    use HasFactory;

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    const CATEGORIES = [
        'technical' => 'Technical Issues',
        'billing' => 'Billing & Payments',
        'payroll' => 'Payroll Issues',
        'compliance' => 'Compliance Questions',
        'feature_request' => 'Feature Request',
        'bug_report' => 'Bug Report',
        'account' => 'Account Issues',
        'other' => 'Other'
    ];

    const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent'
    ];

    const STATUSES = [
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'pending' => 'Pending',
        'resolved' => 'Resolved',
        'closed' => 'Closed'
    ];

    /**
     * Get the user that created the ticket.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the client associated with the ticket.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the assigned user for the ticket.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the responses for the ticket.
     */
    public function responses(): HasMany
    {
        return $this->hasMany(SupportTicketResponse::class);
    }

    /**
     * Generate a unique ticket number.
     */
    public static function generateTicketNumber()
    {
        $prefix = 'TKT';
        $date = date('Ymd');
        $lastTicket = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastTicket ? intval(substr($lastTicket->ticket_number, -4)) + 1 : 1;
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new support ticket.
     */
    public static function createTicket(array $data)
    {
        $data['ticket_number'] = self::generateTicketNumber();
        $data['status'] = 'open';
        
        return self::create($data);
    }

    /**
     * Get tickets for the current client.
     */
    public static function forCurrentClient()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return static::query();
        }

        return static::where('client_id', $clientId);
    }

    /**
     * Scope to get tickets by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get tickets by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to get tickets by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Check if ticket is resolved.
     */
    public function isResolved()
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    /**
     * Get the priority color for UI.
     */
    public function getPriorityColor()
    {
        return match($this->priority) {
            'urgent' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray'
        };
    }

    /**
     * Get the status color for UI.
     */
    public function getStatusColor()
    {
        return match($this->status) {
            'open' => 'blue',
            'in_progress' => 'yellow',
            'pending' => 'orange',
            'resolved' => 'green',
            'closed' => 'gray',
            default => 'gray'
        };
    }
}
