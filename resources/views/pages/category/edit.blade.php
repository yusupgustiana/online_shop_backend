@extends('layouts.app')

@section('title', 'Edit Category')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Category</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('category.index') }}tyuyyy">Category</a></div>
                <div class="breadcrumb-item">Edit</div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <form action="{{ route('category.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-header">
                        <h4>Category Form</h4>
                    </div>

                    <div class="card-body">

                        {{-- IMAGE --}}
                        <div class="form-group">
                            <label>Category Image</label>
                            <input type="file"
                                   class="form-control @error('image') is-invalid @enderror"
                                   name="image">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if ($category->image)
                                <div class="mt-2">
                                    <img src="{{ asset($category->image) }}"
                                         width="100"
                                         class="rounded"
                                         style="object-fit: cover">
                                </div>
                            @endif
                        </div>

                        {{-- NAME --}}
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   name="name"
                                   value="{{ old('name', $category->name) }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- DESCRIPTION --}}
                        <div class="form-group">
                            <label>Description</label>
                            <textarea
                                class="form-control @error('description') is-invalid @enderror"
                                name="description"
                                rows="4">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="card-footer text-right">
                        <button class="btn btn-primary">Update</button>
                        <a href="{{ route('category.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>

                </form>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
@endpush
