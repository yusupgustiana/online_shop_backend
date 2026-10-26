<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\CoreApi;
use RuntimeException;

class MidtransServiceDummy
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key')
            ?: env('MIDTRANS_SERVER_KEY');

        Config::$isProduction = filter_var(
            config(
                'services.midtrans.is_production',
                env('MIDTRANS_IS_PRODUCTION', false)
            ),
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

    public function createVA(
        string $orderId,
        int $total,
        string $customerName,
        string $customerEmail,
        string $bank
    ): array {
        $bank = strtolower($bank);

        $params = [
            'payment_type' => 'bank_transfer',

            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $total,
            ],

            'customer_details' => [
                'first_name' => $customerName,
                'email' => $customerEmail,
            ],

            'bank_transfer' => [
                'bank' => $bank,
            ],
        ];

        $transaction = CoreApi::charge($params);

        $paymentCode = data_get(
            $transaction,
            'va_numbers.0.va_number'
        );

        if ($bank === 'permata') {
            $paymentCode = data_get(
                $transaction,
                'permata_va_number'
            );
        } elseif ($bank === 'mandiri') {
            $paymentCode = data_get(
                $transaction,
                'bill_key'
            );
        }

        return [
            'order_id' => data_get(
                $transaction,
                'order_id',
                $orderId
            ),
            'transaction_id' => data_get(
                $transaction,
                'transaction_id'
            ),
            'payment_code' => $paymentCode,
            'transaction_time' => data_get(
                $transaction,
                'transaction_time'
            ),
            'transaction_status' => data_get(
                $transaction,
                'transaction_status',
                'pending'
            ),
            'expiry_time' => data_get(
                $transaction,
                'expiry_time'
            ),
        ];
    }
}
