<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDummy extends Model
{
    protected $fillable = [
        'id_m_customer',
        'customer_name',
    ];
}