<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToCurrentClient;

class PersonnelIdApplication extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'employee_id',
        'employee_registration_id',
        'id_number',
        'id_type',
        'id_purpose',
        'valid_from',
        'valid_until',
        'access_areas',
        'special_permissions',
        'photo_path',
        'signature_path',
        'fingerprint_path',
        'emergency_access',
        'after_hours_access',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'emergency_access' => 'boolean',
        'after_hours_access' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function registration()
    {
        return $this->belongsTo(EmployeeRegistration::class, 'employee_registration_id');
    }
}
