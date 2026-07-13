@extends('layouts.app')

@section('title', 'Banners')

@section('main')

<div class="main-content">
    <section class="section">

        <div class="section-header">
            <h1>Banners</h1>
        </div>

        <div class="section-body">

            <a href="{{ route('admin.banners.create') }}"
               class="btn btn-primary mb-3">
                + Tambah Banner
            </a>

            <div class="row">

                @foreach($banners as $banner)
                    <div class="col-12 col-sm-6 col-md-3">

                        <div class="card">

                            <div class="card-body">

                                <img src="{{ asset('storage/' . $banner->image) }}"
                                     class="img-fluid mb-2"
                                     style="height:150px; object-fit:cover; width:100%; border-radius:8px;">

                                <h6>{{ $banner->title }}</h6>

                                <div class="d-flex gap-2 mt-2">

                                    <a href="{{ route('admin.banners.edit', $banner->id) }}"
                                       class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.banners.destroy', $banner->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin hapus?')">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-danger btn-sm">
                                            Hapus
                                        </button>
                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>
                @endforeach

            </div>

        </div>

    </section>
</div>

@endsection