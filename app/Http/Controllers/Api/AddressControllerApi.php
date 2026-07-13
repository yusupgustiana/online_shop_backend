<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\AddressResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;


class AddressControllerApi extends Controller
{
/**
 * List alamat user
 */

public function index()
{
    try {
        $addresses = Address::where('user_id', auth()->id())
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'List alamat berhasil diambil',
            'data' => AddressResource::collection($addresses),
        ], 200);

    } catch (\Throwable $e) {

        Log::error('Gagal mengambil daftar alamat', [
            'user_id' => auth()->id(),
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengambil daftar alamat.',
            'error' => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}
    /**
     * Tambah alamat
     */
public function store(Request $request)
{
$request->validate([
    'name' => 'required|string|max:255',
    'full_address' => 'required|string',
    'phone' => 'required|string|max:20',
    'postal_code' => 'required|string|max:10',

    'prov_id' => 'required|integer',
    'prov_name' => 'required|string|max:255',

    'city_id' => 'required|integer',
    'city_name' => 'required|string|max:255',

    'district_id' => 'nullable|integer',
    'district_name' => 'nullable|string|max:255',

    'is_default' => 'nullable|boolean',
]);

    DB::beginTransaction();

    try {

        if ($request->is_default) {
            Address::where('user_id', auth()->id())
                ->update([
                    'is_default' => 0,
                ]);
        }

$address = Address::create([
    'user_id'       => auth()->id(),
    'name'          => $request->name,
    'full_address'  => $request->full_address,
    'phone'         => $request->phone,
    'postal_code'   => $request->postal_code,

    'prov_id'       => $request->prov_id,
    'prov_name'     => $request->prov_name,

    'city_id'       => $request->city_id,
    'city_name'     => $request->city_name,

    'district_id'   => $request->district_id,
    'district_name' => $request->district_name,

    'is_default'    => $request->is_default ?? 0,
]);

        DB::commit();

  return response()->json([
    'success' => true,
    'message' => 'Alamat berhasil ditambahkan',
    'data' => new AddressResource($address),
], 201);

    } catch (\Throwable $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Gagal menambahkan alamat',
            'error' => $e->getMessage(),
        ], 500);
    }
}

/**
 * Detail alamat
 */
public function show($id)
{
    $address = Address::where('user_id', auth()->id())
        ->find($id);

    if (!$address) {
        return response()->json([
            'success' => false,
            'message' => 'Alamat tidak ditemukan',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'message' => 'Detail alamat berhasil diambil',
        'data' => new AddressResource($address),
    ]);
}

    /**
     * Update alamat
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'full_address' => 'required|string',
            'phone' => 'required|string|max:20',
            'postal_code' => 'required|string|max:10',
        ]);

        $address = Address::where('user_id', auth()->id())
            ->find($id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak ditemukan',
            ], 404);
        }

        DB::beginTransaction();

        try {

            // reset default lama
            if ($request->is_default) {
                Address::where('user_id', auth()->id())
                    ->update([
                        'is_default' => 0
                    ]);
            }

            $address->update([
                'name' => $request->name,
                'full_address' => $request->full_address,
                'phone' => $request->phone,
                'postal_code' => $request->postal_code,
                'is_default' => $request->is_default ?? 0,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil diupdate',
                'data' => $address,
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal update alamat',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus alamat
     */
    public function destroy($id)
    {
        $address = Address::where('user_id', auth()->id())
            ->find($id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak ditemukan',
            ], 404);
        }

        DB::beginTransaction();

        try {

            $wasDefault = $address->is_default;

            $address->delete();

            // set default baru jika alamat default dihapus
            if ($wasDefault) {

                $nextAddress = Address::where('user_id', auth()->id())
                    ->first();

                if ($nextAddress) {
                    $nextAddress->update([
                        'is_default' => 1
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil dihapus',
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus alamat',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Set alamat default
     */
    public function setDefault($id)
    {
        $address = Address::where('user_id', auth()->id())
            ->find($id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak ditemukan',
            ], 404);
        }

        DB::beginTransaction();

        try {

            Address::where('user_id', auth()->id())
                ->update([
                    'is_default' => 0
                ]);

            $address->update([
                'is_default' => 1
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Alamat default berhasil diubah',
                'data' => $address,
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah alamat default',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}