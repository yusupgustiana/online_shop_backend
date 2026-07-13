<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Address;
use App\Models\Order;
use App\Services\MidtransService;

class CheckoutControllerUser extends Controller
{
    // Halaman checkout: hanya item yang dipilih
    public function index(Request $request)
    {
        $selectedCartIds = $request->cart_ids ?? [];

        $cart = Cart::with('product')
            ->where('user_id', auth()->id())
            ->whereIn('id', $selectedCartIds)
            ->get();

        if ($cart->isEmpty()) {
            return redirect()->route('user.cart.index')
                ->with('error', 'Pilih produk terlebih dahulu');
        }

        $total = $cart->sum(fn($item) => $item->product->price * $item->quantity);

        $addresses = Address::with(['province', 'city', 'district'])
            ->where('user_id', auth()->id())
            ->get();

        return view('user.checkout.index', compact(
            'cart',
            'total',
            'addresses',
            'selectedCartIds'
        ));
    }

    // Proses checkout
    public function process(Request $request, MidtransService $midtrans)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required',
            'selected_items' => 'required|array'
        ]);

        // Ambil hanya cart yang diceklis
        $cart = Cart::with('product')
            ->where('user_id', auth()->id())
            ->whereIn('id', $request->selected_items)
            ->get();

        if ($cart->isEmpty()) {
            return back()->with('error', 'Tidak ada item dipilih');
        }

        // Hitung subtotal dari item terpilih
        $subtotal = $cart->sum(fn($item) => $item->product->price * $item->quantity);
        $shipping = $request->shipping_cost ?? 0;
        $total = $subtotal + $shipping;

        // Buat order
        $order = Order::create([
            'user_id' => auth()->id(),
            'address_id' => $request->address_id,
            'transaction_number' => 'TRX-' . time(),
            'subtotal' => $subtotal,
            'shipping_cost' => $shipping,
            'total_cost' => $total,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'shipping_service' => $request->courier . ' - ' . $request->service,
        ]);

        // Simpan item order
        foreach ($cart as $item) {
            $order->orderItems()->create([
                'product_id' => $item->product_id,
                'price' => $item->product->price,
                'quantity' => $item->quantity,
                'subtotal' => $item->product->price * $item->quantity,
            ]);
        }

        // Hapus hanya item yang diceklis
        Cart::whereIn('id', $request->selected_items)->delete();

        // Redirect ke halaman pembayaran
        return redirect()->route('user.payment.show', $order->id)
            ->with('success', 'Checkout berhasil, silakan lakukan pembayaran');
    }
}
