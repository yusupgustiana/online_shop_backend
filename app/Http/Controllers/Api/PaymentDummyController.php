<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Str;
use Throwable;
use App\Models\MidtransOrder;
use Illuminate\Support\Facades\DB;
use App\Services\MidtransServiceDummy;


class PaymentDummyController extends Controller
{
public function __construct(
    private MidtransServiceDummy $midtransServiceDummy
) {}


public function store(Request $request)
{
    $validator = \Validator::make($request->all(), [
        'event' => 'required|in:midtrans_order',
        'id_m_customer' => 'required|string|max:100',
        'customer_name' => 'required|string|max:255',
        'customer_email' => 'required|email|max:255',
        'subtotal_produk' => 'required|numeric|min:0',
        'ekspedisi_code' => 'required|string|max:100',
        'ekspedisi_name' => 'required|string|max:255',
        'ekspedisi_price' => 'required|numeric|min:0',
        'payment_type' => 'required|in:bank_transfer',
        'payment' => 'required|in:bca,bni,bri,mandiri,permata',
        'address_shipping' => 'required|string|max:1000',
        'state' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'postcode' => 'required|string|max:20',
        'country' => 'required|string|max:100',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'code' => 422,
            'status' => false,
            'message' => 'Validasi checkout gagal.',
            'errors' => $validator->errors(),
        ], 422);
    }

    try {
        $validated = $validator->validated();

        $subtotal = (int) round(
            (float) $validated['subtotal_produk']
        );

        $shipping = (int) round(
            (float) $validated['ekspedisi_price']
        );

        $total = $subtotal + $shipping;

        if ($total <= 0) {
            return response()->json([
                'code' => 422,
                'status' => false,
                'message' => 'Total pembayaran harus lebih dari nol.',
            ], 422);
        }

        // Buat ID transaksi dari aplikasi.
        $orderId = 'ORD-' . strtoupper(
            (string) \Illuminate\Support\Str::uuid()
        );

        // Buat transaksi melalui MidtransService (Core API).
        $paymentData = $this->midtransServiceDummy->createVA(
            $orderId,
            $total,
            $validated['customer_name'],
            $validated['customer_email'],
            $validated['payment']
        );

        // Jangan simpan transaksi tanpa kode VA untuk alur VA langsung.
        if (empty($paymentData['payment_code'])) {
            Log::error('Midtrans tidak mengembalikan nomor VA', [
                'order_id' => $orderId,
                'payment' => $validated['payment'],
                'transaction_status' => $paymentData['transaction_status'],
            ]);

            return response()->json([
                'code' => 502,
                'status' => false,
                'message' => 'Nomor Virtual Account belum diterima dari Midtrans.',
            ], 502);
        }

        $transactionStatus = $paymentData['transaction_status'];

        $paymentStatus = in_array(
            $transactionStatus,
            ['pending', 'settlement', 'capture', 'deny', 'cancel', 'expire'],
            true
        ) ? $transactionStatus : 'pending';

        $orderStatus = in_array(
            $transactionStatus,
            ['settlement', 'capture'],
            true
        ) ? 'paid' : (
            in_array($transactionStatus, ['expire', 'cancel', 'deny'], true)
                ? $transactionStatus
                : 'pending'
        );

        // Simpan order beserta detail pembayaran dari Midtrans.
        $order = MidtransOrder::create([
            'order_id' => $orderId,
            'id_m_customer' => $validated['id_m_customer'],
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],

            'subtotal_produk' => $subtotal,
            'ekspedisi_code' => $validated['ekspedisi_code'],
            'ekspedisi_name' => $validated['ekspedisi_name'],
            'ekspedisi_price' => $shipping,
            'total' => $total,

            'payment_type' => $validated['payment_type'],
            'payment' => $validated['payment'],

            'payment_code' => $paymentData['payment_code'],
            'transaction_time' => $paymentData['transaction_time'],
            'expiry_time' => $paymentData['expiry_time'],

            'address_shipping' => $validated['address_shipping'],
            'state' => $validated['state'],
            'city' => $validated['city'],
            'postcode' => $validated['postcode'],
            'country' => $validated['country'],

            // Core API tidak menghasilkan Snap token/redirect URL.
            'snap_token' => null,
            'redirect_url' => null,

            'status' => $orderStatus,
            'payment_status' => $paymentStatus,
        ]);

        // Buat nomor pesanan pendek berdasarkan ID database.
$orderDate = ($order->created_at ?? now())->format('ymd');

$orderNumber = 'CN-' . $orderDate . '-' .
    str_pad(
        (string) $order->getKey(),
        6,
        '0',
        STR_PAD_LEFT
    );

// Simpan nomor pesanan.
$order->order_number = $orderNumber;
$order->save();

