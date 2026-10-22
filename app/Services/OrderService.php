<?php

// namespace App\Services;

// use App\Models\Order;
// use App\Models\OrderItem;
// use App\Models\Product;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Str;

// class OrderService
// {
//     public function create($request, $user, $midtransService)
//     {
//         return DB::transaction(function () use ($request, $user, $midtransService) {

//             $subtotal = 0;
//             $itemsData = [];

//             // VALIDASI + HITUNG TOTAL
//             foreach ($request->order_items as $item) {

//                 $product = Product::findOrFail($item['product_id']);

//                 if ($product->stock < $item['quantity']) {
//                     throw new \Exception("Stock {$product->name} tidak cukup");
//                 }

//                 $price = $product->price;
//                 $qty = $item['quantity'];
//                 $sub = $price * $qty;

//                 $subtotal += $sub;

//                 $itemsData[] = [
//                     'product' => $product,
//                     'quantity' => $qty,
//                     'price' => $price,
//                     'subtotal' => $sub
//                 ];
//             }

//             $totalCost = $subtotal + $request->shipping_cost;

//             // CREATE ORDER
//             $order = Order::create([
//                 'user_id' => $user->id,
//                 'address_id' => $request->address_id,
//                 'subtotal' => $subtotal,
//                 'shipping_cost' => $request->shipping_cost,
//                 'total_cost' => $totalCost,
//                 'status' => 'pending',
//                 'payment_method' => $request->payment_method,
//                 'shipping_service' => $request->shipping_service,
//                 'transaction_number' => Str::uuid(),
//             ]);

//             // CREATE ITEMS + KURANGI STOCK
//             foreach ($itemsData as $item) {

//                 OrderItem::create([
//                     'order_id' => $order->id,
//                     'product_id' => $item['product']->id,
//                     'quantity' => $item['quantity'],
//                     'price' => $item['price'], // 🔥 snapshot harga
//                 ]);

//                 $item['product']->decrement('stock', $item['quantity']);
//             }

//             // MIDTRANS VA
//             $payment = $midtransService->createVA(
//                 $order,
//                 $user,
//                 $request->bank
//             );

//             $order->update([
//                 'payment_va_name' => strtoupper($payment['bank']),
//                 'payment_va_number' => $payment['va_number']
//             ]);

//             return $order->load('orderItems.product');
//         });
//     }
// }


namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class OrderService
{
    public function create(
        Request $request,
        $user,
        MidtransService $midtransService
    ) {
        return DB::transaction(function () use (
            $request,
            $user,
            $midtransService
        ) {
            $subtotal = 0;
            $itemsData = [];

            // Validasi produk dan stok, lalu hitung subtotal.
            foreach ($request->input('order_items', []) as $item) {
                $product = Product::whereKey($item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $qty = (int) $item['quantity'];

                if ($qty < 1) {
                    throw new RuntimeException(
                        'Jumlah produk harus lebih dari nol.'
                    );
                }

                if (!$product->is_available || $product->stock < $qty) {
                    throw new RuntimeException(
                        "Stok produk {$product->name} tidak mencukupi."
                    );
                }

                $price = $product->price;
                $lineSubtotal = $price * $qty;

                $subtotal += $lineSubtotal;

                $itemsData[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $lineSubtotal,
                ];
            }

            if (empty($itemsData)) {
                throw new RuntimeException(
                    'Tidak ada produk yang akan dipesan.'
                );
            }

            $shippingCost = (float) $request->input('shipping_cost', 0);

            if ($shippingCost < 0) {
                throw new RuntimeException(
                    'Ongkos kirim tidak boleh negatif.'
                );
            }

            $totalCost = $subtotal + $shippingCost;
            $paymentMethod = $request->input('payment_method');

            // Buat order.
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $request->input('address_id'),
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total_cost' => $totalCost,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'shipping_service' => $request->input('shipping_service'),
                'transaction_number' => (string) Str::uuid(),
                'expired_at' => null,
            ]);

            // Buat item order dan kurangi stok.
            foreach ($itemsData as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);

                $item['product']->decrement(
                    'stock',
                    $item['quantity']
                );
            }

            // COD tidak membutuhkan Virtual Account.
            if ($paymentMethod !== 'cod') {
                $bank = $request->input('bank');

                if (empty($bank)) {
                    throw new RuntimeException(
                        'Bank pembayaran belum dipilih.'
                    );
                }

                $payment = $midtransService->createVA(
                    $order,
                    $user,
                    $bank
                );

                if (!is_array($payment)) {
                    throw new RuntimeException(
                        'Respons pembayaran Midtrans tidak valid.'
                    );
                }

                /*
                 * Mendukung format hasil yang sudah dinormalisasi:
                 * ['bank' => ..., 'va_number' => ..., 'expired_at' => ...]
                 *
                 * Juga mendukung format umum Core API:
                 * ['va_numbers' => [['bank' => ..., 'va_number' => ...]],
                 *  'expiry_time' => ...]
                 */
                $vaData = $payment['va_numbers'][0] ?? [];

                $bankName = $payment['bank']
                    ?? $vaData['bank']
                    ?? null;

                $vaNumber = $payment['va_number']
                    ?? $vaData['va_number']
                    ?? null;

                $expiryValue = $payment['expired_at']
                    ?? $payment['expiry_time']
                    ?? null;

                if (empty($bankName) || empty($vaNumber)) {
                    throw new RuntimeException(
                        'Nomor VA tidak ditemukan pada respons Midtrans. '
                        . 'Periksa implementasi createVA().'
                    );
                }

                $expiredAt = null;

                if (!empty($expiryValue)) {
                    $expiredAt = Carbon::parse($expiryValue);
                }

                $order->update([
                    'payment_va_name' => strtoupper($bankName),
                    'payment_va_number' => $vaNumber,
                    'expired_at' => $expiredAt,
                ]);
            }

            return $order->load('orderItems.product');
        });
    }
}