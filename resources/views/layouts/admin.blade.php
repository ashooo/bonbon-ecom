<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bonbon Ecom') }} | Admin</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
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
        --sidebar-bg: #F3D5E0;
        --sidebar-bg-hover: rgba(90, 58, 58, 0.1);
        --sidebar-text: #5A3A3A;
    }

    body {
        font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';
        background-color: var(--border-soft);
        color: var(--text-dark);
    }

    .text-pink-600 { color: var(--pink-medium) !important; }
    .bg-pink-600 { background-color: var(--pink-medium) !important; }
    .hover\:bg-pink-700:hover { background-color: var(--pink-dark) !important; }
    .bg-pink-100 { background-color: var(--pink-light) !important; }
    .border-pink-600 { border-color: var(--pink-medium) !important; }
    .shadow-soft { box-shadow: 0 20px 35px rgba(0,0,0,0.08); }
    
    /* Sidebar styling */
    aside {
        background-color: var(--sidebar-bg) !important;
    }
    
    aside .nav-link {
        color: var(--sidebar-text) !important;
    }
    
    aside .nav-link:hover {
        color: var(--sidebar-text) !important;
        background-color: var(--sidebar-bg-hover) !important;
    }

    aside .nav-link {
        position: relative;
    }

    aside .nav-link.active {
        background: rgba(241, 241, 241, 0.22) !important;
        box-shadow: 0 8px 20px rgba(90, 58, 58, 0.1);
    }

    aside .nav-link.active::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 10px;
        bottom: 10px;
        width: 4px;
        border-radius: 999px;
        background: var(--brand-brown);
    }

    aside .nav-link.active span.inline-flex {
        background: rgba(90, 58, 58, 0.16) !important;
    }
    
    aside .text-white,
    aside .text-gray-200,
    aside .text-pink-200,
    aside a {
        color: var(--sidebar-text) !important;
    }
    
    aside .border-white\/10 {
        border-color: var(--sidebar-bg-hover) !important;
    }
    
    /* Make icons visible */
    aside svg {
        color: var(--sidebar-text) !important;
        stroke: var(--sidebar-text) !important;
    }
    
    /* Remove icon backgrounds */
    aside .bg-white\/10,
    aside .bg-white\/20 {
        background-color: transparent !important;
    }
</style>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen">
    @php
        $globalToasts = array_values(array_filter([
            ['type' => 'success', 'message' => session()->pull('success')],
            ['type' => 'error', 'message' => session()->pull('error')],
            ['type' => 'warning', 'message' => session()->pull('warning')],
            ['type' => 'info', 'message' => session()->pull('info')],
            ['type' => 'error', 'message' => $errors->any() ? $errors->first() : null],
        ], fn ($toast) => filled($toast['message'] ?? null)));
    @endphp
    <x-toast-notifications :toasts="$globalToasts" />

    <div class="min-h-screen grid grid-cols-[280px_minmax(0,1fr)]">
        <!-- Sidebar -->
