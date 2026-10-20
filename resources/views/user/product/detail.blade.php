
@extends('layouts.app-frontend')

@section('title', 'Detail Produk')

@section('content')

<!-- HEADER -->
<x-header-user />

<div class="max-w-7xl mx-auto px-6 py-10">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 bg-white p-6 rounded-2xl shadow">

        <!-- GAMBAR PRODUK -->
        <div>
            <img
                src="{{ asset($product->image) }}"
                alt="{{ $product->name }}"
                class="w-full h-[400px] object-cover rounded-xl"
            >
        </div>

        <!-- DETAIL PRODUK -->
        <div class="flex flex-col justify-between">

            <div>
                <h1 class="text-3xl font-bold mb-4">
                    {{ $product->name }}
                </h1>

                <p class="text-2xl text-emerald-500 font-semibold mb-4">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>

                <p class="mb-4">
                    <span class="font-semibold">Stok:</span>
                    {{ $product->stock }}
                </p>

                <div class="mb-6">
                    <h3 class="font-semibold mb-2">Deskripsi</h3>
                    <p class="text-gray-600 whitespace-pre-line">
                        {{ $product->description }}
                    </p>
                </div>
            </div>

            <!-- TOMBOL PEMBELIAN -->
            <div class="flex flex-col sm:flex-row gap-4">

                <!-- TAMBAH KE KERANJANG -->
                <form
                    action="{{ route('user.cart.add') }}"
                    method="POST"
                    class="flex-1"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="product_id"
                        value="{{ $product->id }}"
                    >

                    <button
                        type="submit"
                        @disabled($product->stock < 1)
                        class="w-full bg-emerald-500 text-white px-6 py-3 rounded-xl
                        hover:bg-emerald-600 disabled:bg-gray-400
                        disabled:cursor-not-allowed"
                    >
                        + Keranjang
                    </button>
                </form>

                <!-- CHECKOUT -->
                <form
                    action="{{ route('user.cart.add') }}"
                    method="POST"
                    class="flex-1"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="product_id"
                        value="{{ $product->id }}"
                    >

                    <input
                        type="hidden"
                        name="checkout"
                        value="1"
                    >

                    <button
                        type="submit"
                        @disabled($product->stock < 1)
                        class="w-full bg-orange-500 text-white px-6 py-3 rounded-xl
                        hover:bg-orange-600 disabled:bg-gray-400
                        disabled:cursor-not-allowed"
                    >
                        Checkout
                    </button>
                </form>

            </div>

            @if($product->stock < 1)
                <p class="text-red-500 text-sm mt-3">
                    Produk sedang habis.
                </p>
            @endif

        </div>

    </div>

</div>

@endsection
