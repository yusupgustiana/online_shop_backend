<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;

class CartUserController extends Controller
{
    // 🛒 TAMPILKAN CART
    public function index()
    {
        $cart = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get();

        return view('user.cart.index', compact('cart'));
    }

    // ➕ TAMBAH KE CART
 
// public function add(Request $request)
// {
//     $id = $request->product_id;

//     $product = Product::findOrFail($id);

//     $cart = Cart::where('user_id', auth()->id())
//         ->where('product_id', $id)
//         ->first();

//     if ($cart) {
//         $cart->increment('quantity');
//     } else {
//         Cart::create([
//             'user_id' => auth()->id(),
//             'product_id' => $id,
//             'quantity' => 1
//         ]);
//     }

//     return redirect()->back()->with('success', 'Produk ditambahkan ke keranjang');
// }

public function add(Request $request)
{
    $request->validate([
        'product_id' => 'required|integer|exists:products,id',
    ]);

    $id = $request->product_id;

    $product = Product::findOrFail($id);

    // Pastikan produk masih tersedia
    if ($product->stock < 1) {
        return redirect()->back()
            ->with('error', 'Stok produk habis.');
    }

    // Cari produk di keranjang milik pengguna
    $cart = Cart::where('user_id', auth()->id())
        ->where('product_id', $id)
        ->first();

    if ($cart) {
        // Cek stok sebelum menambah jumlah
        if ($cart->quantity >= $product->stock) {
            return redirect()->back()
                ->with('error', 'Jumlah produk sudah mencapai stok tersedia.');
        }

        $cart->increment('quantity');
    } else {
        // Tambahkan produk baru ke keranjang
        $cart = Cart::create([
            'user_id' => auth()->id(),
            'product_id' => $id,
            'quantity' => 1,
        ]);
    }

    // Jika tombol Checkout yang ditekan
    if ($request->boolean('checkout')) {
        return redirect()->route('user.cart.index', [
            'selected' => $cart->id,
        ]);
    }

    // Jika tombol + Keranjang yang ditekan
    return redirect()->back()
        ->with('success', 'Produk ditambahkan ke keranjang.');
}

    // ➖ UPDATE QTY
public function update(Request $request, $id)
{
    $quantity = (int) $request->quantity;

    if ($quantity < 1) {
        $quantity = 1;
    }

    $cart = Cart::where('user_id', auth()->id())
        ->where('product_id', $id)
        ->firstOrFail();

    $cart->update([
        'quantity' => $quantity
    ]);

    return redirect()->back();
}

    // ❌ HAPUS ITEM
    public function remove($id)
    {
        Cart::where('user_id', auth()->id())
            ->where('product_id', $id)
            ->delete();

        return redirect()->back();
    }
}

