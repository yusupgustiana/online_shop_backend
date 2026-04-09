<div class="flex justify-between items-center px-6 py-4 bg-white shadow">

    <h2 class="text-xl font-bold text-emerald-500">Y.G Store</h2>

    <!-- SEARCH -->
    <div class="w-1/3">
        <input type="text"
               placeholder="Cari Produk di sini"
               class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400">
    </div>

    <!-- ICON -->
    <div class="flex items-center gap-6 text-xl">

        <span>🔔</span>

        <!-- CART -->
        <a href="{{ route('cart.index') }}" class="relative">
            🛒
            @if($cartCount > 0)
                <span class="absolute -top-2 -right-3 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">
                    {{ $cartCount }}
                </span>
            @endif
        </a>

        <!-- PROFILE -->
     <div x-data="{ open: false }" 
     class="relative"
     @mouseenter="open = true"
     @mouseleave="open = false">

    <!-- ICON -->
    <button class="cursor-pointer">👤</button>

    <!-- DROPDOWN -->
    <div x-show="open"
         x-transition
         class="absolute right-0 mt-2 w-40 bg-white border rounded-lg shadow z-50">

        <a href="#" class="block px-4 py-2 hover:bg-gray-100">
            Profile
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full text-left px-4 py-2 hover:bg-gray-100">
                Logout
            </button>
        </form>

    </div>
</div>

    </div>
</div>