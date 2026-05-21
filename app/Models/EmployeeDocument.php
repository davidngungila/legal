<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $table = 'employee_documents';

    protected $fillable = [
        'employee_registration_id',
        'document_type',
        'document_name',
        'document_number',
        'issuing_authority',
        'issue_date',
        'expiry_date',
        'document_path',
        'file_size',
        'file_type',
        'status',
        'uploaded_by',
        'verified_by',
        'verified_at',
        'notes',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'verified_at' => 'datetime',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeRegistration::class, 'employee_registration_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_registration_id', $employeeId);
    }

    public function scopeByType($query, $documentType)
    {
        return $query->where('document_type', $documentType);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopePendingVerification($query)
    {
        return $query->where('status', 'pending_verification');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '>', now())
                    ->where('expiry_date', '<=', now()->addDays($days));
    }

    public function getDocumentTypeLabel()
    {
        return match($this->document_type) {
            'national_id' => 'National ID',
            'passport' => 'Passport',
            'birth_certificate' => 'Birth Certificate',
            'academic_certificate' => 'Academic Certificate',
            'professional_certificate' => 'Professional Certificate',
            'medical_certificate' => 'Medical Certificate',
            'police_clearance' => 'Police Clearance',
            'reference_letter' => 'Reference Letter',
            'resume_cv' => 'Resume/CV',
            'contract' => 'Employment Contract',
            'other' => 'Other Document',
            default => 'Unknown'
        };
    }

    public function getStatusLabel()
    {
        return match($this->status) {
            'uploaded' => 'Uploaded',
            'pending_verification' => 'Pending Verification',
            'verified' => 'Verified',
            'rejected' => 'Rejected',
            'expired' => 'Expired',
            default => 'Unknown'
        };
    }

    public function getStatusColor()
    {
        return match($this->status) {
            'uploaded' => 'blue',
            'pending_verification' => 'yellow',
            'verified' => 'green',
            'rejected' => 'red',
            'expired' => 'gray',
            default => 'gray'
        };
    }

    public function isExpired()
    {
        return $this->expiry_date && now()->isAfter($this->expiry_date);
    }

    public function isExpiringSoon($days = 30)
    {
        return $this->expiry_date && 
               now()->isBefore($this->expiry_date) && 
               now()->diffInDays($this->expiry_date) <= $days;
    }

    public function getDaysUntilExpiry()
    {
        if ($this->expiry_date) {
            return now()->diffInDays($this->expiry_date, false);
        }
        return null;
    }

    public function getFileSizeFormatted()
    {
        if ($this->file_size) {
            $bytes = $this->file_size;
            $units = ['B', 'KB', 'MB', 'GB'];
            $unitIndex = 0;
            
            while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
                $bytes /= 1024;
                $unitIndex++;
            }
            
            return round($bytes, 2) . ' ' . $units[$unitIndex];
        }
        
        return 'Unknown';
    }

    public function canBeVerified()
    {
        return $this->status === 'uploaded' || $this->status === 'pending_verification';
    }

    public function verify(User $verifier)
    {
        $this->update([
            'status' => 'verified',
            'verified_by' => $verifier->id,
            'verified_at' => now(),
        ]);
    }

    public function reject(User $verifier, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'verified_by' => $verifier->id,
            'verified_at' => now(),
            'notes' => $reason,
        ]);
    }
}
