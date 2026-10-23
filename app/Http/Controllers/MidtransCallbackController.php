<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderStatusNotification;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class MidtransCallbackController extends Controller
{
    protected FcmService $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    public function handle(Request $request)
    {    Log::warning('MIDTRANS_CALLBACK_CONTROLLER_AKTIF', [
        'time' => now()->toDateTimeString(),
        'order_id' => $request->input('order_id'),
        'transaction_status' => $request->input('transaction_status'),
    ]);
        // 1. Validasi payload callback.
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'string'],
            'status_code' => ['required', 'string'],
            'gross_amount' => ['required', 'numeric', 'min:0'],
            'signature_key' => ['required', 'string'],
            'transaction_status' => ['required', 'string'],
            'fraud_status' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid callback payload',
                'errors' => $validator->errors(),
            ], 422);
        }

        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        $grossAmount = $request->input('gross_amount');
        $signatureKey = $request->input('signature_key');
        $transactionStatus = $request->input('transaction_status');
        $fraudStatus = $request->input('fraud_status');

        // Jangan mencatat signature atau Server Key ke log.
        Log::info('Midtrans callback received', [
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
            'status_code' => $statusCode,
        ]);

        // 2. Verifikasi signature Midtrans.
        $serverKey = config('services.midtrans.server_key');

        if (empty($serverKey)) {
            Log::error('Midtrans Server Key is not configured');

            return response()->json([
                'message' => 'Payment gateway configuration error',
            ], 500);
        }

        $expectedSignature = hash(
            'sha512',
            $orderId . $statusCode . $grossAmount . $serverKey
        );

        if (! hash_equals($expectedSignature, $signatureKey)) {
            Log::warning('Midtrans invalid signature', [
                'order_id' => $orderId,
            ]);

            return response()->json([
                'message' => 'Invalid signature',
            ], 403);
        }

        try {
            // 3. Cari pesanan dan proses perubahan status
            // secara atomik untuk mencegah callback bersamaan
            // mengubah data berdasarkan status lama.
            $result = DB::transaction(function () use (
                $orderId,
                $grossAmount,
                $transactionStatus,
                $fraudStatus
            ) {
                $order = Order::where(
                    'transaction_number',
                    $orderId
                )->lockForUpdate()->first();

                if (! $order) {
                    return ['error' => 'not_found'];
                }

                // 4. Pastikan nominal callback cocok dengan pesanan.
                // Sesuaikan total_cost jika nama kolom Anda berbeda.
                $expectedAmount = number_format(
                    (float) $order->total_cost,
                    2,
                    '.',
                    ''
                );

                $receivedAmount = number_format(
                    (float) $grossAmount,
                    2,
                    '.',
                    ''
                );

                if ($expectedAmount !== $receivedAmount) {
                    Log::warning('Midtrans amount mismatch', [
                        'order_id' => $orderId,
                        'expected_amount' => $expectedAmount,
                        'received_amount' => $receivedAmount,
                    ]);

                    return ['error' => 'amount_mismatch'];
                }

                // 5. Tentukan status berdasarkan respons Midtrans.
                $newStatus = null;

                switch ($transactionStatus) {
                    case 'settlement':
                        $newStatus = 'paid';
                        break;

                    case 'capture':
                        if ($fraudStatus === 'accept') {
                            $newStatus = 'paid';
                        } elseif ($fraudStatus === 'challenge') {
                            $newStatus = 'challenge';
                        } else {
                            Log::warning(
                                'Midtrans capture with unknown fraud status',
                                [
                                    'order_id' => $orderId,
                                    'fraud_status' => $fraudStatus,
                                ]
                            );

                            return ['ignored' => true];
                        }
                        break;

                    case 'pending':
                        $newStatus = 'pending';
                        break;

                    case 'deny':
                        $newStatus = 'failed';
                        break;

                    case 'expire':
                        $newStatus = 'expired';
                        break;

                    case 'cancel':
                        $newStatus = 'cancelled';
                        break;

                    default:
                        Log::warning('Unknown Midtrans status', [
                            'order_id' => $orderId,
                            'transaction_status' => $transactionStatus,
                        ]);

                        return ['ignored' => true];
                }

                $currentStatus = $order->status;

                // 6. Jangan menimpa status paid dengan callback lama.
                if ($currentStatus === 'paid') {
                    return ['unchanged' => true];
                }

                // Status pending atau kegagalan yang datang terlambat
                // tidak boleh menimpa status final.
                // Status paid dari settlement tetap boleh memperbaiki
                // status expired/failed/cancelled jika Midtrans
                // mengonfirmasi pembayaran berhasil.
                $finalStatuses = [
                    'expired',
                    'failed',
                    'cancelled',
                ];

                if (
                    in_array($currentStatus, $finalStatuses, true)
                    && $newStatus !== 'paid'
                ) {
                    return ['unchanged' => true];
                }

                // Callback dengan status yang sama tidak perlu
                // mengirim notifikasi lagi.
                if ($currentStatus === $newStatus) {
                    return ['unchanged' => true];
                }

                $statusMessages = [
                    'paid' => [
                        'title' => 'Pembayaran berhasil',
                        'message' => 'Pembayaran pesanan Anda berhasil dikonfirmasi.',
                    ],
                    'pending' => [
                        'title' => 'Menunggu pembayaran',
                        'message' => 'Pesanan Anda masih menunggu pembayaran.',
                    ],
                    'challenge' => [
                        'title' => 'Pembayaran sedang ditinjau',
                        'message' => 'Pembayaran Anda sedang menjalani pemeriksaan.',
                    ],
                    'failed' => [
                        'title' => 'Pembayaran gagal',
                        'message' => 'Pembayaran pesanan Anda ditolak.',
                    ],
                    'expired' => [
                        'title' => 'Pembayaran kedaluwarsa',
                        'message' => 'Batas waktu pembayaran pesanan Anda telah berakhir.',
                    ],
                    'cancelled' => [
                        'title' => 'Pembayaran dibatalkan',
                        'message' => 'Pembayaran pesanan Anda dibatalkan.',
                    ],
                ];

                $order->status = $newStatus;
                $order->save();

                return [
                    'updated' => true,
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'status' => $newStatus,
                    'title' => $statusMessages[$newStatus]['title'],
                    'message' => $statusMessages[$newStatus]['message'],
                ];
            });

            // 7. Tangani hasil transaksi database.
            if (($result['error'] ?? null) === 'not_found') {
                Log::error('Midtrans order not found', [
                    'order_id' => $orderId,
                ]);

                return response()->json([
                    'message' => 'Order not found',
                ], 404);
            }

            if (($result['error'] ?? null) === 'amount_mismatch') {
                return response()->json([
                    'message' => 'Transaction amount mismatch',
                ], 422);
            }

            if (
                isset($result['ignored'])
                || isset($result['unchanged'])
            ) {
                return response()->json([
                    'message' => 'Callback acknowledged',
                ]);
            }

            // 8. Kirim notifikasi setelah transaksi database berhasil.
            if (! empty($result['updated'])) {
                Log::info('Midtrans order status updated', [
                    'order_id' => $orderId,
                    'status' => $result['status'],
                ]);
Log::info('Memulai proses notifikasi pembayaran', [
    'order_id' => $orderId,
    'user_id' => $result['user_id'],
    'status' => $result['status'],
]);
                $user = User::find($result['user_id']);

                if ($user) {
                    // Firebase Cloud Messaging.
                    try {
                        $this->fcmService->sendToUser(
                            $user,
                            $result['title'],
                            $result['message'],
                            [
                                'type' => 'payment_status',
                                'order_id' => (string) $result['order_id'],
                                'status' => $result['status'],
                            ]
                        );
                    } catch (Throwable $e) {
                        Log::error(
                            'Failed to send FCM from Midtrans callback',
                            [
                                'order_id' => $orderId,
                                'error' => $e->getMessage(),
                            ]
                        );
                    }

                    // Laravel Notification.
                    try {
                        $user->notify(
                            new OrderStatusNotification(
                                $result['title'],
                                $result['message'],
                                $result['order_id'],
                                $result['status']
                            )
                        );
                    } catch (Throwable $e) {
                        Log::error(
                            'Failed to send Laravel notification',
                            [
                                'order_id' => $orderId,
                                'error' => $e->getMessage(),
                            ]
                        );
                    }
                }
            }

            return response()->json([
                'message' => 'Callback processed',
            ]);
        } catch (Throwable $e) {
            Log::error('Midtrans callback error', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Callback processing failed',
            ], 500);
        }
    }
}
