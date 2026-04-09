@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center py-3">
        <h4>Y.G Store</h4>
        <div>
            <i class="bi bi-bell"></i>
            <i class="bi bi-cart"></i>
        </div>
    </div>

    <!-- Search -->
    <form class="mb-3">
        <input type="text" class="form-control" placeholder="Cari Produk di sini">
    </form>

    <!-- Banner -->
    <div class="banner bg-light p-4 text-center">
        <h5>New Year Special Sale</h5>
        <p>Discount up to 25% + Extra Cashback 5%</p>
        <button class="btn btn-primary">SHOP NOW</button>
    </div>

    <!-- Categories -->
    <div class="d-flex justify-content-around my-4">
        <div>Bestseller</div>
        <div>Flashsale</div>
        <div>Toprated</div>
        <div>More</div>
    </div>

    <!-- Featured Products -->
    <h5>Featured Product</h5>
    <div class="row">
        <div class="col-6">
            <div class="card">
                <img src="/images/lampu.png" class="card-img-top" alt="Lampu">
                <div class="card-body">
                    <h6>Lampu</h6>
                    <p>IDR 90.000</p>
                    <button class="btn btn-sm btn-outline-primary">Add to Cart</button>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <img src="/images/earphone.png" class="card-img-top" alt="Earphone">
                <div class="card-body">
                    <h6>Earphone</h6>
                    <p>IDR 320.000</p>
                    <button class="btn btn-sm btn-outline-primary">Add to Cart</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection