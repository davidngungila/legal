<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'ticket_id',
    'user_id',
    'message',
    'is_internal',
    'created_at',
    'updated_at'
])]
#[Hidden([])]
class SupportTicketResponse extends Model
{
    use HasFactory;

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    /**
     * Get the ticket that owns the response.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class);
    }

    /**
     * Get the user that created the response.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get user-friendly response type.
     */
    public function getResponseType()
    {
        return $this->is_internal ? 'Internal Note' : 'Public Response';
    }

    /**
     * Get formatted creation time.
     */
    public function getFormattedTime()
    {
        return $this->created_at->format('M j, Y g:i A');
    }
}
