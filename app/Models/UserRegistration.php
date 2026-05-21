<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserRegistration extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user_registrations';

    protected $fillable = [
        'first_name',
        'middle_name',
        'surname',
        'email',
        'phone_number',
        'date_of_birth',
        'gender',
        'department_name',
        'section_name',
        'designation',
        'project_location',
        'is_active',
        'email_verified_at',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->surname}");
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department_name', $department);
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmail());
    }
}
