<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'full_address',
        'prov_id',
        'city_id',
        'district_id',
        'postal_code',
        'phone',
        'is_default',
    ];

        public function province()
    {
        return $this->belongsTo(Province::class, 'prov_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }
     public function getFullAddressCompleteAttribute()
    {
        return "{$this->full_address}, {$this->district->name}, {$this->city->name}, {$this->province->name}";
    }


}