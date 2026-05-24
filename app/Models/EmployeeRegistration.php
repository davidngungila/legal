<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeRegistration extends Model
{
    use HasFactory;

    protected $table = 'employee_registrations';

    protected $fillable = [
        'employee_number',
        'hr_interview_id',
        'technical_interview_id',
        'surname',
        'first_name',
        'middle_name',
        'birthplace',
        'date_of_birth',
        'age',
        'gender',
        'residence_area',
        'permanent_residence',
        'postal_address',
        'email_address',
        'phone_number',
        'place_of_recruitment',
        'work_station',
        'type_of_contract',
        'job_descriptions',
        'date_employed',
        'terms_conditions',
        'information_consent',
        'employee_signature',
        'signature_date',
        'signed_document_path',
        'ranking_details',
        'employment_history',
        'created_by',
        'approved_by',
        'status',
        'approved_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_employed' => 'date',
        'signature_date' => 'date',
        'information_consent' => 'boolean',
        'approved_at' => 'datetime',
        'age' => 'integer',
        'job_descriptions' => 'array',
        'terms_conditions' => 'array',
        'ranking_details' => 'array',
        'employment_history' => 'array',
    ];

    /**
     * Get the HR interview associated with the registration.
     */
    public function hrInterview(): BelongsTo
    {
        return $this->belongsTo(HrCompetencyInterview::class, 'hr_interview_id');
    }

    /**
     * Get the technical interview associated with the registration.
     */
    public function technicalInterview(): BelongsTo
    {
        return $this->belongsTo(TechnicalInterview::class, 'technical_interview_id');
    }

    /**
     * Get the user who created the registration.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the registration.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class, 'employee_registration_id');
    }

    /**
     * Get full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->middle_name} {$this->surname}";
    }
}

