@extends('layouts.app-frontend')

@section('title', 'Detail Produk')

@section('content')
    <!-- HEADER -->
    <x-header-user />
<div class="max-w-7xl mx-auto px-6 py-10">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 bg-white p-6 rounded-2xl shadow">

        <!-- IMAGE -->
        <div>
             <img src="{{ asset($product->image) }}"
                 class="w-full h-[400px] object-cover rounded-xl">
        </div>

        <!-- DETAIL -->
        <div class="flex flex-col justify-between">

            <div>
                <!-- NAME -->
                <h1 class="text-3xl font-bold mb-4">
                    {{ $product->name }}
                </h1>

                <!-- PRICE -->
                <p class="text-2xl text-emerald-500 font-semibold mb-4">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>

                <!-- STOCK -->
                <p class="mb-4">
                    <span class="font-semibold">Stock:</span>
                    {{ $product->stock }}
                </p>

                <!-- DESCRIPTION -->
                <div class="mb-6">
                    <h3 class="font-semibold mb-2">Deskripsi</h3>
                    <p class="text-gray-600">
                        {{ $product->description }}
                    </p>
                </div>
            </div>

            <!-- BUTTON -->
       <form action="{{ route('user.cart.add') }}" method="POST" class="flex gap-4">
    @csrf

    <input type="hidden" name="product_id" value="{{ $product->id }}">

    <button class="bg-emerald-500 text-white px-6 py-3 rounded-xl hover:bg-emerald-600">
        + Keranjang
    </button>

    <a href="/" class="border px-6 py-3 rounded-xl hover:bg-gray-100">
        Kembali
    </a>
</form>

        </div>

    </div>

</div>

@endsection