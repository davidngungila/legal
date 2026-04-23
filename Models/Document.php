<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'title',
        'description',
        'document_type',
        'file_path',
        'file_size',
        'file_type',
        'status',
        'version',
        'effective_date',
        'expiry_date',
        'created_by',
        'updated_by',
        'is_required',
        'is_public',
        'category',
        'tags',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'is_required' => 'boolean',
        'is_public' => 'boolean',
        'tags' => 'array',
    ];

    /**
     * Get the client that owns the document.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the user who created the document.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the document.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope to get documents for the current client.
     */
    public function scopeForCurrentClient($query)
    {
        $currentClient = session('current_client');
        if ($currentClient) {
            return $query->where('client_id', $currentClient['id']);
        }
        return $query;
    }

    /**
     * Scope to get documents by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope to get public documents.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to get active documents.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>=', now());
                    });
    }

    /**
     * Get status badge HTML.
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Active</span>',
            'draft' => '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Draft</span>',
            'archived' => '<span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">Archived</span>',
            'expired' => '<span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Expired</span>',
        ];

        return $badges[$this->status] ?? $badges['draft'];
    }

    /**
     * Get document type icon.
     */
    public function getIconAttribute()
    {
        $icons = [
            'contract' => 'file-text',
            'handbook' => 'book',
            'policy' => 'shield',
            'safety' => 'alert-triangle',
            'procedure' => 'list',
            'form' => 'file',
            'report' => 'bar-chart',
            'other' => 'file',
        ];

        return $icons[$this->document_type] ?? $icons['other'];
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedFileSizeAttribute()
    {
        if ($this->file_size < 1024) {
            return $this->file_size . ' B';
        } elseif ($this->file_size < 1024 * 1024) {
            return round($this->file_size / 1024, 2) . ' KB';
        } else {
            return round($this->file_size / (1024 * 1024), 2) . ' MB';
        }
    }

    /**
     * Get download URL.
     */
    public function getDownloadUrlAttribute()
    {
        return route('documents.download', $this->id);
    }

    /**
     * Get view URL.
     */
    public function getViewUrlAttribute()
    {
        return route('documents.view', $this->id);
    }
}
