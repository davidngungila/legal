<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'employee_id',
        'contract_type',
        'start_date',
        'end_date',
        'salary',
        'benefits',
        'terms',
        'status',
        'document_path',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'salary' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the client that owns the contract.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the employee that the contract belongs to.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Scope a query to only include active contracts.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include expired contracts.
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    /**
     * Scope a query to only include upcoming expiring contracts.
     */
    public function scopeExpiringSoon($query)
    {
        return $query->where('end_date', '<=', now()->addDays(30))
                    ->where('end_date', '>', now());
    }

    /**
     * Check if the contract is expired.
     */
    public function isExpired()
    {
        return $this->end_date && $this->end_date->isPast();
    }

    /**
     * Check if the contract is expiring soon.
     */
    public function isExpiringSoon()
    {
        return $this->end_date && $this->end_date->diffInDays(now()) <= 30 && !$this->isExpired();
    }
}
