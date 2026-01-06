@extends('layouts.app')

@section('title', 'Users')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
    
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Users</h1>
                <div class="section-header-button">
                    <a href="{{ route('user.create') }}" class="btn btn-primary">Add New</a>
                </div>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Users</a></div>
                    <a class="nav-link" href="{{ route('user.index') }}">All Users</a>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                         @include('components.alert')
            
                        <h2 class="section-title">Users</h2>
                        <p class="section-lead">
                            You can manage all Users, such as editing, deleting and more.
                        </p>


                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>All Users</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="float-left">
                                            <select class="form-control selectric">
                                                <option>Action For Selected</option>
                                                <option>Move to Draft</option>
                                                <option>Move to Pending</option>
                                                <option>Delete Pemanently</option>
                                            </select>
                                        </div>
                                        <div class="float-right">
                                            <form method="GET" action="{{ route('user.index') }}">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Search"
                                                        name="search">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary"><i
                                                                class="fas fa-search"></i></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="clearfix mb-3"></div>
                                        <div class="table-responsive">
                                            <table class="table-striped table">
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>jabatan</th>
                                                    <th>Action</th>
                                                </tr>
                                                @foreach ($users as $user)
                                                    <tr>
                                                        <td>{{ $user->name }}</td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>{{ $user->phone }}</td>
                                                        <td>{{ $user->roles }}</td>
                                                        <td>
                                                            <div class="d-flex justify-content-center">
                                                                <a href="{{ route('user.edit', $user->id) }}"
                                                                    class="btn btn-sm btn-primary btn-icon">
                                                                    <i class="fas fa-edit"></i>
                                                                    edit
                                                                </a>
                                                                <form action="{{ route('user.destroy', $user->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="btn btn-sm btn-danger btn-icon ml-1"
                                                                        onclick="return confirm('Are you sure?')">
                                                                        <i class="fas fa-trash"></i>
                                                                        delete
                                                                    </button>
                                                                </form>
                                                            </div>

                                                            {{-- <a href="{{ route('user.edit', $user->id) }}"
                                                                class="btn btn-primary btn-action mr-1"
                                                                data-toggle="tooltip"
                                                                title="Edit"><i
                                                                    class="fas fa-pencil-alt"></i></a>

                                                            <a href="{{ route('user.destroy', $user->id) }}"
                                                                class="btn btn-danger btn-action"
                                                                data-toggle="tooltip"
                                                                title="Delete"
                                                                data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?"
                                                                data-confirm-yes="alert('Deleted')"><i
                                                                    class="fas fa-trash"></i></a> --}}

                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                        <div class="float-right">

                                            {{ $users->withQueryString()->links() }}

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/features-Users.js') }}"></script>
@endpush