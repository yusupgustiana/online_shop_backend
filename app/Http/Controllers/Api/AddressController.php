<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use App\Http\Resources\AddressResource;
use App\Models\Province;
use App\Models\City;
use App\Models\District;
class AddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = Address::with(['province', 'city', 'district'])
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => AddressResource::collection($addresses)
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'full_address'=> 'required|string',
            'phone'       => 'required|string|max:20',
            'prov_id'     => 'required|integer',
            'city_id'     => 'required|integer',
            'district_id' => 'required|integer',
            'postal_code' => 'required|string|max:20',
            'is_default'  => 'nullable|boolean',
        ]);

        $validated['user_id'] = $request->user()->id;

        $address = Address::create($validated);

        // load relasi 🔥
        $address->load(['province', 'city', 'district']);

        return response()->json([
            'status' => 'success',
            'data' => [new AddressResource($address)]
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $address = Address::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $address->update($request->only([
            'name',
            'full_address',
            'phone',
            'prov_id',
            'city_id',
            'district_id',
            'postal_code',
            'is_default'
        ]));

        // load relasi 🔥
        $address->load(['province', 'city', 'district']);

        return response()->json([
            'status' => 'success',
            'data' => [new AddressResource($address)]
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $address = Address::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $address->delete();

        return response()->json([
            'status' => 'success',
            'data' => []
        ]);
    }
}