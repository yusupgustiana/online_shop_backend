<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carttt;
use Illuminate\Http\Request;

class CarDummyController extends Controller
{
    
public function index(\Illuminate\Http\Request $request)
{
    $validated = $request->validate([
        'id_m_customer' => 'required|string|max:100',
    ]);

    $cart = \App\Models\Carttt::where(
        'id_m_customer',
        $validated['id_m_customer']
    )
        ->orderByDesc('id')
        ->get();

    $firstItem = $cart->first();

    $detail = $cart->map(function ($item) {
        return [
            'id' => (string) $item->id,
            'idSalesInquiry' => (string) (
                $item->getAttribute('id_sales_inquiry') ?? ''
            ),
            'skuRef' => (string) ($item->sku_ref ?? ''),
            'productName' => (string) ($item->product_name ?? ''),
            'brand' => (string) ($item->brand ?? ''),
            'quantity' => (int) ($item->qty ?? 0),
            'unitPriceVat' => (float) ($item->price ?? 0),

            // URL gambar produk untuk Flutter
            'link' => (string) ($item->getAttribute('link') ?? ''),

            'weight' => (float) (
                $item->getAttribute('weight') ?? 0
            ),
            'productCodeRef' => (string) (
                $item->product_code_ref ?? ''
            ),
            'description' => (string) (
                $item->description ?? ''
            ),
            'ready' => (string) (
                $item->getAttribute('ready') ?? ''
            ),
        ];
    })->values();

    $totalQty = $cart->sum(
        fn ($item) => (int) ($item->qty ?? 0)
    );

    $totalAmount = $cart->sum(
        fn ($item) =>
            (float) ($item->price ?? 0)
            * (int) ($item->qty ?? 0)
    );

    return response()->json([
        'status' => 'success',
        'code' => 200,
        'idSalesInquiry' => (string) (
            $firstItem?->getAttribute('id_sales_inquiry') ?? ''
        ),
        'idSalesInquiryAp' => (string) (
            $firstItem?->getAttribute('id_sales_inquiry_ap') ?? ''
        ),
        'totalItem' => $cart->count(),
        'totalQty' => $totalQty,
        'totalAmount' => number_format($totalAmount, 2, '.', ''),
        'detail' => $detail,
    ], 200);
}


public function store(Request $request)
{
    $validated = $request->validate([
        'id_m_customer' => 'required|string|max:100',
        'sku_ref' => 'required|string|max:150',
        'product_code_ref' => 'required|string|max:150',
        'product_code' => 'required|string|max:100',
        'qty' => 'required|integer|min:1',
        'product_name' => 'required|string|max:255',
        'brand' => 'nullable|string|max:100',
        'mf' => 'nullable|string|max:100',
        'car_model' => 'nullable|string|max:255',
        'price' => 'required|numeric|min:0',
        'description' => 'nullable|string',

        // Tambahkan validasi URL gambar
        'link' => 'nullable|string|max:2048',
    ]);

    // Jika produk yang sama sudah ada untuk customer ini,
    // tambahkan quantity dan perbarui URL gambar jika dikirim.
    $cart = Carttt::where(
        'id_m_customer',
        $validated['id_m_customer']
    )
        ->where('sku_ref', $validated['sku_ref'])
        ->where('product_code_ref', $validated['product_code_ref'])
        ->first();

    if ($cart) {
        $cart->qty += $validated['qty'];

        if (!empty($validated['link'])) {
            $cart->link = $validated['link'];
        }

        $cart->save();
    } else {
        $cart = Carttt::create($validated);
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Produk berhasil ditambahkan ke keranjang.',
        'data' => $cart->fresh(),
    ], 201);
}

}
