<header class="bg-[#FFFFFF] shadow-md border-b border-[#F5F5F5]">
    <div class="container mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="/" class="text-2xl font-bold text-pink-600">BonBons PH</a>
        </div>

        <nav class="hidden md:flex space-x-6">
            <a href="/" class="text-[#5A3A3A] hover:text-[#E6B7BE]">Home</a>
            <a href="{{ url('/products') }}" class="text-[#5A3A3A] hover:text-[#E6B7BE]">Products</a>
            <a href="{{ route('orders.index') }}" class="text-[#5A3A3A] hover:text-[#E6B7BE]">Orders</a>
            <a href="/customize" class="text-[#5A3A3A] hover:text-[#E6B7BE]">Customize</a>
        </nav>

        <div class="flex items-center space-x-4">
            <a href="/cart" class="relative">
                <svg class="w-6 h-6 text-[#5A3A3A] hover:text-[#C94F7C] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.1 5H19M7 13v8a2 2 0 002 2h10a2 2 0 002-2v-3"></path>
                </svg>
            </a>

            @auth
                <div class="flex items-center space-x-4">
                    <a href="/profile" class="text-[#5A3A3A] font-medium hover:text-[#C94F7C] transition">{{ Auth::user()->name }}</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-[#5A3A3A] hover:text-[#C94F7C] font-medium transition">Logout</button>
                    </form>
                </div>
            @else
                <a href="/login" class="text-[#5A3A3A] hover:text-[#E6B7BE] font-medium">Login</a>
            @endauth
        </div>
    </div>
</header>
