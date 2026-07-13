@extends('layouts.app-frontend')

@section('content')
  <!-- HEADER -->
    <x-header-user />
<div class="max-w-7xl mx-auto px-6 py-10">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Alamat Saya</h1>

        <a href="{{ route('address.create') }}"
           class="bg-emerald-500 text-white px-4 py-2 rounded-lg">
            + Tambah Alamat
        </a>
    </div>

    @foreach($addresses as $address)
    <div class="border rounded-xl p-4 mb-4 shadow-sm">

        <div class="flex justify-between">
            <div>
                <h2 class="font-semibold">
                    {{ $address->name }}

                    @if($address->is_default)
                        <span class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded ml-2">
                            Utama
                        </span>
                    @endif
                </h2>

                <p class="text-sm text-gray-600">{{ $address->phone }}</p>

                <p class="mt-2 text-gray-700">
                    {{ $address->full_address }}
                </p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('address.edit', $address->id) }}"
                   class="text-blue-500">Edit</a>

                <form action="{{ route('address.destroy', $address->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="text-red-500">Hapus</button>
                </form>
            </div>
        </div>

        <div class="mt-3 flex gap-3 text-sm">

            @if(!$address->is_default)
            <form action="{{ route('address.default', $address->id) }}" method="POST">
                @csrf
                <button class="text-green-600">Jadikan Utama</button>
            </form>
            @endif

        </div>

    </div>
    @endforeach

</div>

@endsection