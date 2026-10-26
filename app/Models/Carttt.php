<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carttt extends Model
{
protected $fillable = [
    'id_m_customer',
    'sku_ref',
    'product_code_ref',
    'product_code',
    'qty',
    'product_name',
    'brand',
    'mf',
    'car_model',
    'price',
    'description',
     'link',
];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
    ];
}