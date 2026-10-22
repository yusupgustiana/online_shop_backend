<?php

// namespace App\Http\Controllers\User;

// use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
// use App\Models\Cart;
// use App\Models\Address;
// use App\Models\Order;
// use App\Services\MidtransService;

// class CheckoutControllerUser extends Controller
// {
//     // Halaman checkout: hanya item yang dipilih
//     public function index(Request $request)
//     {
//         $selectedCartIds = $request->cart_ids ?? [];

//         $cart = Cart::with('product')
//             ->where('user_id', auth()->id())
//             ->whereIn('id', $selectedCartIds)
//             ->get();

//         if ($cart->isEmpty()) {
//             return redirect()->route('user.cart.index')
//                 ->with('error', 'Pilih produk terlebih dahulu');
//         }

//         $total = $cart->sum(fn($item) => $item->product->price * $item->quantity);

//         $addresses = Address::where('user_id', auth()->id())
//             ->get();

//         return view('user.checkout.index', compact(
//             'cart',
//             'total',
//             'addresses',
//             'selectedCartIds'
//         ));
//     }

//     // Proses checkout
//     public function process(Request $request, MidtransService $midtrans)
//     {
//         $request->validate([
//             'address_id' => 'required|exists:addresses,id',
//             'payment_method' => 'required',
//             'selected_items' => 'required|array'
//         ]);

//         // Ambil hanya cart yang diceklis
//         $cart = Cart::with('product')
//             ->where('user_id', auth()->id())
//             ->whereIn('id', $request->selected_items)
//             ->get();

//         if ($cart->isEmpty()) {
//             return back()->with('error', 'Tidak ada item dipilih');
//         }

//         // Hitung subtotal dari item terpilih
//         $subtotal = $cart->sum(fn($item) => $item->product->price * $item->quantity);
//         $shipping = $request->shipping_cost ?? 0;
//         $total = $subtotal + $shipping;

//         // Buat order
//         $order = Order::create([
//             'user_id' => auth()->id(),
//             'address_id' => $request->address_id,
//             'transaction_number' => 'TRX-' . time(),
//             'subtotal' => $subtotal,
//             'shipping_cost' => $shipping,
//             'total_cost' => $total,
//             'status' => 'pending',
//             'payment_method' => $request->payment_method,
//             'shipping_service' => $request->courier . ' - ' . $request->service,
//         ]);

//         // Simpan item order
//         foreach ($cart as $item) {
//             $order->orderItems()->create([
//                 'product_id' => $item->product_id,
//                 'price' => $item->product->price,
//                 'quantity' => $item->quantity,
//                 'subtotal' => $item->product->price * $item->quantity,
//             ]);
//         }

//         // Hapus hanya item yang diceklis
//         Cart::whereIn('id', $request->selected_items)->delete();

//         // Redirect ke halaman pembayaran
//         return redirect()->route('user.payment.show', $order->id)
//             ->with('success', 'Checkout berhasil, silakan lakukan pembayaran');
//     }
// }




namespace App\Http\Controllers\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Address;
use App\Models\Order;
use App\Services\MidtransService;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;
class CheckoutControllerUser extends Controller
{
    /**
     * Halaman checkout.
     * Hanya menampilkan item keranjang yang dipilih.
     */
    public function index(Request $request)
    {
        $selectedCartIds = $request->input('cart_ids', []);

        if (!is_array($selectedCartIds)) {
            $selectedCartIds = [];
        }

        $cart = Cart::with('product')
            ->where('user_id', auth()->id())
            ->whereIn('id', $selectedCartIds)
            ->get();

        // Pastikan semua ID yang dipilih benar-benar milik user.
        if (
            empty($selectedCartIds) ||
            $cart->isEmpty() ||
            $cart->count() !== count(array_unique($selectedCartIds))
        ) {
            return redirect()
                ->route('user.cart.index')
                ->with('error', 'Pilih produk keranjang yang valid.');
        }

        $total = $cart->sum(
            fn ($item) => $item->product->price * $item->quantity
        );

        $addresses = Address::where('user_id', auth()->id())->get();

        return view('user.checkout.index', compact(
            'cart',
            'total',
            'addresses',
            'selectedCartIds'
        ));
    }

