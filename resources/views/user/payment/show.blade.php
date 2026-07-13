@extends('layouts.app-frontend')

@section('title', 'Pembayaran')

@section('content')
<x-header-user />

<div class="max-w-3xl mx-auto px-4 py-8 space-y-6">

    <!-- ================= STATUS + TIMER ================= -->
    <div class="bg-yellow-50 border border-yellow-200 p-5 rounded-2xl text-center">
        <h2 class="font-semibold text-lg text-yellow-700">
            Menunggu Pembayaran
        </h2>

        <p class="text-sm text-gray-600 mt-1">
            Order ID: <strong>{{ $order->transaction_number }}</strong>
        </p>

        @if($order->payment_method !== 'cod')
        <div class="mt-4">
            <p class="text-sm text-gray-500">Batas Waktu Pembayaran</p>
            <p id="countdown" class="text-2xl font-bold text-red-500 mt-1">
                24:00:00
            </p>
        </div>
        @endif
    </div>

    <!-- ================= TOTAL ================= -->
    <div class="bg-white p-6 rounded-2xl shadow">
        <h3 class="font-semibold mb-3">Total Pembayaran</h3>

        <p class="text-3xl font-bold text-emerald-600">
            Rp {{ number_format($order->total_cost) }}
        </p>

        <div class="text-sm text-gray-500 mt-3">
            Subtotal: Rp {{ number_format($order->subtotal) }} <br>
            Ongkir: Rp {{ number_format($order->shipping_cost) }}
        </div>
    </div>

    <!-- ================= VA / COD ================= -->
    <div class="bg-white p-6 rounded-2xl shadow">

        <h3 class="font-semibold mb-4">Metode Pembayaran</h3>

        @if($order->payment_method !== 'cod')

            <div class="flex justify-between mb-3">
                <span class="text-gray-500">Bank</span>
                <span class="font-semibold uppercase">
                    {{ $order->payment_va_name }}
                </span>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-gray-500">Virtual Account</span>

                <div class="flex items-center gap-2">
                    <span id="vaNumber"
                          class="font-bold text-lg tracking-widest">
                        {{ $order->payment_va_number }}
                    </span>

                    <button onclick="copyVA()"
                        class="text-xs bg-gray-100 px-3 py-1 rounded hover:bg-gray-200">
                        Copy
                    </button>
                </div>  
            </div>

        @else

            <div class="bg-yellow-50 p-4 rounded-lg text-sm text-gray-600">
                Pembayaran dilakukan saat barang diterima (COD)
            </div>

        @endif
    </div>

    <!-- ================= INSTRUKSI ================= -->
    @if($order->payment_method !== 'cod')
    <div class="bg-white p-6 rounded-2xl shadow">
        <h3 class="font-semibold mb-4">Cara Pembayaran</h3>

        <ol class="list-decimal ml-5 text-sm text-gray-600 space-y-2">
            <li>Buka aplikasi m-banking / ATM</li>
            <li>Pilih menu Transfer / Virtual Account</li>
            <li>Masukkan nomor VA</li>
            <li>Masukkan jumlah pembayaran</li>
            <li>Konfirmasi pembayaran</li>
        </ol>
    </div>
    @endif

    <!-- ================= ACTION ================= -->
    <a href="{{ route('user.dashboard') }}"
       class="block text-center bg-gray-100 hover:bg-gray-200 py-3 rounded-xl">
        Kembali ke Dashboard
    </a>

</div>
@endsection

@push('scripts')
<script>

<script>

function copyVA() {

    let el = document.getElementById("vaNumber");

    if (!el) return;

    navigator.clipboard.writeText(el.innerText);

    alert("Nomor VA berhasil disalin!");
}

const expiredAt = new Date("{{ $order->expired_at }}").getTime();

setInterval(() => {

    const now = new Date().getTime();

    let distance = expiredAt - now;

    if (distance < 0) {

        document.getElementById("countdown").innerText = "00:00:00";

        return;
    }

    let hours = Math.floor(distance / (1000 * 60 * 60));
    let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    let seconds = Math.floor((distance % (1000 * 60)) / 1000);

    let el = document.getElementById("countdown");

    if (el) {

        el.innerText =
            `${hours.toString().padStart(2,'0')}:` +
            `${minutes.toString().padStart(2,'0')}:` +
            `${seconds.toString().padStart(2,'0')}`;
    }

}, 1000);

</script>
@endpush