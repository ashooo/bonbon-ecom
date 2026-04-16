<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bonbon Ecom') }} | Admin</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --pink-light: #F5E6E8;
            --pink-medium: #E6B7BE;
            --pink-dark: #C88A92;
            --cream: #FFFFFF;
            --white-soft: #FFFFFF;
            --text-dark: #2E2E2E;
            --border-soft: #F5F5F5;
            --brand-brown: #5A3A3A;
            --brand-soft-brown: #7A5252;
        }

        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';
            background-color: #F3F4F6;
            color: var(--text-dark);
        }

        .text-pink-600 { color: var(--pink-medium) !important; }
        .bg-pink-600 { background-color: var(--pink-medium) !important; }
        .hover\:bg-pink-700:hover { background-color: var(--pink-dark) !important; }
        .bg-pink-100 { background-color: var(--pink-light) !important; }
        .border-pink-600 { border-color: var(--pink-medium) !important; }
        .shadow-soft { box-shadow: 0 20px 35px rgba(0,0,0,0.08); }
    </style>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen">
    <div class="min-h-screen grid grid-cols-[280px_minmax(0,1fr)]">
        <!-- Sidebar -->
        <aside class="bg-[#1F2937] text-gray-100 flex flex-col">
            <div class="px-6 py-8 border-b border-white/10">
                <a href="{{ route('admin.dashboard') }}" class="text-2xl font-semibold text-white">BonBon Admin</a>
                <p class="mt-2 text-sm text-gray-400">Store management</p>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1">
                <div class="text-xs uppercase tracking-[0.2em] text-gray-400 mb-3">Main Menu</div>
                <a href="#" class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-200 hover:bg-white/10 hover:text-white active" data-section="dashboard">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-white/10 text-pink-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v18H3V3z"></path></svg>
                    </span>
                    Dashboard
                </a>
                <a href="#" class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-200 hover:bg-white/10 hover:text-white" data-section="products">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-white/10 text-pink-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </span>
                    Products
                </a>
                <a href="#" class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-200 hover:bg-white/10 hover:text-white" data-section="inventory">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-white/10 text-pink-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"></path></svg>
                    </span>
                    Inventory
                </a>
                <a href="#" class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-200 hover:bg-white/10 hover:text-white" data-section="orders">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-white/10 text-pink-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M7 7v14m10-14v14M5 7l1.5-3h11L19 7"></path></svg>
                    </span>
                    Orders
                </a>
                <a href="#" class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-200 hover:bg-white/10 hover:text-white" data-section="users">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-white/10 text-pink-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>
                    </span>
                    Users
                </a>
                <a href="#" class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-200 hover:bg-white/10 hover:text-white" data-section="support">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-white/10 text-pink-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </span>
                    Support
                </a>
                <a href="#" class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-gray-200 hover:bg-white/10 hover:text-white" data-section="settings">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-white/10 text-pink-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </span>
                    Settings
                </a>
            </nav>
        </aside>

        <div class="flex flex-col">
            <header class="relative border-b border-slate-200 bg-white px-6 py-4 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex-1 flex flex-col gap-4 sm:flex-row sm:items-center">
                        <form class="flex w-full max-w-xl items-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 shadow-sm">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"></path></svg>
                            <input type="text" placeholder="Search products, orders, users" class="ml-3 w-full bg-transparent outline-none text-sm text-slate-700" />
                        </form>

                        <div class="flex items-center gap-3">
                            <div class="rounded-2xl bg-slate-50 px-4 py-2 text-sm text-slate-600 shadow-sm">
                                <span id="admin-clock">--:-- --</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 justify-end">
                        <button class="inline-flex h-11 items-center gap-2 rounded-2xl bg-slate-50 px-4 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-100" id="adminProfileToggle">
                            <img src="https://via.placeholder.com/32" alt="Admin" class="h-8 w-8 rounded-full object-cover" />
                            <span>Admin</span>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div id="adminProfileMenu" class="hidden absolute right-6 top-20 z-30 w-56 rounded-2xl border border-slate-200 bg-white shadow-soft">
                            <div class="p-4 border-b border-slate-200">
                                <p class="font-semibold">Admin Name</p>
                                <p class="text-sm text-slate-500">admin@bonbon.com</p>
                            </div>
                            <div class="flex flex-col p-3 gap-2">
                                <a href="/logout" class="rounded-xl px-3 py-2 text-sm text-slate-700 hover:bg-slate-100">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-slate-100 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        const clockEl = document.getElementById('admin-clock');
        function updateAdminClock() {
            const now = new Date();
            const options = { hour: 'numeric', minute: 'numeric', hour12: true, month: 'short', day: 'numeric', year: 'numeric' };
            clockEl.textContent = now.toLocaleString('en-US', options);
        }
        updateAdminClock();
        setInterval(updateAdminClock, 60000);

        const profileToggle = document.getElementById('adminProfileToggle');
        const profileMenu = document.getElementById('adminProfileMenu');
        profileToggle?.addEventListener('click', () => {
            profileMenu.classList.toggle('hidden');
        });
        window.addEventListener('click', (event) => {
            if (!profileToggle?.contains(event.target) && !profileMenu?.contains(event.target)) {
                profileMenu?.classList.add('hidden');
            }
        });

        // Admin navigation
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link');
            const sections = document.querySelectorAll('.admin-section');

            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const section = this.getAttribute('data-section');

                    // Remove active class from all links
                    navLinks.forEach(l => l.classList.remove('active'));
                    // Add active class to clicked link
                    this.classList.add('active');

                    // Hide all sections
                    sections.forEach(s => s.classList.add('hidden'));
                    // Show selected section
                    const targetSection = document.getElementById(section + '-section');
                    if (targetSection) {
                        targetSection.classList.remove('hidden');
                    }
                });
            });

            const requestedSection = new URLSearchParams(window.location.search).get('section');
            const defaultLink = document.querySelector(`[data-section="${requestedSection}"]`)
                || document.querySelector('[data-section="dashboard"]');

            defaultLink?.click();
        });
    </script>
</body>
</html>