<aside class="flex flex-col" style="background-color: var(--sidebar-bg);">
    <div class="px-6 py-8 border-b" style="border-color: var(--sidebar-bg-hover);">
        <a href="{{ route('admin.dashboard') }}" class="text-2xl font-black" style="color: var(--sidebar-text);">BonBon Admin</a>
        <p class="mt-1 text-sm" style="color: var(--sidebar-text);">Store management</p>
    </div>

    <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1">
        <a href="#" class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold transition-all duration-200 hover:bg-white/20" data-section="dashboard" style="color: var(--sidebar-text);">
            <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl transition-all duration-200">
                <svg class="w-6 h-6" fill="none" stroke="var(--sidebar-text)" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </span>
            Dashboard
        </a>
        
        <a href="#" class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold transition-all duration-200 hover:bg-white/20" data-section="products" style="color: var(--sidebar-text);">
            <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl transition-all duration-200">
                <svg class="w-6 h-6" fill="none" stroke="var(--sidebar-text)" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7L4 7M20 12L4 12M20 17L4 17M4 4v16h16V4z"></path>
                </svg>
            </span>
            Products
        </a>

        <a href="#" class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold transition-all duration-200 hover:bg-white/20" data-section="categories" style="color: var(--sidebar-text);">
            <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl transition-all duration-200">
                <svg class="w-6 h-6" fill="none" stroke="var(--sidebar-text)" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"></path>
                </svg>
            </span>
            Categories
        </a>
        
        <a href="#" class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold transition-all duration-200 hover:bg-white/20" data-section="inventory" style="color: var(--sidebar-text);">
            <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl transition-all duration-200">
                <svg class="w-6 h-6" fill="none" stroke="var(--sidebar-text)" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
            </span>
            Inventory
        </a>
        
        <a href="#" class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold transition-all duration-200 hover:bg-white/20" data-section="orders" style="color: var(--sidebar-text);">
            <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl transition-all duration-200">
                <svg class="w-6 h-6" fill="none" stroke="var(--sidebar-text)" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </span>
            Orders
        </a>
        
        <a href="#" class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold transition-all duration-200 hover:bg-white/20" data-section="users" style="color: var(--sidebar-text);">
            <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl transition-all duration-200">
                <svg class="w-6 h-6" fill="none" stroke="var(--sidebar-text)" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </span>
            Users
        </a>
        
        <a href="#" class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold transition-all duration-200 hover:bg-white/20" data-section="support" style="color: var(--sidebar-text);">
            <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl transition-all duration-200">
                <svg class="w-6 h-6" fill="none" stroke="var(--sidebar-text)" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </span>
            Chat Support
        </a>
        
        <a href="#" class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold transition-all duration-200 hover:bg-white/20" data-section="customization" style="color: var(--sidebar-text);">
            <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl transition-all duration-200">
                <svg class="w-6 h-6" fill="none" stroke="var(--sidebar-text)" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M9 16h6M9 8h6M6 5h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2z"></path>
                </svg>
            </span>
            Customization
        </a>

        <a href="#" class="nav-link group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold transition-all duration-200 hover:bg-white/20" data-section="settings" style="color: var(--sidebar-text);">
            <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl transition-all duration-200">
                <svg class="w-6 h-6" fill="none" stroke="var(--sidebar-text)" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </span>
            Settings
        </a>
    </nav>
