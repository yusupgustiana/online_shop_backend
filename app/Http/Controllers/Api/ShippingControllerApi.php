<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; // ⬅️ INI YANG WAJIB
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShippingControllerApi extends Controller
{
    public function cost(Request $request)
    {
        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'weight' => 'required',
            'courier' => 'required',
        ]);

        $response = Http::asForm()->withHeaders([
            'key' => config('services.rajaongkir.key'),
        ])->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
            'origin' => $request->origin,
            'destination' => $request->destination,
            'weight' => $request->weight,
            'courier' => $request->courier,
            'price' => 'lowest',
        ]);

        return response()->json($response->json(), $response->status());
    }
}