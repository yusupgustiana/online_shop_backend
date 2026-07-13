<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use App\Services\RajaOngkirService;

class AddressController extends Controller
{
    // 📍 LIST ALAMAT
    public function index()
    {
        $addresses = Address::with(['province', 'city', 'district'])
            ->where('user_id', auth()->id())
            ->get();

        return view('user.address.index', compact('addresses'));
    }

    // ➕ FORM TAMBAH
public function create(RajaOngkirService $rajaOngkir)
{
    $provinces = $rajaOngkir->getProvinces();

    return view('user.address.create', compact('provinces'));
}
// ambil kota
public function getCities($provinceId, RajaOngkirService $rajaOngkir)
{
    return response()->json(
        $rajaOngkir->getCitiesByProvince($provinceId)
    );
}

// ambil kecamatan
public function getDistricts($cityId, RajaOngkirService $rajaOngkir)
{
    return response()->json(
        $rajaOngkir->getDistrictsByCity($cityId)
    );
}

    // 💾 SIMPAN
public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'phone' => 'required',
        'full_address' => 'required',
        'prov_id' => 'required',
        'city_id' => 'required',
        'district_id' => 'required',
        'postal_code' => 'required',
    ]);

    // 🔥 kalau centang default → reset semua
    if ($request->is_default) {
        Address::where('user_id', auth()->id())
            ->update(['is_default' => 0]);
    }

Address::create([
    'user_id' => auth()->id(),
    'name' => $request->name,
    'phone' => $request->phone,
    'full_address' => $request->full_address,

    'prov_id' => $request->prov_id,
    'prov_name' => $request->prov_name,       // 🔥 TAMBAH
    'city_id' => $request->city_id,
    'city_name' => $request->city_name,       // 🔥 TAMBAH
    'district_id' => $request->district_id,
    'district_name' => $request->district_name, // 🔥 TAMBAH

    'postal_code' => $request->postal_code,
    'is_default' => $request->is_default ?? 0,
]);

    return redirect()->route('address.index')
        ->with('success', 'Alamat berhasil ditambahkan');
}

    // ✏️ EDIT
    public function edit($id)
    {
        $address = Address::where('user_id', auth()->id())
            ->findOrFail($id);

        return view('user.address.edit', compact('address'));
    }

    // 🔄 UPDATE
    public function update(Request $request, $id)
    {
        $address = Address::where('user_id', auth()->id())
            ->findOrFail($id);

        $address->update($request->all());

        return redirect()->route('address.index')
            ->with('success', 'Alamat berhasil diupdate');
    }

    // ❌ DELETE
    public function destroy($id)
    {
        Address::where('user_id', auth()->id())
            ->where('id', $id)
            ->delete();

        return back()->with('success', 'Alamat dihapus');
    }

    public function getOngkir(Request $request, RajaOngkirService $rajaOngkir)
{
    $origin = 5779; // 🔥 ganti dengan ID kota toko kamu

    $destination = $request->district_id;

    $couriers = ['jne', 'jnt', 'sicepat'];

    $results = [];

    foreach ($couriers as $courier) {
        $response = $rajaOngkir->getCost(
            $origin,
            $destination,
            $courier,
            1000
        );

        if (isset($response['data'])) {
            foreach ($response['data'] as $service) {
                $results[] = [
                    'courier' => strtoupper($courier),
                    'service' => $service['service'],
                    'cost' => $service['cost'],
                    'etd' => $service['etd'] ?? '-',
                ];
            }
        }
    }

    return response()->json($results);
}
}