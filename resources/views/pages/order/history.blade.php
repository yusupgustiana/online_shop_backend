<!DOCTYPE html>
<html>
<head>
    <title>Order Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-4xl mx-auto py-10">

    <h1 class="text-2xl font-bold mb-6">Pesanan Saya</h1>

    @foreach($orders as $order)
    <div class="bg-white rounded-xl shadow mb-5 p-5">

        <!-- Header -->
        <div class="flex justify-between items-center mb-3">
            <div>
                <p class="text-sm text-gray-500">No Invoice</p>
                <p class="font-semibold">{{ $order->transaction_number }}</p>
            </div>

            <span class="px-3 py-1 text-sm rounded-full
                {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-600' : 'bg-green-100 text-green-600' }}">
                {{ strtoupper($order->status) }}
            </span>
        </div>

        <!-- Items Preview -->
        <div class="border-t pt-3">
            @foreach($order->orderItems->take(2) as $item)
            <div class="flex items-center gap-4 mb-2">
                <img src="{{ asset($item->product->image ?? '') }}"
                     class="w-14 h-14 object-cover rounded">

                <div class="flex-1">
                    <p class="font-medium">{{ $item->product->name }}</p>
                    <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
                </div>

                <p class="font-semibold">
                    Rp {{ number_format($item->product->price ?? 0) }}
                </p>
            </div>
            @endforeach
        </div>

        <!-- Footer -->
        <div class="flex justify-between items-center mt-4 border-t pt-3">
            <div>
                <p class="text-sm text-gray-500">Total Belanja</p>
                <p class="text-lg font-bold text-green-600">
                    Rp {{ number_format($order->total_cost) }}
                </p>
            </div>

            <a href="{{ route('orders.show', $order->id) }}"
               class="bg-blue-500 text-white px-4 py-2 rounded-lg">
               Lihat Detail
            </a>
        </div>

    </div>
    @endforeach

</div>

</body>
</html>