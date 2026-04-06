<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;
use App\Services\MidtransService;
use App\Models\Order;
use App\Models\Cart;

class OrderController extends Controller
{
    protected $orderService;
    protected $midtransService;

    public function __construct(OrderService $orderService, MidtransService $midtransService)
    {
        $this->orderService = $orderService;
        $this->midtransService = $midtransService;
    }

    // ✅ HALAMAN CHECKOUT (AMBIL DATA CART)
    public function create()
    {
        $cart = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get();

        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang kosong');
        }

        return view('pages.order.checkout', compact('cart'));
    }

    // ✅ SIMPAN ORDER (DARI CART 🔥)
    public function store(StoreOrderRequest $request)
    {
        try {

            // 🔥 ambil cart dari database
            $cart = Cart::where('user_id', auth()->id())->get();

            if ($cart->isEmpty()) {
                return redirect()->back()->with('error', 'Keranjang kosong');
            }

            // 🔥 ubah cart → order_items
            $request->merge([
                'order_items' => $cart->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity
                    ];
                })->toArray()
            ]);

            // 🔥 create order via service
            $order = $this->orderService->create(
                $request,
                auth()->user(),
                $this->midtransService
            );

            // 🔥 kosongkan cart setelah sukses
            Cart::where('user_id', auth()->id())->delete();

            return redirect()
                ->route('orders.show', $order->id)
                ->with('success', 'Order berhasil dibuat');

        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    // ✅ HISTORY
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

        return view('pages.order.history', compact('orders'));
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

        return view('pages.order.show', compact('order'));
    }
}