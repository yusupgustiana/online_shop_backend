@extends('layouts.app')

@section('main')

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Tambah Banner</h1>
        </div>

        <div class="section-body">

            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
                @csrf

                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label>Image</label>
                    <input type="file" name="image" class="form-control">
                </div>

                <div class="form-group">
                    <label>Link</label>
                    <input type="text" name="link" class="form-control">
                </div>

                <div class="form-group">
                    <label>Position</label>
                    <input type="number" name="position" class="form-control">
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" value="1"> Aktif
                    </label>
                </div>

                <button class="btn btn-primary">
                    Simpan
                </button>

            </form>

        </div>
    </section>
</div>

@endsection