        return response()->json([
            'code' => 201,
            'status' => true,
            'message' => 'Transaksi Virtual Account berhasil dibuat.',
            'data' => [
                'order_id' => $order->order_id,
                 'order_number' => $order->order_number,
                'id_m_customer' => $order->id_m_customer,
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,

                'subtotal_produk' => (float) $order->subtotal_produk,

                'ekspedisi' => [
                    'code' => $order->ekspedisi_code,
                    'name' => $order->ekspedisi_name,
                    'price' => (float) $order->ekspedisi_price,
                ],

                'total' => (float) $order->total,

                'payment_type' => $order->payment_type,
                'payment' => $order->payment,
                'paymentCode' => $order->payment_code,
                'transactionTime' => $order->transaction_time,
                'expiry_time' => $order->expiry_time,

                'payment_status' => $order->payment_status,
                'status' => $order->status,
            ],
        ], 201);

    } catch (\Throwable $e) {
        Log::error('Checkout Midtrans Core API gagal', [
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'id_m_customer' => $request->input('id_m_customer'),
        ]);

        return response()->json([
            'code' => 500,
            'status' => false,
            'message' => 'Gagal membuat transaksi pembayaran.',
        ], 500);
    }
}

public function history(string $idMCustomer)
{
    $orders = MidtransOrder::where(
        'id_m_customer',
        $idMCustomer
    )
        ->latest()
        ->paginate(10);

    return response()->json([
        'code' => 200,
        'status' => true,
        'message' => 'Riwayat transaksi berhasil diambil.',

        'data' => $orders->getCollection()->map(function ($order) {
            return [
                'order_id' => $order->order_id,
                'id_m_customer' => $order->id_m_customer,
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,

                'subtotal_produk' => (float) $order->subtotal_produk,
'paymentCode' => $order->payment_code,
'transactionTime' => $order->transaction_time,
                'ekspedisi_code' => $order->ekspedisi_code,
                'ekspedisi_name' => $order->ekspedisi_name,
                'ekspedisi_price' => (float) $order->ekspedisi_price,

                'total' => (float) $order->total,

                'payment_type' => $order->payment_type,
                'payment' => $order->payment,

                'status' => $order->status,
                'payment_status' => $order->payment_status,

                'created_at' => $order->created_at,
            ];
        })->values(),

        'pagination' => [
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
            'per_page' => $orders->perPage(),
            'total' => $orders->total(),
        ],
    ]);
}

public function historyDetail(
    string $idMCustomer,
    string $orderId
) {
    $order = \App\Models\MidtransOrder::where(
        'id_m_customer',
        $idMCustomer
    )
        ->where('order_id', $orderId)
        ->first();

    if (!$order) {
        return response()->json([
            'code' => 404,
            'status' => false,
            'message' => 'Detail transaksi tidak ditemukan.',
            'data' => null,
        ], 404);
    }

    return response()->json([
        'code' => 200,
        'status' => true,
        'message' => 'Detail transaksi berhasil diambil.',
        'data' => [
            'order_id' => $order->order_id,
            'id_m_customer' => $order->id_m_customer,
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
'paymentCode' => $order->payment_code,
'transactionTime' => $order->transaction_time,
            'subtotal_produk' => (float) $order->subtotal_produk,

            'ekspedisi' => [
                'code' => $order->ekspedisi_code,
                'name' => $order->ekspedisi_name,
                'price' => (float) $order->ekspedisi_price,
            ],

            'total' => (float) $order->total,

            'payment_type' => $order->payment_type,
            'payment' => $order->payment,
            'payment_status' => $order->payment_status,
            'status' => $order->status,

            'address_shipping' => $order->address_shipping,
            'state' => $order->state,
            'city' => $order->city,
            'postcode' => $order->postcode,
            'country' => $order->country,

            'snap_token' => $order->snap_token,
            'redirect_url' => $order->redirect_url,
            'expiry_time' => $order->expiry_time,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
        ],
    ]);

    }




