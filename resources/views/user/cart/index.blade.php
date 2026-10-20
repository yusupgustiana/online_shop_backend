
@extends('layouts.app-frontend')

@section('title', 'Keranjang')

@section('content')

<!-- HEADER -->
<x-header-user />

<div class="max-w-7xl mx-auto px-6 py-10">

    @if($cart->count() > 0)

        @php
            $total = 0;

            foreach ($cart as $item) {
                $total += ($item->product?->price ?? 0) * $item->quantity;
            }
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- DAFTAR PRODUK -->
            <div class="lg:col-span-2 space-y-4">

                @foreach($cart as $item)

                    <div class="flex items-start bg-white p-4 rounded-xl shadow gap-3">

                        <!-- CHECKBOX CHECKOUT -->
                        <div class="pt-8">
                   
<input
    type="checkbox"
    name="cart_ids[]"
    value="{{ $item->id }}"
    form="checkout-form"
    {{ request('selected') == $item->id ? 'checked' : '' }}
    class="w-5 h-5 cursor-pointer"
/>

                        </div>

                        <!-- GAMBAR -->
                        <img
                            src="{{ asset($item->product?->image ?? '') }}"
                            alt="{{ $item->product?->name ?? 'Produk' }}"
                            class="w-24 h-24 object-cover rounded-lg flex-shrink-0"
                        >

                        <!-- INFORMASI PRODUK -->
                        <div class="flex-1 min-w-0">

                            <h3 class="font-semibold text-lg">
                                {{ $item->product?->name ?? 'Produk tidak ditemukan' }}
                            </h3>

                            <p class="text-gray-500">
                                Rp {{ number_format($item->product?->price ?? 0, 0, ',', '.') }}
                            </p>

<!-- JUMLAH PRODUK -->
<div class="flex items-center mt-3 gap-2">

    <!-- MINUS -->
    <form
        action="{{ route('user.cart.update', $item->product_id) }}"
        method="POST"
    >
        @csrf

        <input
            type="hidden"
            name="quantity"
            value="{{ $item->quantity - 1 }}"
        >

        <button
            type="submit"
            @disabled($item->quantity <= 1)
            class="w-8 h-8 bg-gray-200 rounded flex items-center justify-center
            hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
        >
            -
        </button>
    </form>

    <!-- JUMLAH -->
    <span class="px-3 py-1 border rounded min-w-[40px] text-center">
        {{ $item->quantity }}
    </span>

    <!-- PLUS -->
    <form
        action="{{ route('user.cart.update', $item->product_id) }}"
        method="POST"
    >
        @csrf

        <input
            type="hidden"
            name="quantity"
            value="{{ $item->quantity + 1 }}"
        >

        <button
            type="submit"
            class="w-8 h-8 bg-green-500 text-white rounded hover:bg-green-600
            flex items-center justify-center"
        >
            +
        </button>
    </form>

</div>
                        </div>

                        <!-- SUBTOTAL DAN HAPUS -->
                        <div class="text-right flex-shrink-0">

                            <p class="font-bold text-green-600">
                                Rp {{ number_format(($item->product?->price ?? 0) * $item->quantity, 0, ',', '.') }}
                            </p>

                            <!-- FORM HAPUS TERPISAH -->
                            <form
                                action="{{ route('user.cart.remove', $item->product_id) }}"
                                method="POST"
                                class="mt-2"
                                onsubmit="return confirm('Hapus produk dari keranjang?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="text-red-500 text-sm hover:text-red-700"
                                >
                                    Hapus
                                </button>
                            </form>

                        </div>

                    </div>

                @endforeach

            </div>

            <!-- RINGKASAN BELANJA -->
            <div class="bg-white p-6 rounded-xl shadow sticky top-6 h-fit">

                <h3 class="text-lg font-bold mb-4">
                    Ringkasan Belanja
                </h3>

                <div class="flex justify-between mb-2 gap-3">
                    <span>Total semua produk</span>

                    <span class="font-bold text-green-600 text-right">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </span>
                </div>

                <p class="text-gray-500 text-sm mb-3">
                    Centang produk yang ingin kamu checkout.
                </p>

                <!-- FORM CHECKOUT TERPISAH -->
                <form
                    id="checkout-form"
                    action="{{ route('user.checkout.index') }}"
                    method="GET"
                >
                    <button
                        type="submit"
                        class="w-full mt-4 bg-green-500 text-white py-3 rounded-lg hover:bg-green-600 transition"
                    >
                        Checkout
                    </button>
                </form>

            </div>

        </div>

    @else

        <!-- KERANJANG KOSONG -->
        <div class="text-center py-20">
            <p class="text-gray-500 text-lg">
                Keranjang masih kosong 😢
            </p>
        </div>

    @endif

</div>

@endsection
