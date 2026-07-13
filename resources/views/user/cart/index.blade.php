@extends('layouts.app-frontend')

@section('title', 'Keranjang')

@section('content')

<!-- HEADER -->
<x-header-user />

<div class="max-w-7xl mx-auto px-6 py-10">

    @if($cart->count() > 0)

    <!-- FORM CHECKOUT -->
<form action="{{ route('user.checkout.index') }}" method="GET">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- ================= LEFT: LIST PRODUK ================= -->
            <div class="lg:col-span-2 space-y-4">

                @foreach($cart as $item)

                <div class="flex items-start bg-white p-4 rounded-xl shadow justify-between">

                    <!-- CHECKBOX -->
                    <div class="mr-3 pt-8">
                        <input type="checkbox"
                             name="cart_ids[]"
                               value="{{ $item->id }}"
                               checked
                               class="w-5 h-5">
                    </div>

                    <!-- GAMBAR -->
                    <img src="{{ asset($item->product->image) }}"
                         class="w-24 h-24 object-cover rounded-lg flex-shrink-0">

                    <!-- INFO -->
                    <div class="ml-4 flex-1 min-w-0">

                        <h3 class="font-semibold text-lg truncate">
                            {{ $item->product?->name ?? 'Produk tidak ditemukan' }}
                        </h3>

                        <p class="text-gray-500">
                            Rp {{ number_format($item->product?->price ?? 0, 0, ',', '.') }}
                        </p>

                        <!-- QTY -->
                        <div class="flex items-center mt-3 space-x-2">

                            <!-- MINUS -->
                            <a href="{{ route('user.cart.update', [
                                'id' => $item->product_id,
                                'quantity' => $item->quantity - 1
                            ]) }}"
                            class="w-8 h-8 bg-gray-200 rounded flex items-center justify-center
                            {{ $item->quantity <= 1 ? 'opacity-50 pointer-events-none' : 'hover:bg-gray-300' }}">
                                -
                            </a>

                            <!-- QTY -->
                            <span class="px-3 py-1 border rounded min-w-[40px] text-center">
                                {{ $item->quantity }}
                            </span>

                            <!-- qty checkout -->
                        <input type="hidden"
       name="cart_items[{{ $item->id }}][cart_id]"
       value="{{ $item->id }}">

<input type="hidden"
       name="cart_items[{{ $item->id }}][qty_checkout]"
       value="{{ $item->quantity }}">

                            <!-- PLUS -->
                            <a href="{{ route('user.cart.update', [
                                'id' => $item->product_id,
                                'quantity' => $item->quantity + 1
                            ]) }}"
                            class="w-8 h-8 bg-green-500 text-white rounded hover:bg-green-600 flex items-center justify-center">
                                +
                            </a>

                        </div>

                    </div>

                    <!-- KANAN -->
                    <div class="text-right w-32 flex-shrink-0">

                        <p class="font-bold text-lg text-green-600">
                            Rp {{ number_format(($item->product?->price ?? 0) * $item->quantity, 0, ',', '.') }}
                        </p>

                        <!-- HAPUS -->
                        <a href="{{ route('user.cart.remove', $item->product_id) }}"
                           class="text-red-500 text-sm mt-2 inline-block"
                           onclick="return confirm('Hapus produk dari keranjang?')">
                            Hapus
                        </a>

                    </div>

                </div>

                @endforeach

            </div>

            <!-- ================= RIGHT: SUMMARY ================= -->
            <div class="bg-white p-6 rounded-xl shadow sticky top-6 h-fit">

                <h3 class="text-lg font-bold mb-4">
                    Ringkasan Belanja
                </h3>

                @php
                    $total = 0;

                    foreach($cart as $item){
                        $total += ($item->product?->price ?? 0) * $item->quantity;
                    }
                @endphp

                <div class="flex justify-between mb-2">
                    <span>Total</span>

                    <span class="font-bold text-green-600">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </span>
                </div>

                <p class="text-gray-500 text-sm mb-3">
                    Produk dicentang akan ikut checkout.
                </p>

                <button type="submit"
                        class="w-full mt-4 bg-green-500 text-white py-2 rounded-lg hover:bg-green-600">
                    Checkout
                </button>

            </div>

        </div>

    </form>

    @else

    <div class="text-center py-20">
        <p class="text-gray-500 text-lg">
            Keranjang masih kosong 😢
        </p>
    </div>

    @endif

</div>

@endsection