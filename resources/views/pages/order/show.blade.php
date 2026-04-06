<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-xl mx-auto py-10">

    <h1 class="text-xl font-bold mb-5">Checkout</h1>

    @if(session('error'))
        <p class="text-red-500">{{ session('error') }}</p>
    @endif

    <form method="POST" action="{{ route('orders.store') }}">
        @csrf

        <input type="hidden" name="address_id" value="1">

        <div class="mb-3">
            <label>Ongkir</label>
            <input type="number" name="shipping_cost" class="w-full border p-2" required>
        </div>

        <div class="mb-3">
            <label>Bank</label>
            <select name="bank" class="w-full border p-2">
                <option value="bca">BCA</option>
                <option value="bni">BNI</option>
                <option value="bri">BRI</option>
                <option value="permata">Permata</option>
                <option value="cimb">CIMB</option>
            </select>
        </div>

        <input type="hidden" name="payment_method" value="bank_transfer">
        <input type="hidden" name="shipping_service" value="JNE">

        <!-- Contoh item -->
        <input type="hidden" name="order_items[0][product_id]" value="1">
        <input type="hidden" name="order_items[0][quantity]" value="1">

        <button class="bg-blue-500 text-white px-4 py-2 rounded">
            Checkout
        </button>
    </form>

</div>

</body>
</html>