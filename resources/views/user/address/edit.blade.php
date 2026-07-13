@extends('layouts.app-frontend')

@section('content')

  <!-- HEADER -->
    <x-header-user />
<div class="max-w-7xl mx-auto px-6 py-10">

<h1 class="text-xl font-bold mb-4">Edit Alamat</h1>

<form action="{{ route('address.update', $address->id) }}" method="POST">
@csrf @method('PUT')

<input type="text" name="name" value="{{ $address->name }}"
class="w-full border p-2 mb-3 rounded">

<input type="text" name="phone" value="{{ $address->phone }}"
class="w-full border p-2 mb-3 rounded">

<textarea name="full_address"
class="w-full border p-2 mb-3 rounded">{{ $address->full_address }}</textarea>

<input type="text" name="postal_code" value="{{ $address->postal_code }}"
class="w-full border p-2 mb-3 rounded">

<label class="flex items-center gap-2">
<input type="checkbox" name="is_default" value="1"
{{ $address->is_default ? 'checked' : '' }}>
Jadikan utama
</label>

<button class="bg-blue-500 text-white w-full py-2 mt-4 rounded">
Update
</button>

</form>

</div>

@endsection