public function callback2(Request $request)
{
    $payload = $request->all();

    /*
    |--------------------------------------------------------------------------
    | 1. Validasi field wajib
    |--------------------------------------------------------------------------
    */
    $required = [
        'order_id',
        'status_code',
        'gross_amount',
        'signature_key',
        'transaction_status',
    ];

    foreach ($required as $field) {
        if (
            !array_key_exists($field, $payload) ||
            !is_scalar($payload[$field]) ||
            trim((string) $payload[$field]) === ''
        ) {
            return response()->json([
                'status' => false,
                'message' => "Field {$field} wajib tersedia.",
            ], 400);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Ambil Server Key
    |--------------------------------------------------------------------------
    */
    $serverKey = config('services.midtrans.server_key');

    if (empty($serverKey)) {
        Log::error('Server Key Midtrans belum dikonfigurasi.');

        return response()->json([
            'status' => false,
            'message' => 'Konfigurasi pembayaran belum tersedia.',
        ], 500);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Verifikasi signature Midtrans
    |--------------------------------------------------------------------------
    | Rumus:
    | SHA512(order_id + status_code + gross_amount + Server Key)
    */
    $orderId = (string) $payload['order_id'];
    $statusCode = (string) $payload['status_code'];
    $grossAmount = (string) $payload['gross_amount'];
    $receivedSignature = (string) $payload['signature_key'];

    $expectedSignature = hash(
        'sha512',
        $orderId . $statusCode . $grossAmount . $serverKey
    );

    if (!hash_equals($expectedSignature, $receivedSignature)) {
        Log::warning('Signature callback Midtrans tidak valid.', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
        ]);

        return response()->json([
            'status' => false,
            'message' => 'Signature tidak valid.',
        ], 403);
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Validasi format nominal
    |--------------------------------------------------------------------------
    */
    if (
        !is_numeric($grossAmount) ||
        (float) $grossAmount < 0
    ) {
        return response()->json([
            'status' => false,
            'message' => 'Nominal transaksi tidak valid.',
        ], 400);
    }

    try {
        $result = DB::transaction(function () use ($payload, $orderId, $grossAmount) {

            /*
            |--------------------------------------------------------------------------
            | 5. Cari dan kunci transaksi
            |--------------------------------------------------------------------------
            */
            $order = MidtransOrder::where('order_id', $orderId)
                ->lockForUpdate()
                ->first();

            if (!$order) {
                throw new \RuntimeException(
                    'Transaksi tidak ditemukan: ' . $orderId
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 6. Cocokkan nominal dengan database
            |--------------------------------------------------------------------------
            | Perbandingan menggunakan dua angka desimal.
            */
            if (
                number_format((float) $order->total, 2, '.', '') !==
                number_format((float) $grossAmount, 2, '.', '')
            ) {
                throw new \RuntimeException(
                    'Nominal transaksi tidak sesuai dengan database.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 7. Petakan status transaksi
            |--------------------------------------------------------------------------
            */
            $transactionStatus = strtolower(
                (string) $payload['transaction_status']
            );

            $fraudStatus = strtolower(
                (string) ($payload['fraud_status'] ?? '')
            );

            switch ($transactionStatus) {
                case 'settlement':
                    $paymentStatus = 'paid';
                    $orderStatus = 'paid';
                    break;

                case 'capture':
                    if ($fraudStatus === 'accept') {
                        $paymentStatus = 'paid';
                        $orderStatus = 'paid';
                    } elseif ($fraudStatus === 'challenge') {
                        $paymentStatus = 'pending';
                        $orderStatus = 'pending';
                    } else {
                        $paymentStatus = 'failed';
                        $orderStatus = 'failed';
                    }
                    break;

                case 'pending':
                    $paymentStatus = 'pending';
                    $orderStatus = 'pending';
                    break;

                case 'expire':
                    $paymentStatus = 'expired';
                    $orderStatus = 'expired';
                    break;

                case 'cancel':
                case 'deny':
                case 'failure':
                    $paymentStatus = 'failed';
                    $orderStatus = 'failed';
                    break;

                default:
                    Log::info(
                        'Status transaksi Midtrans belum dipetakan.',
                        [
                            'order_id' => $orderId,
                            'transaction_status' => $transactionStatus,
                        ]
                    );

                    return 'ignored';
            }

            /*
            |--------------------------------------------------------------------------
            | 8. Lindungi transaksi yang sudah dibayar
            |--------------------------------------------------------------------------
            | Callback lama tidak boleh menurunkan status paid.
            */
            if (
                strtolower((string) $order->payment_status) === 'paid' &&
                $paymentStatus !== 'paid'
            ) {
                Log::info(
                    'Callback diabaikan karena transaksi sudah dibayar.',
                    ['order_id' => $orderId]
                );

                return 'already_paid';
            }

            /*
            |--------------------------------------------------------------------------
            | 9. Perbarui status transaksi
            |--------------------------------------------------------------------------
            */
            $order->payment_status = $paymentStatus;
            $order->status = $orderStatus;

            if (!empty($payload['transaction_id'])) {
                $order->payment_transaction_id =
                    $payload['transaction_id'];
            }

            if (!empty($payload['transaction_time'])) {
                $order->transaction_time =
                    $payload['transaction_time'];
            }

            $order->save();

            return 'updated';
        });

        /*
        |--------------------------------------------------------------------------
        | 10. Respons sukses
        |--------------------------------------------------------------------------
        */
        return response()->json([
            'status' => true,
            'message' => match ($result) {
                'updated' => 'Callback Midtrans berhasil diproses.',
                'already_paid' => 'Transaksi sudah dibayar. Status dipertahankan.',
                'ignored' => 'Status transaksi tidak mengubah data pesanan.',
                default => 'Callback berhasil diproses.',
            },
        ], 200);

    } catch (\Throwable $e) {

        /*
        |--------------------------------------------------------------------------
        | 11. Catat error tanpa membocorkan detail ke publik
        |--------------------------------------------------------------------------
        */
        Log::error('Gagal memproses callback Midtrans.', [
            'order_id' => $orderId,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'status' => false,
            'message' => 'Callback gagal diproses.',
            'debug' => app()->environment('local')
                ? $e->getMessage()
                : null,
        ], 500);
    }
}


}   
