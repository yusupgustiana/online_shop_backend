@extends('layouts.app-frontend')

@section('title', 'Home')

@section('content')

<div class="container-home">

    <!-- HEADER -->
    <div class="header">
        <h2>Cwb Store</h2>

        <div class="icons">
            <span>🔔</span>
            <span>🛒</span>
        </div>
    </div>

    <!-- SEARCH -->
    <div class="search">
        <input type="text" placeholder="Cari Produk di sini">
    </div>

    <!-- BANNER -->
    <div class="banner">
        <div>
            <h3>New Year<br>Special Sale</h3>
            <p>Discount up to 25%</p>
            <button>SHOP NOW</button>
        </div>
        <img src="https://via.placeholder.com/120" alt="">
    </div>

    <!-- DOT -->
    <div class="dots">
        <span class="active"></span>
        <span></span>
    </div>

    <!-- CATEGORY -->
<div class="section">
    <div class="section-header">
        <h4>Categories</h4>
        <a href="#">See All</a>
    </div>

    <div class="categories">
        @foreach($categories as $category)
            <div class="cat">
             <div class="circle">
<img src="{{ asset('storage/' . $category->image) }}" width="40">
</div>
                <p>{{ $category->name }}</p>
            </div>
        @endforeach
    </div>
</div>

    <!-- PRODUCT -->
    <div class="section">
        <div class="section-header">
            <h4>Featured Product</h4>
            <a href="#">See All</a>
        </div>

        <div class="products">
            <div class="card">
                <img src="https://via.placeholder.com/150">
                <p>Lampu</p>
                <span>IDR 90.000</span>
            </div>

            <div class="card">
                <img src="https://via.placeholder.com/150">
                <p>Earphone</p>
                <span>IDR 320.000</span>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
body {
    background: #f5f7f9;
}

/* FULLSCREEN */
.container-home {
    width: 100%;
    min-height: 100vh;
    padding: 20px;
}

/* HEADER */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.header h2 {
    color: #3bb6a0;
}

.icons {
    display: flex;
    gap: 15px;
}

/* SEARCH */
.search input {
    width: 100%;
    padding: 12px;
    border-radius: 12px;
    border: none;
    background: #eee;
}

/* BANNER */
.banner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f1e7e7;
    border-radius: 16px;
    padding: 20px;
    margin-top: 15px;
}

.banner h3 {
    font-size: 18px;
}

.banner button {
    margin-top: 10px;
    padding: 8px 12px;
    border: none;
    background: black;
    color: white;
    border-radius: 8px;
}

/* DOT */
.dots {
    text-align: center;
    margin: 10px 0;
}

.dots span {
    display: inline-block;
    width: 8px;
    height: 8px;
    background: #ccc;
    border-radius: 50%;
}

.dots .active {
    background: #3bb6a0;
}

/* SECTION */
.section {
    margin-top: 20px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.section-header a {
    color: #3bb6a0;
    font-size: 12px;
}

/* CATEGORY */
.categories {
    display: flex;
    justify-content: space-between;
}

.cat {
    text-align: center;
}

.circle {
    width: 60px;
    height: 60px;
    background: #e8f7f3;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* PRODUCT */
.products {
    display: flex;
    gap: 15px;
}

.card {
    background: white;
    padding: 10px;
    border-radius: 12px;
    width: 48%;
}

.card img {
    width: 100%;
    border-radius: 10px;
}

.card span {
    color: #3bb6a0;
}
</style>
@endpush