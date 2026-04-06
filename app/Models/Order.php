<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'address_id',
        'subtotal',
        'shipping_cost',
        'total_cost',
        'status',
        'payment_method',
        'shipping_service',
        'transaction_number',
        'payment_va_name',
        'payment_va_number'
    ];

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke order items
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

//        public function address()
// {
//     return $this->belongsTo(Address::class)
//         ->select('full_address', 'name', 'phone');
// }
}