@extends('layouts.app-frontend')

@section('title', 'Checkout')

@section('content')
<x-header-user />

<div class="max-w-7xl mx-auto px-4 md:px-6 py-8">

    <h1 class="text-2xl font-bold mb-6">Checkout</h1>

    {{-- ERROR VALIDATION --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-600 p-4 rounded-xl mb-6">
            <ul class="list-disc ml-5 space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('user.checkout.process') }}"
          method="POST"
          id="checkoutForm">

        @csrf

        {{-- SELECTED ITEMS --}}
        @foreach($cart as $item)
            <input type="hidden"
                   name="selected_items[]"
                   value="{{ $item->id }}">
        @endforeach

        <div class="grid lg:grid-cols-3 gap-6">

            <!-- ================= LEFT ================= -->
            <div class="lg:col-span-2 space-y-6">

                <!-- ALAMAT -->
                <div class="bg-white rounded-2xl shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">
                        Alamat Pengiriman
                    </h2>

                    <select name="address_id"
                            id="addressSelect"
                            class="w-full border rounded-lg px-3 py-2"
                            required>

                        <option value="">Pilih alamat</option>

                        @foreach($addresses as $address)
                            <option value="{{ $address->id }}"
                                    data-district="{{ $address->district_id }}">

                                {{ $address->name }} - {{ $address->city_name }}

                            </option>
                        @endforeach

                    </select>
                </div>

                <!-- ONGKIR -->
                <div class="bg-white rounded-2xl shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">
                        Pengiriman
                    </h2>

                    <div id="ongkirList"
                         class="space-y-3 max-h-72 overflow-y-auto text-sm">

                        Pilih alamat terlebih dahulu

                    </div>

                    <p id="ongkirError"
                       class="text-red-500 text-sm mt-2 hidden">

                        Silakan pilih metode pengiriman

                    </p>
                </div>

                <!-- HIDDEN ONGKIR -->
                <input type="hidden"
                       name="shipping_cost"
                       id="shipping_cost">

                <input type="hidden"
                       name="courier"
                       id="courier">

                <input type="hidden"
                       name="service"
                       id="service">

                <!-- PAYMENT -->
                <div class="bg-white rounded-2xl shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">
                        Metode Pembayaran
                    </h2>

                    <select name="payment_method"
                            class="w-full border rounded-lg px-3 py-2">

                        <option value="cod">COD</option>

                        <optgroup label="Virtual Account">
                            <option value="bca">BCA</option>
                            <option value="bri">BRI</option>
                            <option value="bni">BNI</option>
                            <option value="mandiri">Mandiri</option>
                        </optgroup>

                    </select>
                </div>

                <!-- BUTTON -->
                <button type="submit"
                        class="w-full bg-emerald-500 text-white py-3 rounded-xl font-semibold hover:bg-emerald-600">

                    Bayar Sekarang

                </button>

            </div>

            <!-- ================= RIGHT ================= -->
            <div class="bg-white rounded-2xl shadow p-6 h-fit">

                <h2 class="text-lg font-semibold mb-4">
                    Ringkasan Pesanan
                </h2>

                @php $total = 0; @endphp

                @foreach($cart as $item)

                    @php
                        $subtotal = $item->product->price * $item->quantity;
                        $total += $subtotal;
                    @endphp

                    <div class="flex justify-between mb-2 text-sm">

                        <span>
                            {{ $item->product->name }}
                            ({{ $item->quantity }})
                        </span>

                        <span>
                            Rp {{ number_format($subtotal) }}
                        </span>

                    </div>

                @endforeach

                <!-- ONGKIR -->
                <div class="flex justify-between text-sm mt-3">

                    <span>Ongkir</span>

                    <span id="ongkirText">
                        Rp 0
                    </span>

                </div>

                <!-- TOTAL -->
                <div class="border-t mt-4 pt-4 flex justify-between font-semibold">

                    <span>Total</span>

                    <span id="grandTotal">
                        Rp {{ number_format($total) }}
                    </span>

                </div>

                <input type="hidden"
                       id="baseTotal"
                       value="{{ $total }}">

            </div>

        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>

const checkoutForm = document.getElementById('checkoutForm');

checkoutForm.addEventListener('submit', function(e) {

    const shippingCost = document.getElementById('shipping_cost').value;
    const courier = document.getElementById('courier').value;
    const service = document.getElementById('service').value;

    if (!shippingCost || !courier || !service) {

        e.preventDefault();

        document.getElementById('ongkirError')
            .classList.remove('hidden');

        return;
    }

});

</script>
@endpush