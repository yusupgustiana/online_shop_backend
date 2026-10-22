<?php

// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use App\Http\Requests\StoreOrderRequest;
// use App\Services\OrderService;
// use App\Services\MidtransService;
// use App\Models\Order;
// use App\Notifications\OrderStatusNotification;
// class OrderControllerApi extends Controller
// {
//     protected $orderService;
//     protected $midtransService;

//     public function __construct(OrderService $orderService, MidtransService $midtransService)
//     {
//         $this->orderService = $orderService;
//         $this->midtransService = $midtransService;
//     }

//     // ✅ CREATE ORDER (SUDAH BERSIH)
//     public function store(StoreOrderRequest $request)
//     {
//         try {

//             $order = $this->orderService->create(
//                 $request,
//                 auth()->user(),
//                 $this->midtransService
//             );
//         // Notifikasi pesanan berhasil dibuat
//         $user->notify(new OrderStatusNotification(
//             'Pesanan berhasil dibuat',
//             'Pesanan Anda berhasil dibuat dan menunggu pembayaran.',
//             $order->id,
//             $order->status ?? 'pending'
//         ));
//             return response()->json([
//                 'message' => 'Order created successfully',
//                 'order' => $order
//             ], 201);

//         } catch (\Exception $e) {

//             return response()->json([
//                 'message' => 'Order failed',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     // ✅ HISTORY (SUDAH OK, SEDIKIT DIRAPIKAN)
  
// public function history()
// {
//     $orders = Order::with([
//         'orderItems.product',
//         'address',
//     ])
//     ->where('user_id', auth()->id())
//     ->latest()
//     ->get();

//     return response()->json([
//         'message' => 'Order history',
//         'orders' => $orders,
//     ]);
// }


//     // ✅ DETAIL
//     public function show($id)
//     {
//         $order = Order::with([
//             'orderItems.product',
//             'address.province',
//             'address.city',
//             'address.district'
//         ])
//         ->where('user_id', auth()->id())
//         ->findOrFail($id);

//         return response()->json([
//             'message' => 'Order detail',
//             'order' => $order
//         ]);
//     }

//     // ✅ MIDTRANS CALLBACK (TAMBAHAN PENTING 🔥)
//     public function callback(\Illuminate\Http\Request $request)
//     {
//         $serverKey = config('midtrans.server_key');

//         $hashed = hash("sha512",
//             $request->order_id .
//             $request->status_code .
//             $request->gross_amount .
//             $serverKey
//         );

//         if ($hashed !== $request->signature_key) {
//             return response()->json(['message' => 'Invalid signature'], 403);
//         }

//         $order = Order::where('transaction_number', $request->order_id)->first();

//         if (!$order) {
//             return response()->json(['message' => 'Order not found']);
//         }

//         switch ($request->transaction_status) {
//             case 'settlement':
//                 $order->update(['status' => 'paid']);
//                 break;

//             case 'expire':
//                 $order->update(['status' => 'expired']);
//                 break;

//             case 'cancel':
//                 $order->update(['status' => 'failed']);
//                 break;
//         }

//         return response()->json(['message' => 'OK']);
//     }
// }





namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;
use App\Services\MidtransService;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\FcmService;
class OrderControllerApi extends Controller
{
    protected $orderService;
    protected $midtransService;
    protected $fcmService;
    public function __construct(
        OrderService $orderService,
        MidtransService $midtransService,
        FcmService $fcmService
    ) {
        $this->orderService = $orderService;
        $this->midtransService = $midtransService;
        $this->fcmService = $fcmService;
    }

