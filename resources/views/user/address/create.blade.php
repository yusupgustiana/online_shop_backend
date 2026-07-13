@extends('layouts.app-frontend')

@section('title', 'Tambah Alamat')

@section('content')
  <!-- HEADER -->
    <x-header-user />
<div class="max-w-7xl mx-auto px-6 py-10">


    <!-- CARD -->
    <div class="bg-white rounded-2xl shadow p-6">

        <!-- HEADER -->
        <h2 class="text-2xl font-semibold mb-6 text-gray-800">
            Tambah Alamat
        </h2>

        <form action="{{ route('address.store') }}" method="POST" class="space-y-5">
        @csrf

        <!-- NAMA -->
        <div>
            <label class="text-sm font-medium text-gray-600">Nama Penerima</label>
            <input type="text" name="name"
                class="w-full mt-1 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-400 focus:outline-none"
                placeholder="Masukkan nama"
                required>
        </div>

        <!-- HP -->
        <div>
            <label class="text-sm font-medium text-gray-600">No HP</label>
            <input type="text" name="phone"
                class="w-full mt-1 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-400 focus:outline-none"
                placeholder="08xxxxxxxxxx"
                required>
        </div>

        <!-- ALAMAT -->
        <div>
            <label class="text-sm font-medium text-gray-600">Alamat Lengkap</label>
            <textarea name="full_address"
                class="w-full mt-1 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-400 focus:outline-none"
                rows="3"
                placeholder="Nama jalan, nomor rumah, dll"
                required></textarea>
        </div>

        <!-- GRID 2 KOLOM -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- PROVINSI -->
            <div>
                <label class="text-sm font-medium text-gray-600">Provinsi</label>
                <select id="province"
                        name="prov_id"
                        data-url="{{ url('/rajaongkir/cities') }}"
                        class="w-full mt-1 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-400 focus:outline-none"
                        required>
                    <option value="">Pilih Provinsi</option>
                    @foreach($provinces['data'] as $prov)
                        <option value="{{ $prov['id'] }}">{{ $prov['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- KOTA -->
            <div>
                <label class="text-sm font-medium text-gray-600">Kota</label>
                <select id="city"
                        name="city_id"
                        data-url="{{ url('/rajaongkir/districts') }}"
                        class="w-full mt-1 border rounded-lg px-3 py-2 bg-gray-50 focus:ring-2 focus:ring-emerald-400 focus:outline-none"
                        required
                        disabled>
                    <option value="">Pilih Kota</option>
                </select>
            </div>

        </div>

        <!-- GRID 2 KOLOM -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- KECAMATAN -->
            <div>
                <label class="text-sm font-medium text-gray-600">Kecamatan</label>
                <select id="district"
                        name="district_id"
                        class="w-full mt-1 border rounded-lg px-3 py-2 bg-gray-50 focus:ring-2 focus:ring-emerald-400 focus:outline-none"
                        required
                        disabled>
                    <option value="">Pilih Kecamatan</option>
                </select>
            </div>

            <!-- KODE POS -->
            <div>
                <label class="text-sm font-medium text-gray-600">Kode Pos</label>
                <input type="text" name="postal_code"
                    class="w-full mt-1 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-400 focus:outline-none"
                    placeholder="12345"
                    required>
            </div>

        </div>
        <input type="hidden" name="prov_name" id="prov_name">
<input type="hidden" name="city_name" id="city_name">
<input type="hidden" name="district_name" id="district_name">

        <!-- DEFAULT -->
        <div class="flex items-center justify-between mt-2">
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="is_default" value="1"
                    class="rounded text-emerald-500 focus:ring-emerald-400">
                Jadikan alamat utama
            </label>
        </div>

        <!-- BUTTON -->
        <button class="w-full bg-emerald-500 text-white py-3 rounded-xl font-semibold hover:bg-emerald-600 transition">
            Simpan Alamat
        </button>

        </form>

    </div>

</div>

@endsection