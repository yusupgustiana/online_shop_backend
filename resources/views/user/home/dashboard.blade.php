@extends('layouts.app-frontend')

@section('title', 'Home')

@section('content')

<div class="min-h-screen bg-gray-100">

    <!-- HEADER -->
    <x-header-user />

    <div class="max-w-7xl mx-auto px-6 py-6">

        <!-- BANNER -->
        <div class="flex justify-between items-center bg-pink-100 rounded-2xl p-6 mt-4">
            <div>
                <h3 class="text-lg font-bold leading-tight">New Year<br>Special Sale</h3>
                <p class="text-sm mt-1">Discount up to 25%</p>
                <button class="mt-3 bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                    SHOP NOW
                </button>
            </div>
            <img src="https://via.placeholder.com/120" class="rounded-lg">
        </div>

        <!-- DOTS -->
        <div class="flex justify-center space-x-2 mt-3">
            <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
            <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
        </div>

            <!-- CATEGORY -->
            <div class="mt-8">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-bold text-lg">Categories</h4>
                    <a href="#" class="text-emerald-500 text-sm">See All</a>
                </div>

                <div class="flex gap-6 overflow-x-auto pb-2">
                    @foreach($categories as $category)
                        <div class="flex-shrink-0 text-center">
           <div class="w-16 h-16 rounded-full overflow-hidden flex items-center justify-center">
             <img src="{{ asset($category->image) }}" class="w-full h-full object-cover">
                </div>
                            <p class="text-sm mt-2">{{ $category->name }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

  <!-- FEATURED PRODUCT -->
<div class="mt-8">
    <div class="flex justify-between items-center mb-4">
        <h4 class="font-bold text-lg">Featured Product</h4>
        <a href="#" class="text-emerald-500 text-sm">See All</a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        @foreach($products as $product)
            <a href="{{ route('user.product.show', $product->id) }}">
                <div class="bg-white p-3 rounded-xl shadow hover:shadow-lg hover:scale-105 transition cursor-pointer">

                    <img src="{{ asset($product->image) }}"
                         class="w-full h-40 object-cover rounded-lg">

                    <p class="mt-2 font-semibold text-sm">
                        {{ $product->name }}
                    </p>

                    <span class="text-emerald-500 font-bold">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </span>

                </div>
            </a>
        @endforeach
    </div>
</div>

    </div>
</div>

@endsection