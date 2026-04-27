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
                <div class="relative" data-account-menu>
                    <button
                        type="button"
                        class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full border border-[#E6D5D8] bg-pink-50 text-[#5A3A3A] transition hover:border-[#C94F7C] focus:outline-none focus:ring-2 focus:ring-pink-300"
                        aria-haspopup="true"
                        aria-expanded="false"
                        aria-label="Open account menu"
                        data-account-toggle
                    >
                        <img
                            src="{{ Auth::user()->profile_image_url }}"
                            alt="{{ Auth::user()->name }}"
                            class="h-full w-full object-cover"
                        >
                    </button>

                    <div
                        class="absolute right-0 z-20 mt-3 hidden min-w-[11rem] rounded-xl border border-[#F0E2E5] bg-white p-2 shadow-lg"
                        data-account-dropdown
                    >
                        <a href="/profile" class="block rounded-lg px-4 py-2 text-sm font-medium text-[#5A3A3A] transition hover:bg-pink-50 hover:text-[#C94F7C]">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full rounded-lg px-4 py-2 text-left text-sm font-medium text-[#5A3A3A] transition hover:bg-pink-50 hover:text-[#C94F7C]">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="/login" class="text-[#5A3A3A] hover:text-[#E6B7BE] font-medium">Login</a>
            @endauth
        </div>
    </div>
</header>

@auth
    <script>
        document.querySelectorAll('[data-account-menu]').forEach((menu) => {
            const toggle = menu.querySelector('[data-account-toggle]');
            const dropdown = menu.querySelector('[data-account-dropdown]');

            if (!toggle || !dropdown) {
                return;
            }

            toggle.addEventListener('click', (event) => {
                event.stopPropagation();
                const isOpen = !dropdown.classList.contains('hidden');

                document.querySelectorAll('[data-account-dropdown]').forEach((item) => {
                    item.classList.add('hidden');
                });
                document.querySelectorAll('[data-account-toggle]').forEach((button) => {
                    button.setAttribute('aria-expanded', 'false');
                });

                dropdown.classList.toggle('hidden', isOpen);
                toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            });
        });

        document.addEventListener('click', (event) => {
            document.querySelectorAll('[data-account-menu]').forEach((menu) => {
                if (!menu.contains(event.target)) {
                    const dropdown = menu.querySelector('[data-account-dropdown]');
                    const toggle = menu.querySelector('[data-account-toggle]');

                    dropdown?.classList.add('hidden');
                    toggle?.setAttribute('aria-expanded', 'false');
                }
            });
        });
    </script>
@endauth
