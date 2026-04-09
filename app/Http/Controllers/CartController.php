<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\Product;
// use App\Models\Cart;

// class CartController extends Controller
// {
//     // 🛒 TAMPILKAN CART
//     public function index()
//     {
//         $cart = Cart::with('product')
//             ->where('user_id', auth()->id())
//             ->get();

//         return view('pages.cart.index', compact('cart'));
//     }

//     // ➕ TAMBAH KE CART
//     public function add($id)
//     {
//         $product = Product::findOrFail($id);

//         $cart = Cart::where('user_id', auth()->id())
//             ->where('product_id', $id)
//             ->first();

//         if ($cart) {
//             $cart->increment('quantity');
//         } else {
//             Cart::create([
//                 'user_id' => auth()->id(),
//                 'product_id' => $id,
//                 'quantity' => 1
//             ]);
//         }

//         return redirect()->back()->with('success', 'Produk ditambahkan ke keranjang');
//     }

//     // ➖ UPDATE QTY
//     public function update(Request $request, $id)
//     {
//         $request->validate([
//             'quantity' => 'required|integer|min:1'
//         ]);

//         $cart = Cart::where('user_id', auth()->id())
//             ->where('product_id', $id)
//             ->firstOrFail();

//         $cart->update([
//             'quantity' => $request->quantity
//         ]);

//         return redirect()->back();
//     }

//     // ❌ HAPUS ITEM
//     public function remove($id)
//     {
//         Cart::where('user_id', auth()->id())
//             ->where('product_id', $id)
//             ->delete();

//         return redirect()->back();
//     }
// }