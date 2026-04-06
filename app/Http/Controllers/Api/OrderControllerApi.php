<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;
use App\Services\MidtransService;
use App\Models\Order;

class OrderControllerApi extends Controller
{
    protected $orderService;
    protected $midtransService;

    public function __construct(OrderService $orderService, MidtransService $midtransService)
    {
        $this->orderService = $orderService;
        $this->midtransService = $midtransService;
    }

    // ✅ CREATE ORDER (SUDAH BERSIH)
    public function store(StoreOrderRequest $request)
    {
        try {

            $order = $this->orderService->create(
                $request,
                auth()->user(),
                $this->midtransService
            );

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Order failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ HISTORY (SUDAH OK, SEDIKIT DIRAPIKAN)
    public function history()
    {
        $orders = Order::with([
            'orderItems.product',
            'address.province',
            'address.city',
            'address.district'
        ])
        ->where('user_id', auth()->id())
        ->latest()
        ->get();

        return response()->json([
            'message' => 'Order history',
            'orders' => $orders
        ]);
    }

    // ✅ DETAIL
    public function show($id)
    {
        $order = Order::with([
            'orderItems.product',
            'address.province',
            'address.city',
            'address.district'
        ])
        ->where('user_id', auth()->id())
        ->findOrFail($id);

        return response()->json([
            'message' => 'Order detail',
            'order' => $order
        ]);
    }

    // ✅ MIDTRANS CALLBACK (TAMBAHAN PENTING 🔥)
    public function callback(\Illuminate\Http\Request $request)
    {
        $serverKey = config('midtrans.server_key');

        $hashed = hash("sha512",
            $request->order_id .
            $request->status_code .
            $request->gross_amount .
            $serverKey
        );

        if ($hashed !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $order = Order::where('transaction_number', $request->order_id)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found']);
        }

        switch ($request->transaction_status) {
            case 'settlement':
                $order->update(['status' => 'paid']);
                break;

            case 'expire':
                $order->update(['status' => 'expired']);
                break;

            case 'cancel':
                $order->update(['status' => 'failed']);
                break;
        }

        return response()->json(['message' => 'OK']);
    }
}