<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MidtransOrder extends Model
{
    protected $fillable = [
        'order_id',
        'id_m_customer',
        'customer_name',
        'customer_email',
        'subtotal_produk',
        'ekspedisi_code',
        'ekspedisi_name',
        'ekspedisi_price',
        'total',
        'payment_type',
        'payment',
        'address_shipping',
        'state',
        'city',
        'postcode',
        'country',
        'snap_token',
        'redirect_url',
        'status',
        'payment_status',
        'expiry_time',
        'payment_code',
        'transaction_time',
         'order_number',
    ];

    protected $casts = [
        'subtotal_produk' => 'decimal:2',
        'ekspedisi_price' => 'decimal:2',
        'total' => 'decimal:2',
        'expiry_time' => 'datetime',
    ];
}
