<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToCurrentClient;

class SocialRecord extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'employee_registration_id',
        'nssf_number',
        'nssf_card_path',
        'nhif_number',
        'nhif_card_path',
        'tin_number',
        'tin_certificate_path',
        'wcf_number',
        'wcf_certificate_path',
        'osha_number',
        'osha_certificate_path',
        'bank_name',
        'bank_account_number',
        'bank_branch',
        'bank_verification_path',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'emergency_contact_address',
        'next_of_kin_name',
        'next_of_kin_relationship',
        'next_of_kin_phone',
        'next_of_kin_address',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    public function employee()
    {
        return $this->belongsTo(EmployeeRegistration::class, 'employee_registration_id');
    }
}
