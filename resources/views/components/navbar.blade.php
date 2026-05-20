@auth
    @php
        $navbarNotifications = Auth::user()->activeUserNotifications()->take(8)->get();
        $navbarUnreadNotificationsCount = Auth::user()->unreadUserNotifications()->count();
    @endphp
@endauth

<header id="main-navbar" class="sticky top-0 z-[100] transition-colors duration-300 bg-[#FBF2F6] shadow-[0_4px_20px_rgba(196,122,144,0.08)] border-b border-[#ECD8E0]">
    <div class="container mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="/" id="navbar-brand" class="text-2xl font-bold text-[#4D2E38] transition-colors duration-300">BonBons PH</a>
        </div>

        <nav class="hidden md:flex space-x-6">
            <a href="/" class="nav-link-item text-[#533843] hover:text-[#C47A90] transition-colors duration-300">Home</a>
            <a href="{{ url('/#shop') }}" class="nav-link-item text-[#533843] hover:text-[#C47A90] transition-colors duration-300">Shop</a>
            <a href="{{ route('orders.index') }}" class="nav-link-item text-[#533843] hover:text-[#C47A90] transition-colors duration-300">Orders</a>
            <a href="/customize" class="nav-link-item text-[#533843] hover:text-[#C47A90] transition-colors duration-300">Customize</a>
        </nav>

        <div class="flex items-center space-x-4">
            <a href="/cart" id="navbar-notification-btn" class="relative flex h-10 w-10 items-center justify-center rounded-full border border-[#E9C7D4] bg-[#FFFFFF] text-[#4D2E38] transition-colors duration-300 hover:border-[#C47A90] hover:text-[#C47A90] focus:outline-none focus:ring-2 focus:ring-[#F6DFE9]">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.1 5H19M7 13v8a2 2 0 002 2h10a2 2 0 002-2v-3"></path>
                </svg>
            </a>

            @auth
                <div class="relative" data-notification-menu>
                    <button
                        type="button"
                        class="relative flex h-10 w-10 items-center justify-center rounded-full border border-[#E6D5D8] bg-[#FFFFFF] text-[#4D2E38] transition hover:border-[#C94F7C] hover:text-[#C94F7C] focus:outline-none focus:ring-2 focus:ring-pink-300"
                        aria-haspopup="true"
                        aria-expanded="false"
                        aria-label="Open notifications"
                        data-notification-toggle
                        data-read-url="{{ route('notifications.read-all') }}"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0m6 0H9"></path>
                        </svg>
                        <span
                            class="absolute -right-1 -top-1 {{ $navbarUnreadNotificationsCount > 0 ? 'inline-flex' : 'hidden' }} h-[1.15rem] w-[1.15rem] items-center justify-center rounded-full bg-[#C47A90] text-[10px] font-bold leading-none text-[#FFFFFF]"
                            data-notification-badge
                        >
                            {{ $navbarUnreadNotificationsCount }}
                        </span>
                    </button>

                    <div
                        class="absolute right-0 z-20 mt-3 hidden w-[22rem] overflow-hidden rounded-xl border border-[#ECD8E0] bg-[#FFFFFF] shadow-[0_10px_40px_rgba(196,122,144,0.15)]"
                        data-notification-dropdown
                    >
                        <div class="flex items-center justify-between border-b border-[#ECD8E0] px-4 py-3">
                            <h3 class="text-sm font-semibold text-[#4D2E38]">Notifications</h3>
                            <a href="{{ route('notifications.index') }}" class="text-xs font-medium text-[#4D2E38] hover:text-pink-700">View all</a>
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            @forelse ($navbarNotifications as $notification)
                                <a
                                    href="{{ route('notifications.open', $notification) }}"
                                    class="block border-b border-[#ECD8E0] px-4 py-3 transition hover:bg-[#FBEAF1] {{ $notification->read_at ? 'bg-[#FFFFFF]' : 'bg-[#FBEAF1]' }}"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-semibold text-[#4D2E38]">{{ $notification->title }}</p>
                                                @if (! $notification->read_at)
                                                    <span class="inline-flex h-2 w-2 rounded-full bg-[#C47A90]"></span>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-xs text-[#533843]">{{ $notification->body }}</p>
                                        </div>
                                        <span class="shrink-0 text-[11px] text-[#8A6A76]">{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                </a>
                            @empty
                                <div class="px-4 py-8 text-center text-sm text-[#533843]">
                                    No notifications yet.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="relative" data-account-menu>
                    <button
                        type="button"
                        class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full border border-[#E6D5D8] bg-pink-50 text-[#4D2E38] transition hover:border-[#C94F7C] focus:outline-none focus:ring-2 focus:ring-pink-300"
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
                        class="absolute right-0 z-20 mt-3 hidden min-w-[11rem] rounded-xl border border-[#F0E2E5] bg-[#FFFFFF] p-2 shadow-lg"
                        data-account-dropdown
                    >
                        <a href="/profile" class="block rounded-lg px-4 py-2 text-sm font-medium text-[#4D2E38] transition hover:bg-[#FBEAF1] hover:text-[#C94F7C]">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full rounded-lg px-4 py-2 text-left text-sm font-medium text-[#4D2E38] transition hover:bg-[#FBEAF1] hover:text-[#C94F7C]">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="/login" class="nav-link-item text-[#533843] hover:text-[#C47A90] font-medium transition-colors duration-300">Login</a>
            @endauth
        </div>
    </div>
</header>

@auth
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        document.querySelectorAll('[data-notification-menu]').forEach((menu) => {
            const toggle = menu.querySelector('[data-notification-toggle]');
            const dropdown = menu.querySelector('[data-notification-dropdown]');
            const badge = menu.querySelector('[data-notification-badge]');
            let hasMarkedAsRead = false;

            if (!toggle || !dropdown) {
                return;
            }

            toggle.addEventListener('click', async (event) => {
                event.stopPropagation();
                const isOpen = !dropdown.classList.contains('hidden');

                document.querySelectorAll('[data-notification-dropdown]').forEach((item) => {
                    item.classList.add('hidden');
                });
                document.querySelectorAll('[data-notification-toggle]').forEach((button) => {
                    button.setAttribute('aria-expanded', 'false');
                });
                document.querySelectorAll('[data-account-dropdown]').forEach((item) => {
                    item.classList.add('hidden');
                });
                document.querySelectorAll('[data-account-toggle]').forEach((button) => {
                    button.setAttribute('aria-expanded', 'false');
                });

                dropdown.classList.toggle('hidden', isOpen);
                toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');

                if (!isOpen && badge && !badge.classList.contains('hidden') && !hasMarkedAsRead) {
                    hasMarkedAsRead = true;

                    try {
                        await fetch(toggle.dataset.readUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                        });
                    } catch (error) {
                        console.error('Unable to mark notifications as read.', error);
                    }

                    badge.classList.add('hidden');
                    badge.textContent = '';
                }
            });
        });

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
                document.querySelectorAll('[data-notification-dropdown]').forEach((item) => {
                    item.classList.add('hidden');
                });
                document.querySelectorAll('[data-notification-toggle]').forEach((button) => {
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
