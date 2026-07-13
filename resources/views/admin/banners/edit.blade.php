@extends('layouts.app')

@section('main')

<div class="main-content">
    <section class="section">

        <div class="section-header">
            <h1>Edit Banner</h1>
        </div>

        <div class="section-body">

            <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
                @csrf @method('PUT')

                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" value="{{ $banner->title }}" class="form-control">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control">{{ $banner->description }}</textarea>
                </div>

                <div class="form-group">
                    <label>Image Sekarang</label><br>
                    <img src="{{ asset('storage/' . $banner->image) }}" class="h-32 mb-2 rounded">
                </div>

                <div class="form-group">
                    <label>Ganti Image</label>
                    <input type="file" name="image" class="form-control">
                </div>

                <div class="form-group">
                    <label>Link</label>
                    <input type="text" name="link" value="{{ $banner->link }}" class="form-control">
                </div>

                <div class="form-group">
                    <label>Position</label>
                    <input type="number" name="position" value="{{ $banner->position }}" class="form-control">
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" value="1" {{ $banner->is_active ? 'checked' : '' }}>
                        Aktif
                    </label>
                </div>

                <button class="btn btn-primary">
                    Update
                </button>

            </form>

        </div>
    </section>
</div>

@endsection