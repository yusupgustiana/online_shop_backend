<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\CoreApi;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

public function createVA($order, $user, $bank)
{
    $params = [
        'payment_type' => 'bank_transfer',
        'transaction_details' => [
            'order_id' => $order->transaction_number,
            'gross_amount' => $order->total_cost,
        ],
        'customer_details' => [
            'first_name' => $user->name,
            'email' => $user->email,
        ],
        'bank_transfer' => [
            'bank' => $bank
        ]
    ];

    $transaction = \Midtrans\CoreApi::charge($params);

    return [
        'bank' => $bank,
        'va_number' => $transaction->va_numbers[0]->va_number ?? null
    ];
}


}