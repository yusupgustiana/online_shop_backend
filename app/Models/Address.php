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
        'prov_name',
        'city_id',
        'city_name',
        'district_id',
        'district_name',
        'postal_code',
        'phone',
        'is_default',
    ];

public function getFullAddressCompleteAttribute()
{
    return collect([
        $this->full_address,
        $this->district_name,
        $this->city_name,
        $this->prov_name,
    ])->filter()->implode(', ');
}


}