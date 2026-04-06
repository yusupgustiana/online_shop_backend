<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
 protected $fillable = [
    'order_id',
    'product_id',
    'quantity',
    'price'
];

public function getSubtotalAttribute()
{
    return $this->price * $this->quantity;
}

    /**
     * Relasi ke order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}