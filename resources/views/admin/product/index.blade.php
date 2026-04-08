@extends('layouts.app')

@section('title', 'Products')

@push('style')
<link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Product</h1>
            <div class="section-header-button">
                <a href="{{ route('admin.product.create') }}" class="btn btn-primary">Add New</a>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>All Product</h4>
                </div>

                <div class="card-body">
                    <!-- Search -->
                    <div class="float-right mb-3">
                        <form method="GET" action="{{ route('admin.product.index') }}">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search" name="search">
                                <div class="input-group-append">
                                    <button class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="clearfix"></div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($products as $product)
                                <tr>
                                    <td>
                                        <img
                                            src="{{ asset($product->image_url) }}"
                                            alt="{{ $product->name }}"
                                            width="50"
                                            height="50"
                                            class="rounded"
                                            style="object-fit:cover; cursor:pointer;"
                                            data-toggle="modal"
                                            data-target="#imageModal{{ $product->id }}">
                                    </td>

                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ $product->price }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>{{ $product->created_at?->format('d M Y') }}</td>

                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ route('pages.product.edit', $product->id) }}"
                                               class="btn btn-sm btn-primary mr-1">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('admin.product.destroy', $product->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="float-right">
                        {{ $products->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- MODAL IMAGE (DI LUAR TABLE) --}}
@foreach ($products as $product)
<div class="modal fade" id="imageModal{{ $product->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $product->name }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
            </div>

            <div class="modal-body text-center">
                <img src="{{ asset($product->image) }}"
                     class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
@endpush
