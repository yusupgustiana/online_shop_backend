<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\CoreApi;
use RuntimeException;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key')
            ?: env('MIDTRANS_SERVER_KEY');

        Config::$isProduction = filter_var(
            env('MIDTRANS_IS_PRODUCTION', false),
            FILTER_VALIDATE_BOOLEAN
        );

        Config::$isSanitized = true;
        Config::$is3ds = true;

        if (empty(Config::$serverKey)) {
            throw new RuntimeException(
                'MIDTRANS_SERVER_KEY belum dikonfigurasi.'
            );
        }
    }

    public function createVA($order, $user, $bank): array
    {
        $params = [
            'payment_type' => 'bank_transfer',

            'transaction_details' => [
                'order_id' => $order->transaction_number,
                'gross_amount' => (int) $order->total_cost,
            ],

            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],

            'bank_transfer' => [
                'bank' => strtolower($bank),
            ],
        ];

        $transaction = CoreApi::charge($params);

        $vaNumber = data_get(
            $transaction,
            'va_numbers.0.va_number'
        );

        if (strtolower($bank) === 'permata') {
            $vaNumber = data_get(
                $transaction,
                'permata_va_number'
            );
        }

        if (strtolower($bank) === 'mandiri') {
            $vaNumber = data_get($transaction, 'bill_key');
        }

        if (!$vaNumber) {
            throw new RuntimeException(
                'Midtrans tidak mengembalikan nomor pembayaran. '
                . 'Periksa respons transaksi Midtrans.'
            );
        }

        return [
            'order_id' => $transaction->order_id ?? $order->transaction_number,
            'transaction_id' => $transaction->transaction_id ?? null,
            'bank' => strtolower($bank),
            'va_number' => $vaNumber,
            'transaction_time' => $transaction->transaction_time ?? null,
            'transaction_status' => $transaction->transaction_status ?? 'pending',
            'expiry_time' => $transaction->expiry_time ?? null,
        ];
    }
}