</aside>

        <div class="flex flex-col">
            <header class="relative border-b border-[#ECD8E0] bg-[#FBF2F6] px-6 py-4 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex-1 flex flex-col gap-4 sm:flex-row sm:items-center">
                        <div class="flex items-center gap-3">
                            <div class="rounded-2xl bg-[#FFFFFF] px-4 py-2 text-sm text-[#8A6A76] border border-[#ECD8E0] shadow-sm">
                                <span id="admin-clock">--:-- --</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 justify-end">
                        @php
                            $__admin_avatar = $storeSettings?->chat_avatar_url ?? 'https://via.placeholder.com/32';
                        @endphp
                        <button class="inline-flex h-11 items-center gap-2 rounded-2xl bg-slate-50 px-4 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-100" id="adminProfileToggle">
                            <img src="{{ $__admin_avatar }}" alt="Admin" class="h-8 w-8 rounded-full object-cover" />
                            <span>Admin</span>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div id="adminProfileMenu" class="hidden absolute right-6 top-20 z-30 w-56 rounded-2xl border border-[#ECD8E0] bg-[#FBF2F6] shadow-soft">
                            <div class="p-4 border-b border-[#ECD8E0]">
                                <p class="font-semibold">Admin Name</p>
                                <p class="text-sm text-[#8F6172]">admin@bonbon.com</p>
                            </div>
                            <div class="flex flex-col p-3 gap-2">
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full rounded-xl px-3 py-2 text-left text-sm text-[#4D2E38] hover:bg-[#FBEAF1]">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-[#FBEAF1] p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')

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
            const hasSectionUi = sections.length > 0;
            const dashboardUrl = @json(route('admin.dashboard'));
            const activeSectionInput = document.getElementById('active-admin-section');

            const applySection = (section) => {
                if (!section) return;

                navLinks.forEach(l => l.classList.remove('active'));
                const link = document.querySelector(`.nav-link[data-section="${section}"]`);
                link?.classList.add('active');

                sections.forEach(s => s.classList.add('hidden'));
                const targetSection = document.getElementById(section + '-section');
                if (targetSection) {
                    targetSection.classList.remove('hidden');
                }

                const nextUrl = `${window.location.pathname}?section=${encodeURIComponent(section)}`;
                window.history.replaceState({}, '', nextUrl);
                if (activeSectionInput) activeSectionInput.value = section;
            };

            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const section = this.getAttribute('data-section');

                    if (!hasSectionUi) {
                        window.location.href = `${dashboardUrl}?section=${encodeURIComponent(section)}`;
                        return;
                    }

                    e.preventDefault();
                    applySection(section);
                });
            });

            if (hasSectionUi) {
                const requestedSection = new URLSearchParams(window.location.search).get('section');
                const fallbackSection = 'dashboard';
                const validRequested = document.querySelector(`.nav-link[data-section="${requestedSection}"]`)
                    ? requestedSection
                    : fallbackSection;
                applySection(validRequested);
            }
        });
    </script>

    <div id="bonbon-confirm-modal" class="fixed inset-0 z-[10060] hidden items-center justify-center bg-[#2E2E2E]/45 p-4" aria-hidden="true">
        <div class="w-full max-w-md rounded-2xl border border-[#EED9DE] bg-white p-5 shadow-2xl">
            <h3 id="bonbon-confirm-title" class="text-lg font-semibold text-[#5A3A3A]">Please confirm</h3>
            <p id="bonbon-confirm-message" class="mt-2 text-sm text-[#8C6770]">Are you sure you want to continue?</p>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" id="bonbon-confirm-cancel" class="rounded-xl border border-[#D6B7C3] bg-white px-4 py-2 text-sm font-semibold text-[#6B4957] transition hover:bg-[#FAF1F5]">Cancel</button>
                <button type="button" id="bonbon-confirm-ok" class="rounded-xl bg-[#C88A92] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#7A5252]">Confirm</button>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const modal = document.getElementById('bonbon-confirm-modal');
            const titleEl = document.getElementById('bonbon-confirm-title');
            const messageEl = document.getElementById('bonbon-confirm-message');
            const cancelBtn = document.getElementById('bonbon-confirm-cancel');
            const okBtn = document.getElementById('bonbon-confirm-ok');
            let pendingForm = null;

            const closeModal = () => {
                pendingForm = null;
                modal?.classList.add('hidden');
                modal?.classList.remove('flex');
                modal?.setAttribute('aria-hidden', 'true');
            };

            const openModal = (form) => {
                if (!modal) return;
                pendingForm = form;
                titleEl.textContent = form.dataset.confirmTitle || 'Please confirm';
                messageEl.textContent = form.dataset.confirmMessage || 'Are you sure you want to continue?';
                okBtn.textContent = form.dataset.confirmOk || 'Confirm';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modal.setAttribute('aria-hidden', 'false');
            };

            document.addEventListener('submit', (event) => {
                const form = event.target.closest('form[data-confirm]');
                if (!form || form.dataset.confirmBypassed === '1') return;
                event.preventDefault();
                openModal(form);
            }, true);

            okBtn?.addEventListener('click', () => {
                if (!pendingForm) return closeModal();
                pendingForm.dataset.confirmBypassed = '1';
                pendingForm.requestSubmit();
                pendingForm.dataset.confirmBypassed = '0';
                closeModal();
            });

            cancelBtn?.addEventListener('click', closeModal);
            modal?.addEventListener('click', (event) => {
                if (event.target === modal) closeModal();
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        })();
    </script>
</body>
</html>
