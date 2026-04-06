<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class MidtransCallbackController extends Controller
{
    public function handle(Request $request)
    {
        try {

            // log semua request dari Midtrans
            Log::info('Midtrans Callback Request', $request->all());

            $orderId           = $request->input('order_id');
            $statusCode        = $request->input('status_code');
            $grossAmount       = $request->input('gross_amount');
            $signatureKey      = $request->input('signature_key');
            $transactionStatus = $request->input('transaction_status');
            $fraudStatus       = $request->input('fraud_status');

            // ambil server key dari config
            $serverKey = config('services.midtrans.server_key');

            // generate signature
            $mySignature = hash(
                'sha512',
                $orderId . $statusCode . $grossAmount . $serverKey
            );

            // verifikasi signature
            if ($signatureKey !== $mySignature) {

                Log::warning('Midtrans Invalid Signature', [
                    'order_id' => $orderId,
                    'signature_midtrans' => $signatureKey,
                    'signature_local' => $mySignature
                ]);

                return response()->json([
                    'message' => 'Invalid signature'
                ], 403);
            }

            // cari order
            $order = Order::where('transaction_number', $orderId)->first();

            if (!$order) {

                Log::error('Midtrans Order Not Found', [
                    'order_id' => $orderId
                ]);

                return response()->json([
                    'message' => 'Order not found'
                ], 404);
            }

            // jika sudah paid tidak perlu update lagi
            if ($order->status === 'paid') {

                Log::info('Order already paid', [
                    'order_id' => $orderId
                ]);

                return response()->json([
                    'message' => 'Order already paid'
                ]);
            }

            // mapping status midtrans → order status
            switch ($transactionStatus) {

                case 'capture':

                    if ($fraudStatus === 'challenge') {
                        $order->status = 'challenge';
                    } else {
                        $order->status = 'paid';
                    }

                    break;

                case 'settlement':
                    $order->status = 'paid';
                    break;

                case 'pending':
                    $order->status = 'pending';
                    break;

                case 'deny':
                    $order->status = 'failed';
                    break;

                case 'expire':
                    $order->status = 'expired';
                    break;

                case 'cancel':
                    $order->status = 'cancelled';
                    break;

                default:
                    Log::warning('Unknown Midtrans Status', [
                        'status' => $transactionStatus
                    ]);
                    break;
            }

            $order->save();

            Log::info('Midtrans Callback Success', [
                'order_id' => $orderId,
                'status' => $order->status
            ]);

            return response()->json([
                'message' => 'Callback processed'
            ]);

        } catch (\Exception $e) {

            Log::error('Midtrans Callback Error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Callback error'
            ], 500);
        }
    }
}