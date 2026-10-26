<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDummy extends Model
{
    protected $fillable = [
        'sku_ref',
        'sku_no',
        'product_code',
        'name',
        'brand',
        'unit_price_vat',
        'stock',
        'id_m_warehouse',
    ];

    protected $casts = [
        'unit_price_vat' => 'decimal:2',
        'stock' => 'integer',
    ];
}