    // CREATE ORDER

public function store(StoreOrderRequest $request)
{
    $user = $request->user();

    try {
        $order = $this->orderService->create(
            $request,
            $user,
            $this->midtransService
        );

        $isCod = $order->payment_method === 'cod';

        $title = 'Pesanan berhasil dibuat';

        $message = $isCod
            ? "Pesanan #{$order->id} berhasil dibuat."
            : "Pesanan #{$order->id} berhasil dibuat dan menunggu pembayaran.";

        // Push notification ke aplikasi Flutter melalui FCM.
        try {
            $this->fcmService->sendToUser(
                $user,
                $title,
                $message,
                [
                    'type' => 'order_created',
                    'order_id' => (string) $order->id,
                    'status' => (string) $order->status,
                ]
            );
        } catch (\Throwable $notificationError) {
            Log::error('Gagal mengirim FCM saat order dibuat', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'error' => $notificationError->getMessage(),
            ]);
        }

        // Pertahankan notifikasi Laravel yang sudah ada,
        // jika memang digunakan oleh aplikasi.
        try {
            $user->notify(new OrderStatusNotification(
                $title,
                $message,
                $order->id,
                $order->status ?? 'pending'
            ));
        } catch (\Throwable $notificationError) {
            Log::error('Gagal mengirim Laravel notification', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'error' => $notificationError->getMessage(),
            ]);
        }

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order,
        ], 201);

    } catch (\Throwable $e) {
        report($e);

        return response()->json([
            'message' => 'Order failed',
            'error' => 'Terjadi kesalahan saat membuat pesanan.',
        ], 500);
    }
}


    // ORDER HISTORY
    public function history(Request $request)
    {
        $orders = Order::with([
            'orderItems.product',
            'address',
        ])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Order history',
            'orders' => $orders,
        ]);
    }

    // ORDER DETAIL
    public function show(Request $request, $id)
    {
        $order = Order::with([
            'orderItems.product',
            'address',
        ])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'message' => 'Order detail',
            'order' => $order,
        ]);
    }

    // MIDTRANS CALLBACK
    public function callback(Request $request)
    {
        $request->validate([
            'order_id' => ['required', 'string'],
            'status_code' => ['required', 'string'],
            'gross_amount' => ['required'],
            'signature_key' => ['required', 'string'],
            'transaction_status' => ['required', 'string'],
        ]);

        $serverKey = config('midtrans.server_key');

        $hashed = hash(
            'sha512',
            $request->order_id .
            $request->status_code .
            $request->gross_amount .
            $serverKey
        );

        if (! hash_equals($hashed, $request->signature_key)) {
            return response()->json([
                'message' => 'Invalid signature',
            ], 403);
        }

       
$order = Order::where(
    'transaction_number',
    $request->order_id
)->first();

if (! $order) {
    return response()->json([
        'message' => 'Order not found',
    ], 404);
}

$statusMap = [
    'settlement' => [
        'status' => 'paid',
        'title' => 'Pembayaran berhasil',
        'message' => 'Pembayaran pesanan Anda berhasil dikonfirmasi.',
    ],
    'expire' => [
        'status' => 'expired',
        'title' => 'Pembayaran kedaluwarsa',
        'message' => 'Batas waktu pembayaran pesanan Anda telah berakhir.',
    ],
    'cancel' => [
        'status' => 'failed',
        'title' => 'Pembayaran dibatalkan',
        'message' => 'Pembayaran pesanan Anda dibatalkan.',
    ],
    'deny' => [
        'status' => 'failed',
        'title' => 'Pembayaran gagal',
        'message' => 'Pembayaran pesanan Anda ditolak.',
    ],
];

$transactionStatus = $request->transaction_status;

// Status pending atau status lain yang tidak ada di mapping
// tidak mengubah status order melalui bagian ini.
if (! isset($statusMap[$transactionStatus])) {
    return response()->json([
        'message' => 'OK',
    ]);
}

$newStatus = $statusMap[$transactionStatus];

// Update database dahulu. Simpan informasi notifikasi
// untuk dikirim setelah transaksi berhasil.
$notificationData = DB::transaction(function () use ($order, $newStatus) {
    $lockedOrder = Order::whereKey($order->id)
        ->lockForUpdate()
        ->firstOrFail();

    // Callback yang sama tidak memicu notifikasi berulang.
    if ($lockedOrder->status === $newStatus['status']) {
        return null;
    }

    $lockedOrder->update([
        'status' => $newStatus['status'],
    ]);

    return [
        'order_id' => $lockedOrder->id,
        'user_id' => $lockedOrder->user_id,
        'status' => $newStatus['status'],
        'title' => $newStatus['title'],
        'message' => $newStatus['message'],
    ];
});

// Kirim notifikasi setelah transaksi database selesai.
if ($notificationData !== null) {
    $user = User::find($notificationData['user_id']);

    if ($user) {
        try {
            $this->fcmService->sendToUser(
                $user,
                $notificationData['title'],
                $notificationData['message'],
                [
                    'type' => 'payment_status',
                    'order_id' => (string) $notificationData['order_id'],
                    'status' => $notificationData['status'],
                ]
            );
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim FCM dari callback Midtrans', [
                'order_id' => $notificationData['order_id'],
                'user_id' => $notificationData['user_id'],
                'error' => $e->getMessage(),
            ]);
        }

        // Opsional: pertahankan notifikasi Laravel.
        try {
            $user->notify(new OrderStatusNotification(
                $notificationData['title'],
                $notificationData['message'],
                $notificationData['order_id'],
                $notificationData['status']
            ));
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim Laravel notification dari callback', [
                'order_id' => $notificationData['order_id'],
                'user_id' => $notificationData['user_id'],
                'error' => $e->getMessage(),
            ]);
        }
    }
}

return response()->json([
    'message' => 'OK',
]);
    }
}
