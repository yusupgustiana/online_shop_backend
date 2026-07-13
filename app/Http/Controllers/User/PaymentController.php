<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;

class PaymentController extends Controller
{
    public function show($id)
    {
        $order = Order::with([
            'orderItems.product',
            'address'
        ])
        ->where('user_id', auth()->id()) // 🔥 keamanan
        ->findOrFail($id);

        return view('user.payment.show', compact('order'));
    }
}