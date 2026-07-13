@extends('layouts.app-frontend')

@section('title', 'Home')

@section('content')

<div class="min-h-screen bg-gray-100">
<!-- HEADER -->
<x-header-user />

<!-- ================= BANNER FULL WIDTH ================= -->
@if($banners->count())
<div class="w-full bg-white">

    <div class="swiper mySwiper w-full">

        <div class="swiper-wrapper">
            @foreach($banners as $banner)
                <div class="swiper-slide flex justify-center items-center">

                    <img src="{{ asset('storage/' . $banner->image) }}"
                         class="w-full h-auto max-h-[500px] object-contain">

                </div>
            @endforeach
        </div>

        <div class="swiper-pagination"></div>

    </div>

</div>
@endif


<!-- ================= CONTENT ================= -->
<div class="max-w-7xl mx-auto px-6 py-6">
    <!-- isi category & produk -->
</div>

        <!-- ================= CATEGORY ================= -->
        <div class="mt-8">
            <div class="flex justify-between items-center mb-4">
                <h4 class="font-bold text-lg">Categories</h4>
                <a href="#" class="text-emerald-500 text-sm">See All</a>
            </div>

            <div class="flex gap-6 overflow-x-auto pb-2">
                @foreach($categories as $category)
                    <div class="flex-shrink-0 text-center">
                        <div class="w-16 h-16 rounded-full overflow-hidden">
                            <img src="{{ asset($category->image) }}"
                                 class="w-full h-full object-cover">
                        </div>
                        <p class="text-sm mt-2">{{ $category->name }}</p>
                    </div>
                @endforeach
            </div>
        </div>


        <!-- ================= FEATURED PRODUCT ================= -->
        <div class="mt-8">
            <div class="flex justify-between items-center mb-4">
                <h4 class="font-bold text-lg">Featured Product</h4>
                <a href="#" class="text-emerald-500 text-sm">See All</a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">

                @foreach($products as $product)
                    <a href="{{ route('user.product.show', $product->id) }}">
                        <div class="bg-white p-3 rounded-xl shadow hover:shadow-lg hover:scale-105 transition">

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


{{-- ================= SWIPER CDN ================= --}}
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    new Swiper(".mySwiper", {
        loop: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });
});
</script>
@endpush