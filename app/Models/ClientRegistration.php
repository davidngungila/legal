<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientRegistration extends Model
{
    use HasFactory;

    protected $table = 'client_registrations';

    protected $fillable = [
        'employer_name',
        'employer_number',
        'contact_person',
        'contact_phone',
        'contact_email',
        'tin_number',
        'tin_certificate_path',
        'osha_registration',
        'osha_certificate_path',
        'nhif_registration',
        'nhif_certificate_path',
        'wcf_registration',
        'wcf_certificate_path',
        'vat_registration_number',
        'vat_certificate_path',
        'nssf_registration',
        'nssf_certificate_path',
        'phone',
        'mobile',
        'email',
        'postal_address',
        'region',
        'district',
        'fax',
        'location',
        'road',
        'street',
        'plot',
        'block',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getFullAddressAttribute()
    {
        $address = [];
        if ($this->location) $address[] = $this->location;
        if ($this->road) $address[] = $this->road;
        if ($this->street) $address[] = $this->street;
        if ($this->plot) $address[] = 'Plot ' . $this->plot;
        if ($this->block) $address[] = 'Block ' . $this->block;
        if ($this->region) $address[] = $this->region;
        if ($this->district) $address[] = $this->district;
        
        return implode(', ', $address);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    public static function generateEmployerNumber()
    {
        $prefix = 'EMP';
        $year = date('Y');
        $sequence = str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
        return "{$prefix}{$year}{$sequence}";
    }
}
