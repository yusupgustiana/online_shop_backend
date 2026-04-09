@extends('layouts.app-frontend')

@section('title', 'Keranjang')

@section('content')
  <!-- HEADER -->
    <x-header-user />
<div class="max-w-7xl mx-auto px-6 py-10">

  

    @if($cart->count() > 0)

    <!-- GRID UTAMA -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <!-- ================= LEFT: LIST PRODUK ================= -->
        <div class="lg:col-span-2 space-y-4">

            @foreach($cart as $item)

            <div class="flex items-start bg-white p-4 rounded-xl shadow justify-between">

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
                        <form action="{{ route('user.cart.update', $item->product_id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="quantity" value="{{ $item->quantity - 1 }}">
                            <button 
                                class="w-8 h-8 bg-gray-200 rounded flex items-center justify-center 
                                {{ $item->quantity <= 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-300' }}"
                                {{ $item->quantity <= 1 ? 'disabled' : '' }}
                            >
                                -
                            </button>
                        </form>

                        <!-- QTY -->
                        <span class="px-3 py-1 border rounded min-w-[40px] text-center">
                            {{ $item->quantity }}
                        </span>

                        <!-- PLUS -->
                        <form action="{{ route('user.cart.update', $item->product_id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                            <button class="w-8 h-8 bg-green-500 text-white rounded hover:bg-green-600 flex items-center justify-center">
                                +
                            </button>
                        </form>

                    </div>
                </div>

                <!-- KANAN -->
                <div class="text-right w-32 flex-shrink-0">
                    <p class="font-bold text-lg text-green-600">
                        Rp {{ number_format(($item->product?->price ?? 0) * $item->quantity, 0, ',', '.') }}
                    </p>

                    <form action="{{ route('user.cart.remove', $item->product_id) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button class="text-red-500 text-sm mt-2">
                            Hapus
                        </button>
                    </form>
                </div>

            </div>

            @endforeach

        </div>

        <!-- ================= RIGHT: SUMMARY ================= -->
        <div class="bg-white p-6 rounded-xl shadow sticky top-6 h-fit">

            <h3 class="text-lg font-bold mb-4">Ringkasan Belanja</h3>

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

            <button class="w-full mt-4 bg-green-500 text-white py-2 rounded-lg hover:bg-green-600">
                Checkout
            </button>

        </div>

    </div>

    @else
        <div class="text-center py-20">
            <p class="text-gray-500 text-lg">Keranjang masih kosong 😢</p>
        </div>
    @endif

</div>
@endsection