    /**
     * Memproses checkout melalui OrderService.
     */
    public function process(
        Request $request,
        OrderService $orderService,
        \App\Services\MidtransService $midtrans
    ) {
        $validated = $request->validate([
            'address_id' => [
                'required',
                'integer',
                Rule::exists('addresses', 'id')
                    ->where('user_id', auth()->id()),
            ],

            'selected_items' => ['required', 'array', 'min:1'],
            'selected_items.*' => ['required', 'integer', 'distinct'],

            'shipping_cost' => [
                'required',
                'numeric',
                'min:0',
            ],

            'payment_method' => [
                'required',
                Rule::in(['cod', 'bca', 'bni', 'bri', 'permata']),
            ],

     'bank' => [
    'nullable',
    'required_if:payment_method,bank_transfer',
    Rule::in(['bca', 'bni', 'bri', 'permata']),
],

            'courier' => ['required', 'string', 'max:100'],
            'service' => ['required', 'string', 'max:100'],
        ]);

        $user = $request->user();

        // Ambil hanya keranjang milik pengguna yang login.
        $cart = Cart::with('product')
            ->where('user_id', $user->id)
            ->whereIn('id', $validated['selected_items'])
            ->get();

        // Pastikan tidak ada ID keranjang milik user lain atau ID palsu.
        if (
            $cart->isEmpty() ||
            $cart->count() !== count($validated['selected_items'])
        ) {
            return back()
                ->withInput()
                ->with('error', 'Item keranjang tidak valid. Silakan checkout ulang.');
        }

        // Pastikan alamat masih milik user yang sedang login.
        $address = Address::where('user_id', $user->id)
            ->where('id', $validated['address_id'])
            ->first();

        if (!$address) {
            return back()
                ->withInput()
                ->with('error', 'Alamat pengiriman tidak valid.');
        }

        // Siapkan data untuk OrderService::create().
        $orderItems = $cart->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
            ];
        })->values()->all();

        $paymentMethod = $validated['payment_method'];

        $payload = [
            'address_id' => $address->id,
            'shipping_cost' => (float) $validated['shipping_cost'],
            'payment_method' => $paymentMethod,
            'shipping_service' =>
                $validated['courier'] . ' - ' . $validated['service'],
            'order_items' => $orderItems,

            // Dipakai OrderService untuk memilih bank VA.
            'bank' => $paymentMethod === 'cod'
                ? null
                : ($validated['bank'] ?? $paymentMethod),
        ];

        try {
            /*
             * OrderService bertanggung jawab atas:
             * - pembuatan order dan item,
             * - validasi stok,
             * - pengurangan stok,
             * - pembuatan pembayaran VA bila diperlukan,
             * - penyimpanan data pembayaran.
             */
            $order = DB::transaction(function () use (
                $orderService,
                $payload,
                $user,
                $midtrans
            ) {
                // Mengubah payload menjadi objek request Laravel.
                $orderRequest = Request::create(
                    '/internal/order',
                    'POST',
                    $payload
                );

                return $orderService->create(
                    $orderRequest,
                    $user,
                    $midtrans
                );
            });

            /*
             * Hapus hanya item yang dipilih, setelah OrderService berhasil.
             * Filter user_id tetap dipakai untuk keamanan.
             */
            Cart::where('user_id', $user->id)
                ->whereIn('id', $validated['selected_items'])
                ->delete();

            return redirect()
                ->route('user.payment.show', $order->id)
                ->with('success', 'Checkout berhasil. Silakan lanjutkan pembayaran.');

        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Checkout gagal diproses. Silakan periksa stok dan pembayaran, lalu coba kembali.'
                );
        }
    }
}