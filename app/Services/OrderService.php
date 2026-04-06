<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function create($request, $user, $midtransService)
    {
        return DB::transaction(function () use ($request, $user, $midtransService) {

            $subtotal = 0;
            $itemsData = [];

            // VALIDASI + HITUNG TOTAL
            foreach ($request->order_items as $item) {

                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stock {$product->name} tidak cukup");
                }

                $price = $product->price;
                $qty = $item['quantity'];
                $sub = $price * $qty;

                $subtotal += $sub;

                $itemsData[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $sub
                ];
            }

            $totalCost = $subtotal + $request->shipping_cost;

            // CREATE ORDER
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $request->address_id,
                'subtotal' => $subtotal,
                'shipping_cost' => $request->shipping_cost,
                'total_cost' => $totalCost,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'shipping_service' => $request->shipping_service,
                'transaction_number' => Str::uuid(),
            ]);

            // CREATE ITEMS + KURANGI STOCK
            foreach ($itemsData as $item) {

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'], // 🔥 snapshot harga
                ]);

                $item['product']->decrement('stock', $item['quantity']);
            }

            // MIDTRANS VA
            $payment = $midtransService->createVA(
                $order,
                $user,
                $request->bank
            );

            $order->update([
                'payment_va_name' => strtoupper($payment['bank']),
                'payment_va_number' => $payment['va_number']
            ]);

            return $order->load('orderItems.product');
        });
    }
}