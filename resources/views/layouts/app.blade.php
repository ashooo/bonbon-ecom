<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bonbon Ecom') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=great-vibes:400|instrument-sans:400,500,600" rel="stylesheet" />
    @if (file_exists(public_path('css/bonbon-loader.css')))
        <link rel="stylesheet" href="/css/bonbon-loader.css">
    @endif
    @if (file_exists(public_path('js/bonbon-loader.js')))
        <script src="/js/bonbon-loader.js" defer></script>
    @endif
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
            --chat-panel-width: 430px;
        }

        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';
            background-color: var(--border-soft);
            color: var(--text-dark);
        }

        body.chat-open #app-shell {
            padding-right: var(--chat-panel-width);
        }
        body.chat-open #bonbon-chat-open {
            opacity: 0;
            pointer-events: none;
            transform: translateY(12px);
        }

        .bg-pink-600 { background-color: var(--pink-medium) !important; }
        .hover\:bg-pink-700:hover { background-color: var(--pink-dark) !important; }
        .text-pink-600 { color: var(--pink-medium) !important; }
        .hover\:text-pink-600:hover { color: var(--pink-medium) !important; }
        .text-pink-500 { color: var(--pink-light) !important; }
        .bg-pink-100 { background-color: var(--pink-light) !important; }
        .bg-pink-50 { background-color: #F9EFF1 !important; }
        .border-pink-600 { border-color: var(--pink-medium) !important; }
        .bg-gray-50 { background-color: var(--cream) !important; }
        .text-gray-700 { color: var(--brand-brown) !important; }
        .text-gray-900 { color: var(--text-dark) !important; }
        .bg-white { background-color: var(--white-soft) !important; }
        .bg-gray-100 { background-color: var(--border-soft) !important; }
        .border-gray-300 { border-color: var(--border-soft) !important; }
        .hover\:bg-gray-50:hover { background-color: #F5F5F5 !important; }
        .btn-pink {
            background-color: var(--pink-medium) !important;
            color: #ffffff !important;
        }
        .btn-pink:hover {
            background-color: var(--pink-dark) !important;
        }
        .font-script {
            font-family: 'Great Vibes', cursive;
        }
        #app-shell {
            transition: padding-right 220ms ease;
        }
        #bonbon-chat-panel {
            width: var(--chat-panel-width);
            transform: translateX(calc(var(--chat-panel-width) + 16px));
            transition: transform 220ms ease;
        }
        body.chat-open #bonbon-chat-panel {
            transform: translateX(0);
        }
        @media (max-width: 1023px) {
            body.chat-open #app-shell {
                padding-right: 0;
            }
            #bonbon-chat-panel {
                width: min(100vw, 100%);
                transform: translateX(100%);
            }
            body.chat-open #bonbon-chat-backdrop {
                opacity: 1;
                pointer-events: auto;
            }
        }
    </style>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-[#F5F5F5] text-[#2E2E2E]">
    @unless (View::hasSection('hideGlobalLoader'))
    <!-- Page Loading Overlay -->
    <div id="page-loader" style="position:fixed;inset:0;z-index:99999;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:16px;background:#F5F5F5;transition:opacity 0.5s ease, visibility 0.5s ease;">
        <div style="position:relative;width:96px;height:96px;display:flex;align-items:center;justify-content:center;">
            {{-- Orbiting dots (inline for instant render) --}}
            <div style="position:absolute;inset:0;animation:_plOrbit 3s linear infinite;">
                <span style="position:absolute;top:50%;left:50%;width:8px;height:8px;margin:-4px 0 0 -4px;border-radius:50%;background:#C88A92;transform:rotate(0deg) translateX(44px);"></span>
                <span style="position:absolute;top:50%;left:50%;width:7px;height:7px;margin:-3.5px 0 0 -3.5px;border-radius:50%;background:#E6B7BE;transform:rotate(60deg) translateX(44px);"></span>
                <span style="position:absolute;top:50%;left:50%;width:6px;height:6px;margin:-3px 0 0 -3px;border-radius:50%;background:#F5E6E8;transform:rotate(120deg) translateX(44px);"></span>
                <span style="position:absolute;top:50%;left:50%;width:6px;height:6px;margin:-3px 0 0 -3px;border-radius:50%;background:#C88A92;opacity:0.5;transform:rotate(180deg) translateX(44px);"></span>
                <span style="position:absolute;top:50%;left:50%;width:5px;height:5px;margin:-2.5px 0 0 -2.5px;border-radius:50%;background:#E6B7BE;opacity:0.4;transform:rotate(240deg) translateX(44px);"></span>
                <span style="position:absolute;top:50%;left:50%;width:4px;height:4px;margin:-2px 0 0 -2px;border-radius:50%;background:#F5E6E8;opacity:0.3;transform:rotate(300deg) translateX(44px);"></span>
            </div>
            {{-- Cake SVG --}}
            <svg style="animation:_plBounce 1.4s ease-in-out infinite;" viewBox="0 0 64 72" fill="none" xmlns="http://www.w3.org/2000/svg" width="48" height="54">
                <path d="M28 8 C28 4,32 2,32 0" stroke="#E6B7BE" stroke-width="1.5" stroke-linecap="round" fill="none" opacity="0.5" style="animation:_plSteam 2s ease-in-out infinite;"/>
                <path d="M36 10 C36 6,40 4,40 2" stroke="#E6B7BE" stroke-width="1.5" stroke-linecap="round" fill="none" opacity="0.3" style="animation:_plSteam 2s ease-in-out 0.7s infinite;"/>
                <rect x="29" y="12" width="4" height="14" rx="2" fill="#F8E2E7"/>
                <ellipse cx="31" cy="11" rx="3" ry="4" fill="#FFD97D"/>
                <ellipse cx="31" cy="12" rx="2" ry="2.5" fill="#FFB347"/>
                <path d="M12 30 C12 30,16 24,22 26 C28 28,30 22,32 22 C34 22,36 28,42 26 C48 24,52 30,52 30 L52 38 L12 38 Z" fill="#C88A92"/>
                <rect x="12" y="34" width="40" height="12" rx="3" fill="#F5E6E8"/>
                <rect x="12" y="34" width="40" height="4" rx="2" fill="#E6B7BE" opacity="0.5"/>
                <path d="M8 46 C8 44,12 42,18 44 C24 46,26 40,32 40 C38 40,40 46,46 44 C52 42,56 44,56 46 L56 48 L8 48 Z" fill="#C88A92"/>
                <rect x="8" y="46" width="48" height="14" rx="4" fill="#F5E6E8"/>
                <rect x="8" y="46" width="48" height="4" rx="2" fill="#E6B7BE" opacity="0.4"/>
                <ellipse cx="32" cy="62" rx="28" ry="4" fill="#EED9DE"/>
                <circle cx="20" cy="37" r="1.2" fill="#FFB6C1"/><circle cx="28" cy="36" r="1" fill="#FFD97D"/>
                <circle cx="36" cy="37" r="1.2" fill="#FFB6C1"/><circle cx="44" cy="36" r="1" fill="#FFD97D"/>
                <circle cx="16" cy="52" r="1.2" fill="#FFD97D"/><circle cx="24" cy="53" r="1" fill="#FFB6C1"/>
                <circle cx="32" cy="51" r="1.3" fill="#B5EAD7"/><circle cx="40" cy="53" r="1" fill="#FFB6C1"/>
                <circle cx="48" cy="52" r="1.2" fill="#FFD97D"/>
                <circle cx="31" cy="22" r="4" fill="#E74C6F"/>
                <circle cx="29.5" cy="20.5" r="1.2" fill="#FF7E9D" opacity="0.7"/>
                <path d="M31 18 C33 14,35 16,34 18" stroke="#5A3A3A" stroke-width="1" fill="none" stroke-linecap="round"/>
            </svg>
        </div>
        <p style="font-family:'Instrument Sans',sans-serif;font-size:13px;font-weight:700;color:#C88A92;letter-spacing:0.15em;text-transform:uppercase;animation:pulse 2s ease-in-out infinite;">Loading your treats...</p>
    </div>
    <style>
        @keyframes _plOrbit { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
        @keyframes _plBounce { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
        @keyframes _plSteam { 0%{opacity:0;transform:translateY(4px)} 30%{opacity:0.6} 100%{opacity:0;transform:translateY(-8px)} }
    </style>
    <script>
        window.addEventListener('load', function() {
            var loader = document.getElementById('page-loader');
            if (loader) {
                loader.style.opacity = '0';
                loader.style.visibility = 'hidden';
                setTimeout(function() { loader.remove(); }, 600);
            }
        });
    </script>
    @endunless
    @unless (View::hasSection('hideChatbot'))
    <div id="bonbon-chat-backdrop" class="pointer-events-none fixed inset-0 z-30 bg-[#2E2E2E]/25 opacity-0 transition-opacity lg:hidden"></div>

    <aside id="bonbon-chat-panel" class="fixed inset-y-0 right-0 z-40 flex h-screen max-w-full flex-col overflow-hidden border-l border-[#EED9DE] bg-[linear-gradient(180deg,_#fffefe,_#fff7f8)] shadow-[-18px_0_45px_rgba(90,58,58,0.14)]">
        <button id="bonbon-chat-resizer" type="button" class="absolute left-0 top-0 hidden h-full w-2 -translate-x-1/2 cursor-col-resize rounded-full bg-transparent lg:block" aria-label="Resize chat panel"></button>

        <div class="shrink-0 bg-white/90 px-5 py-4 backdrop-blur">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="relative flex h-11 w-11 items-center justify-center rounded-full bg-[#F8E2E7] text-sm font-semibold text-[#5A3A3A]">
                        @if($storeSettings?->chat_avatar_url)
                            <img src="{{ $storeSettings->chat_avatar_url }}" alt="{{ $storeSettings->chat_display_name ?? 'Bonbon Chat' }}" class="h-full w-full rounded-full object-cover">
                        @else
                            {{ strtoupper(substr($storeSettings?->chat_display_name ?? 'Bonbon Chat', 0, 2)) }}
                        @endif
                        <span id="bonbon-chat-status-dot" class="absolute bottom-0 right-0 h-3.5 w-3.5 rounded-full border-2 border-white bg-slate-300"></span>
                    </span>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#C88A92]">{{ $storeSettings?->chat_display_name ?: 'Bonbon Chat' }}</p>
                        <p id="bonbon-chat-status-text" class="text-sm font-semibold text-[#5A3A3A]">Offline</p>
                    </div>
                </div>
                <button id="bonbon-chat-close" type="button" class="rounded-full p-2 text-[#8C6770] transition hover:bg-[#FFF0F3]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6l-12 12" />
                    </svg>
                </button>
            </div>

            <div class="mt-4 rounded-[1.6rem] border border-[#F1DADF] bg-[#FFF7F8] p-2">
                <div class="grid grid-cols-2 gap-2">
                    <button
                        type="button"
                        id="bonbon-chat-mode-live"
                        data-chat-mode="live"
                        class="bonbon-chat-mode-toggle rounded-[1.1rem] px-4 py-3 text-sm font-semibold transition"
                    >
                        Live Chat
                    </button>
                    <button
                        type="button"
                        id="bonbon-chat-mode-ai"
                        data-chat-mode="ai"
                        class="bonbon-chat-mode-toggle rounded-[1.1rem] px-4 py-3 text-sm font-semibold transition"
                    >
                        AI Chat
                    </button>
                </div>
                <p id="bonbon-chat-mode-description" class="px-2 pt-3 text-xs leading-6 text-[#9E7680]">
                    Chat with Bonbon Support in real time.
                </p>
            </div>
        </div>

        <div id="bonbon-chat-thread" class="min-h-0 flex-1 overflow-y-auto px-4 py-5">
            <div id="bonbon-chat-empty" class="space-y-5">
                <div class="rounded-[1.75rem] border border-dashed border-[#E9CBD3] bg-white px-5 py-6 text-sm leading-7 text-[#8C6770]">
                    Ask about shipping, prices, custom cakes, or send image pegs. Messages update live with typing, delivery, and seen status.
                </div>
                <div>
                    <p class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-[#B38A93]">Quick prompts</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach (['What are your best sellers?', 'Do you offer same-day delivery?', 'How much is shipping?', 'Can I send a peg design?'] as $prompt)
                            <button type="button" class="bonbon-quick-prompt rounded-full border border-[#F0D5DB] bg-white px-4 py-2 text-xs font-semibold text-[#6F4C54] transition hover:-translate-y-0.5 hover:bg-[#FFF5F7]" data-prompt="{{ $prompt }}">{{ $prompt }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="shrink-0 border-t border-[#F1DADF] bg-white px-4 py-4">
            <div id="bonbon-chat-typing-indicator" class="mb-3 hidden text-sm text-[#9E7680]">Bonbon Support is typing...</div>

            <form id="bonbon-chat-send-form" class="space-y-3" enctype="multipart/form-data">
                <div id="bonbon-chat-attachment-preview" class="hidden rounded-[1.25rem] border border-[#EED9DE] bg-[#FFF7F8] p-3">
                    <div class="flex items-start justify-between gap-3">
                        <div id="bonbon-chat-attachment-preview-content" class="min-w-0 flex-1"></div>
                        <button id="bonbon-chat-attachment-clear" type="button" class="rounded-full p-2 text-[#8C6770] transition hover:bg-white">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6l-12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex flex-wrap items-end gap-3 rounded-[1.75rem] border border-[#EED9DE] bg-[#FFF9FA] p-3">
                    <label class="inline-flex h-12 w-12 cursor-pointer items-center justify-center rounded-full bg-white text-[#6B4951] shadow-sm ring-1 ring-[#F1DADF] transition hover:bg-[#FFF3F5]">
                        <input id="bonbon-chat-attachment" name="attachment" type="file" class="hidden" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m18 13-5.172 5.172a4 4 0 1 1-5.656-5.656L13.5 6.187A2 2 0 0 1 16.328 9l-6.364 6.364a.75.75 0 1 1-1.06-1.06l5.657-5.657" />
                        </svg>
                    </label>

                    <div class="min-w-[180px] flex-1">
                        <textarea id="bonbon-chat-message-input" name="body" rows="1" class="max-h-40 w-full resize-none bg-transparent px-2 py-3 text-sm text-[#5A3A3A] outline-none placeholder:text-[#B28D95]" placeholder="Message Bonbon Support..."></textarea>
                    </div>

                    <button type="submit" class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-[#5A3A3A] text-white transition hover:bg-[#7A5252]">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14m-6-6 6 6-6 6" />
                        </svg>
                    </button>
                </div>

                <div class="flex items-center justify-between gap-3 px-2">
                    <p id="bonbon-chat-upload-name" class="text-xs text-[#9E7680]">Images, PDF, DOC, DOCX up to 5MB</p>
                </div>
            </form>
        </div>
    </aside>
    @endunless

    <div id="app-shell">
        @include('components.navbar')

        <main class="container mx-auto px-4 py-8">
            @if (session('success'))
                <div class="mb-6 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-3xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>

        @unless (View::hasSection('hideFooter'))
            @include('components.footer')
        @endunless
    </div>

    @unless (View::hasSection('hideChatbot'))
    <button
        id="bonbon-chat-open"
        type="button"
        class="fixed bottom-5 right-5 z-40 flex items-center gap-3 rounded-full bg-[#5A3A3A] px-5 py-3 text-white shadow-[0_18px_40px_rgba(90,58,58,0.28)] transition hover:-translate-y-0.5 hover:bg-[#7A5252]"
    >
        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h8M8 14h5m-8 6 1.7-3.4A8 8 0 1 1 20 12a8 8 0 0 1-8 8H5Z" />
            </svg>
        </span>
        <span>
            <span class="block text-xs uppercase tracking-[0.24em] text-white/70">Need help?</span>
            <span class="block text-sm font-semibold">Open Bonbon Chat</span>
        </span>
    </button>
    @endunless

    @stack('scripts')

    @unless (View::hasSection('hideChatbot'))
    <script>
        (() => {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            const backdrop = document.getElementById('bonbon-chat-backdrop');
            const openButton = document.getElementById('bonbon-chat-open');
            const closeButton = document.getElementById('bonbon-chat-close');
            const resizer = document.getElementById('bonbon-chat-resizer');
            const thread = document.getElementById('bonbon-chat-thread');
            const emptyState = document.getElementById('bonbon-chat-empty');
            const sendForm = document.getElementById('bonbon-chat-send-form');
            const messageInput = document.getElementById('bonbon-chat-message-input');
            const attachmentInput = document.getElementById('bonbon-chat-attachment');
            const attachmentPreview = document.getElementById('bonbon-chat-attachment-preview');
            const attachmentPreviewContent = document.getElementById('bonbon-chat-attachment-preview-content');
            const attachmentClear = document.getElementById('bonbon-chat-attachment-clear');
            const uploadName = document.getElementById('bonbon-chat-upload-name');
            const typingIndicator = document.getElementById('bonbon-chat-typing-indicator');
            const statusDot = document.getElementById('bonbon-chat-status-dot');
            const statusText = document.getElementById('bonbon-chat-status-text');
            const liveModeButton = document.getElementById('bonbon-chat-mode-live');
            const aiModeButton = document.getElementById('bonbon-chat-mode-ai');
            const modeDescription = document.getElementById('bonbon-chat-mode-description');
            let typingTimer = null;
            let typingState = false;
            let currentMessagesHash = '';
            let currentMessages = [];
            let hasLoaded = false;
            let isResizing = false;
            let heartbeatTimer = null;
            let currentChannelName = null;
            let currentConversation = null;
            let currentMode = window.sessionStorage.getItem('bonbon-chat-mode') === 'ai' ? 'ai' : 'live';
            let aiMessages = [];
            let aiMessageSequence = 0;
            let aiReplyTimer = null;
            const aiStorageKey = 'bonbon-chat-ai-messages-v1';

            const routes = {
                session: @json(route('chat.session')),
                send: @json(route('chat.messages.store')),
                aiSend: @json(route('chat.ai-message')),
                typing: @json(route('chat.typing')),
                presence: @json(route('chat.presence')),
            };

            const buildHeaders = (json = false) => {
                const socketId = window.Echo?.socketId?.();

                return {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    ...(json ? { 'Content-Type': 'application/json' } : {}),
                    ...(socketId ? { 'X-Socket-ID': socketId } : {}),
                };
            };

            const escapeHtml = (value) => {
                const div = document.createElement('div');
                div.textContent = value ?? '';
                return div.innerHTML;
            };

            const isNearBottom = () => thread.scrollHeight - thread.scrollTop - thread.clientHeight < 120;
            const scrollToBottom = () => {
                thread.scrollTop = thread.scrollHeight;
            };

            const getTimeStampParts = (date = new Date()) => {
                const fullTimestamp = date.toLocaleString([], {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                });

                const timestamp = date.toLocaleTimeString([], {
                    hour: 'numeric',
                    minute: '2-digit',
                });

                return {
                    created_at: date.toISOString(),
                    timestamp,
                    full_timestamp: fullTimestamp,
                };
            };

            const buildAiMessage = ({ senderType, body, receiptLabel = 'Seen', products = [] }) => {
                aiMessageSequence += 1;
                const now = getTimeStampParts();

                return {
                    id: `ai-${aiMessageSequence}`,
                    sender_type: senderType,
                    sender_name: senderType === 'customer' ? 'You' : 'Bonbon AI',
                    body,
                    products,
                    attachment_url: null,
                    attachment_download_url: null,
                    attachment_view_url: null,
                    attachment_name: null,
                    attachment_mime: null,
                    attachment_size: null,
                    is_image: false,
                    created_at: now.created_at,
                    timestamp: now.timestamp,
                    full_timestamp: now.full_timestamp,
                    is_mine: senderType === 'customer',
                    receipt_label: receiptLabel,
                };
            };

            const renderAiProductCards = (products = []) => {
                if (!Array.isArray(products) || !products.length) {
                    return '';
                }

                const tokenField = csrf ? `<input type="hidden" name="_token" value="${escapeHtml(csrf)}">` : '';

                return `
                    <div class="mt-3 grid gap-3">
                        ${products.map((product) => `
                            <div class="overflow-hidden rounded-[1.5rem] border border-[#F0DCE1] bg-white shadow-sm">
                                <a href="${product.product_url}" class="flex items-stretch gap-3 p-3 transition hover:bg-[#FFF8F9]">
                                    <img
                                        src="${escapeHtml(product.image_url || ('https://via.placeholder.com/140x140?text=' + encodeURIComponent(product.name)))}"
                                        alt="${escapeHtml(product.name)}"
                                        class="h-24 w-24 rounded-[1.1rem] object-cover"
                                    >
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <p class="text-sm font-semibold text-[#5A3A3A]">${escapeHtml(product.name)}</p>
                                                <p class="mt-1 text-xs text-[#9E7680]">${escapeHtml(product.category || 'BonBon Product')}</p>
                                            </div>
                                            <span class="rounded-full bg-[#FFF1F4] px-3 py-1 text-xs font-semibold text-[#B75C75]">PHP ${escapeHtml(product.formatted_price)}</span>
                                        </div>
                                        <p class="mt-2 text-xs leading-5 text-[#7F5B64]">${escapeHtml(product.description || '')}</p>
                                    </div>
                                </a>
                                <div class="flex flex-wrap gap-2 border-t border-[#F5E6E8] px-3 py-3">
                                    <a href="${product.product_url}" class="rounded-full border border-[#E7C7CF] bg-white px-4 py-2 text-xs font-semibold text-[#7A5252] transition hover:bg-[#FFF4F6]">View Product</a>
                                    <form method="POST" action="${product.add_to_cart_url}" class="inline-flex">
                                        ${tokenField}
                                        <input type="hidden" name="product_id" value="${escapeHtml(String(product.product_id))}">
                                        ${product.variant_id ? `<input type="hidden" name="variant_id" value="${escapeHtml(String(product.variant_id))}">` : ''}
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="rounded-full bg-[#5A3A3A] px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#7A5252]">Add to Cart</button>
                                    </form>
                                    <a href="${product.checkout_url}" class="rounded-full bg-[#C94F7C] px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#b8456e]">Checkout</a>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
            };

            const saveAiMessages = () => {
                window.sessionStorage.setItem(aiStorageKey, JSON.stringify(aiMessages));
            };

            const loadAiMessages = () => {
                try {
                    const stored = JSON.parse(window.sessionStorage.getItem(aiStorageKey) ?? '[]');
                    aiMessages = Array.isArray(stored) ? stored : [];
                    aiMessageSequence = aiMessages.length;
                } catch (error) {
                    aiMessages = [];
                    aiMessageSequence = 0;
                }
            };

            const getAiReply = (input) => {
                const text = (input ?? '').toLowerCase();

                if (text.includes('delivery') || text.includes('ship') || text.includes('shipping')) {
                    return "Bonbon AI here. Delivery availability depends on your location and the order schedule. For exact delivery coverage and fees, switch to Live Chat and our team can confirm it for you.";
                }

                if (text.includes('same day') || text.includes('today')) {
                    return "For same-day requests, the fastest route is Live Chat so support can check stock and production timing right away.";
                }

                if (text.includes('custom') || text.includes('peg') || text.includes('design')) {
                    return "Yes, Bonbon can handle custom cake requests and peg inspirations. You can send your peg in Live Chat so the support team can review it properly.";
                }

                if (text.includes('price') || text.includes('hm') || text.includes('cost') || text.includes('how much')) {
                    return "Prices vary by product, size, and customizations. If you already know the item you want, Live Chat is best for an exact quote.";
                }

                if (text.includes('best seller') || text.includes('popular')) {
                    return "Our best sellers are usually the most visually appealing celebration cakes and easy-to-order favorites. You can also browse the featured products section for quick picks.";
                }

                if (text.includes('payment') || text.includes('gcash') || text.includes('cod')) {
                    return "Payment options can depend on the checkout setup currently enabled by the store. For the most accurate answer, switch to Live Chat and support can confirm what is available.";
                }

                if (text.includes('hello') || text.includes('hi') || text.includes('hey')) {
                    return "Hi! You can ask me quick questions here, or switch to Live Chat if you want a real Bonbon support reply.";
                }

                return "I can help with quick store questions, but for exact order details, custom requests, or delivery confirmation, please switch to Live Chat so Bonbon Support can assist you directly.";
            };

            const renderModeButtons = () => {
                [liveModeButton, aiModeButton].forEach((button) => {
                    if (!button) {
                        return;
                    }

                    const active = button.dataset.chatMode === currentMode;
                    button.className = `bonbon-chat-mode-toggle rounded-[1.1rem] px-4 py-3 text-sm font-semibold transition ${
                        active
                            ? 'bg-[#5A3A3A] text-white shadow-sm'
                            : 'bg-white text-[#7A5252] hover:bg-[#FFF1F4]'
                    }`;
                });

                if (!modeDescription) {
                    return;
                }

                modeDescription.textContent = currentMode === 'ai'
                    ? 'Get quick automated answers from Bonbon AI.'
                    : 'Chat with Bonbon Support in real time.';
            };

            const renderCurrentMode = () => {
                renderModeButtons();

                if (currentMode === 'ai') {
                    resetConversationSubscription();
                    stopHeartbeat();
                    typingIndicator.classList.add('hidden');
                    statusDot.className = 'absolute bottom-0 right-0 h-3.5 w-3.5 rounded-full border-2 border-white bg-amber-400';
                    statusText.textContent = 'AI instant replies';
                    messageInput.placeholder = 'Ask Bonbon AI...';
                    attachmentInput.disabled = true;
                    attachmentInput.value = '';
                    renderComposerAttachment(null);
                    uploadName.textContent = 'AI chat is text-only. Switch to Live Chat to send files.';
                    renderMessages(aiMessages);
                    return;
                }

                messageInput.placeholder = 'Message Bonbon Support...';
                attachmentInput.disabled = false;
                sync();
                startHeartbeat();
            };

            const subscribeToConversation = (channelName) => {
                if (!window.Echo || !channelName || currentChannelName === channelName) {
                    return;
                }

                if (currentChannelName) {
                    window.Echo.leave(currentChannelName);
                }

                currentChannelName = channelName;
                window.Echo.channel(channelName).listen('.chat.conversation.updated', () => {
                    sync(false);
                });
            };

            const resetConversationSubscription = () => {
                if (!window.Echo || !currentChannelName) {
                    currentChannelName = null;
                    return;
                }

                window.Echo.leave(currentChannelName);
                currentChannelName = null;
            };

            const startHeartbeat = () => {
                stopHeartbeat();
                heartbeatTimer = window.setInterval(() => {
                    fetch(routes.presence, {
                        method: 'POST',
                        headers: buildHeaders(true),
                        body: JSON.stringify({}),
                    });
                }, 30000);
            };

            const stopHeartbeat = () => {
                if (heartbeatTimer) {
                    window.clearInterval(heartbeatTimer);
                    heartbeatTimer = null;
                }
            };

            const openPanel = () => {
                document.body.classList.add('chat-open');

                if (!hasLoaded) {
                    if (currentMode === 'ai') {
                        renderCurrentMode();
                    } else {
                        sync();
                        startHeartbeat();
                    }
                    hasLoaded = true;
                } else if (currentMode === 'ai') {
                    renderCurrentMode();
                } else {
                    startHeartbeat();
                }
            };

            const closePanel = () => {
                document.body.classList.remove('chat-open');
                stopHeartbeat();
            };

            const formatAttachment = (message) => {
                if (!message.attachment_url) {
                    return '';
                }

                if (message.is_image) {
                    return `
                        <div class="mt-3 overflow-hidden rounded-2xl bg-white/70">
                            <a href="${message.attachment_view_url || message.attachment_url}" target="_blank" class="block">
                                <img src="${message.attachment_view_url || message.attachment_url}" alt="${escapeHtml(message.attachment_name ?? 'Attachment')}" class="max-h-72 w-full object-cover">
                            </a>
                            <div class="flex items-center justify-end border-t border-white/60 px-3 py-2">
                                <a href="${message.attachment_download_url || message.attachment_url}" download class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white text-[#5A3A3A] shadow-sm transition hover:bg-[#FFF0F3]">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    `;
                }

                return `
                    <div class="mt-3 flex items-center gap-3 rounded-2xl border border-white/50 bg-white/75 px-4 py-3 text-sm font-semibold text-[#5A3A3A]">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[#F8E4E8]">FILE</span>
                        <span class="truncate flex-1">${escapeHtml(message.attachment_name ?? 'Download attachment')}</span>
                        <a href="${message.attachment_download_url || message.attachment_url}" download class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white text-[#5A3A3A] shadow-sm transition hover:bg-[#FFF0F3]">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14" />
                            </svg>
                        </a>
                    </div>
                `;
            };

            const renderComposerAttachment = (file) => {
                if (!file) {
                    attachmentPreview.classList.add('hidden');
                    attachmentPreviewContent.innerHTML = '';
                    uploadName.textContent = 'Images, PDF, DOC, DOCX up to 5MB';
                    return;
                }

                uploadName.textContent = file.name;

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = () => {
                        attachmentPreviewContent.innerHTML = `
                            <div class="flex items-start gap-3">
                                <img src="${reader.result}" alt="${escapeHtml(file.name)}" class="h-20 w-20 rounded-2xl object-cover">
                                <div class="min-w-0 pt-1">
                                    <p class="truncate text-sm font-semibold text-[#5A3A3A]">${escapeHtml(file.name)}</p>
                                    <p class="mt-1 text-xs text-[#8C6770]">Image ready to send</p>
                                </div>
                            </div>
                        `;
                        attachmentPreview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                    return;
                }

                attachmentPreviewContent.innerHTML = `
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-white text-xs font-semibold text-[#5A3A3A] shadow-sm ring-1 ring-[#F1DADF]">FILE</span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-[#5A3A3A]">${escapeHtml(file.name)}</p>
                            <p class="mt-1 text-xs text-[#8C6770]">Attachment ready to send</p>
                        </div>
                    </div>
                `;
                attachmentPreview.classList.remove('hidden');
            };

            const renderMessages = (messages) => {
                currentMessages = messages;
                const nextHash = JSON.stringify(messages.map((message) => [message.id, message.receipt_label]));
                const shouldStick = isNearBottom();

                if (!messages.length) {
                    thread.innerHTML = '';
                    thread.appendChild(emptyState);
                    currentMessagesHash = nextHash;
                    return;
                }

                if (currentMessagesHash === nextHash && thread.querySelector('[data-message-id]')) {
                    return;
                }

                thread.innerHTML = messages.map((message) => {
                    if (message.sender_type === 'system' && !message.products?.length) {
                        return `
                            <div data-message-id="${message.id}" class="mb-5 flex justify-center">
                                <div class="max-w-xl rounded-full bg-[#FCEDEF] px-4 py-2 text-center text-xs font-medium text-[#8C6770]">
                                    ${escapeHtml(message.body ?? '')}
                                </div>
                            </div>
                        `;
                    }

                    const wrapper = message.is_mine ? 'justify-end' : 'justify-start';
                    const bubble = message.is_mine ? 'bg-[#5A3A3A] text-white' : 'border border-[#F0DCE1] bg-white text-[#5A3A3A]';
                    const meta = message.is_mine ? 'text-white/70' : 'text-[#A07C84]';
                    const radius = message.is_mine ? 'rounded-[1.8rem_1.8rem_0.5rem_1.8rem]' : 'rounded-[1.8rem_1.8rem_1.8rem_0.5rem]';

                    return `
                        <div data-message-id="${message.id}" class="mb-4 flex ${wrapper}">
                            <div class="max-w-[90%]">
                                <div class="${bubble} ${radius} px-4 py-3 shadow-sm">
                                    ${message.body ? `<div class="whitespace-pre-wrap text-sm leading-7">${escapeHtml(message.body)}</div>` : ''}
                                    ${message.sender_type === 'assistant' ? renderAiProductCards(message.products || []) : ''}
                                    ${formatAttachment(message)}
                                </div>
                                <div class="mt-1 flex ${message.is_mine ? 'justify-end' : 'justify-start'} gap-2 px-1 text-[11px] ${meta}">
                                    <span>${escapeHtml(message.timestamp)}</span>
                                    ${message.is_mine ? `<span>${escapeHtml(message.receipt_label)}</span>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

                currentMessagesHash = nextHash;

                if (shouldStick) {
                    scrollToBottom();
                }
            };

            const renderConversation = (conversation) => {
                currentConversation = conversation;

                 if (currentMode === 'ai') {
                    return;
                }

                if (!conversation) {
                    statusDot.className = 'absolute bottom-0 right-0 h-3.5 w-3.5 rounded-full border-2 border-white bg-slate-300';
                    statusText.textContent = 'Offline';
                    typingIndicator.classList.add('hidden');
                    resetConversationSubscription();
                    return;
                }

                const supportOnline = conversation.admin_is_online;
                statusDot.className = `absolute bottom-0 right-0 h-3.5 w-3.5 rounded-full border-2 border-white ${supportOnline ? 'bg-emerald-500' : 'bg-slate-300'}`;
                statusText.textContent = supportOnline ? 'Online now' : 'Offline';
                typingIndicator.classList.toggle('hidden', !conversation.admin_is_typing);
            };

            const sync = async (resubscribe = true) => {
                if (currentMode === 'ai') {
                    renderCurrentMode();
                    return;
                }

                try {
                    const response = await fetch(routes.session, { headers: buildHeaders() });
                    const payload = await response.json();
                    renderConversation(payload.conversation);
                    renderMessages(payload.messages);

                    if (resubscribe && payload.conversation?.broadcast_channel) {
                        subscribeToConversation(payload.conversation.broadcast_channel);
                    }
                } catch (error) {
                    statusDot.className = 'absolute bottom-0 right-0 h-3.5 w-3.5 rounded-full border-2 border-white bg-amber-400';
                }
            };

            const sendTyping = async (isTyping) => {
                if (currentMode === 'ai') {
                    return;
                }

                if (typingState === isTyping) {
                    return;
                }

                typingState = isTyping;
                await fetch(routes.typing, {
                    method: 'POST',
                    headers: buildHeaders(true),
                    body: JSON.stringify({ is_typing: isTyping }),
                });
            };

            sendForm.addEventListener('submit', async (event) => {
                event.preventDefault();
                const body = messageInput.value.trim();

                if (currentMode === 'ai') {
                    if (!body) {
                        uploadName.textContent = 'Write a message before sending.';
                        return;
                    }

                    const customerMessage = buildAiMessage({
                        senderType: 'customer',
                        body,
                        receiptLabel: 'Seen',
                    });

                    aiMessages = [...aiMessages, customerMessage];
                    saveAiMessages();
                    renderMessages(aiMessages);
                    messageInput.value = '';
                    messageInput.style.height = 'auto';
                    uploadName.textContent = 'AI chat is text-only. Switch to Live Chat to send files.';
                    typingIndicator.textContent = 'Bonbon AI is thinking...';
                    typingIndicator.classList.remove('hidden');
                    scrollToBottom();

                    const history = aiMessages.slice(-8).map((message) => ({
                        role: message.is_mine ? 'user' : 'assistant',
                        content: message.body ?? '',
                    }));

                    try {
                        const response = await fetch(routes.aiSend, {
                            method: 'POST',
                            headers: buildHeaders(true),
                            body: JSON.stringify({
                                body,
                                history,
                            }),
                        });

                        if (!response.ok) {
                            throw new Error('Unable to reach Bonbon AI.');
                        }

                        const payload = await response.json();
                        const aiReply = buildAiMessage({
                            senderType: 'assistant',
                            body: payload.message?.body ?? 'Bonbon AI could not answer that just now.',
                            receiptLabel: '',
                            products: payload.message?.products ?? [],
                        });

                        typingIndicator.classList.add('hidden');
                        typingIndicator.textContent = 'Bonbon Support is typing...';
                        aiMessages = [...aiMessages, aiReply];
                        saveAiMessages();
                        renderMessages(aiMessages);
                        scrollToBottom();
                    } catch (error) {
                        typingIndicator.classList.add('hidden');
                        typingIndicator.textContent = 'Bonbon Support is typing...';
                        uploadName.textContent = 'Bonbon AI is unavailable right now. Please try again or switch to Live Chat.';
                    }

                    return;
                }

                const formData = new FormData(sendForm);

                try {
                    const response = await fetch(routes.send, {
                        method: 'POST',
                        headers: buildHeaders(),
                        body: formData,
                    });

                    if (!response.ok) {
                        throw new Error('Unable to send message.');
                    }

                    const payload = await response.json();

                    messageInput.value = '';
                    attachmentInput.value = '';
                    renderComposerAttachment(null);
                    messageInput.style.height = 'auto';
                    typingState = false;
                    void sendTyping(false);
                    renderConversation(payload.conversation);
                    if (payload.conversation?.broadcast_channel) {
                        subscribeToConversation(payload.conversation.broadcast_channel);
                    }
                    renderMessages([...currentMessages, payload.message]);
                    scrollToBottom();
                } catch (error) {
                    uploadName.textContent = 'Message failed to send. Please try again.';
                }
            });

            attachmentInput.addEventListener('change', () => {
                renderComposerAttachment(attachmentInput.files[0] ?? null);
            });

            attachmentClear.addEventListener('click', () => {
                attachmentInput.value = '';
                renderComposerAttachment(null);
            });

            document.querySelectorAll('.bonbon-quick-prompt').forEach((button) => {
                button.addEventListener('click', () => {
                    openPanel();
                    if (currentMode === 'live') {
                        renderCurrentMode();
                    }
                    messageInput.value = button.dataset.prompt ?? '';
                    messageInput.focus();
                    messageInput.dispatchEvent(new Event('input'));
                });
            });

            messageInput.addEventListener('input', () => {
                messageInput.style.height = 'auto';
                messageInput.style.height = `${Math.min(messageInput.scrollHeight, 160)}px`;

                if (currentMode === 'live') {
                    void sendTyping(messageInput.value.trim().length > 0);
                }

                window.clearTimeout(typingTimer);
                typingTimer = window.setTimeout(() => {
                    if (currentMode === 'live') {
                        void sendTyping(false);
                    }
                }, 1800);
            });

            messageInput.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();
                    sendForm.requestSubmit();
                }
            });

            const resize = (clientX) => {
                const min = 340;
                const max = Math.min(window.innerWidth * 0.7, 760);
                const width = Math.max(min, Math.min(window.innerWidth - clientX, max));
                document.documentElement.style.setProperty('--chat-panel-width', `${width}px`);
            };

            resizer.addEventListener('mousedown', (event) => {
                if (window.innerWidth < 1024) {
                    return;
                }

                isResizing = true;
                document.body.style.userSelect = 'none';
                resize(event.clientX);
            });

            window.addEventListener('mousemove', (event) => {
                if (!isResizing) {
                    return;
                }

                resize(event.clientX);
            });

            window.addEventListener('mouseup', () => {
                if (!isResizing) {
                    return;
                }

                isResizing = false;
                document.body.style.userSelect = '';
            });

            openButton.addEventListener('click', openPanel);
            closeButton.addEventListener('click', closePanel);
            backdrop.addEventListener('click', closePanel);
            window.addEventListener('bonbon-chat:open', openPanel);

            [liveModeButton, aiModeButton].forEach((button) => {
                button?.addEventListener('click', () => {
                    const nextMode = button.dataset.chatMode === 'ai' ? 'ai' : 'live';
                    if (currentMode === nextMode) {
                        return;
                    }

                    currentMode = nextMode;
                    window.sessionStorage.setItem('bonbon-chat-mode', currentMode);
                    window.clearTimeout(aiReplyTimer);
                    typingIndicator.classList.add('hidden');
                    typingIndicator.textContent = 'Bonbon Support is typing...';
                    renderCurrentMode();
                });
            });

            window.addEventListener('beforeunload', () => {
                if (currentMode === 'live') {
                    fetch(routes.typing, {
                        method: 'POST',
                        headers: buildHeaders(true),
                        body: JSON.stringify({ is_typing: false }),
                        keepalive: true,
                    });
                }
            });

            loadAiMessages();
            renderModeButtons();

            @if (request()->routeIs('assistant'))
                openPanel();
                hasLoaded = true;
            @endif
        })();
    </script>
    @endunless
</body>
</html>
