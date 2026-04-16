<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Bonbon Ecom') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=great-vibes:400|instrument-sans:400,500,600" rel="stylesheet" />
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
            background-color: var(--border-soft);
            color: var(--text-dark);
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
    </style>
    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-[#F5F5F5] text-[#2E2E2E]">
    @include('components.navbar')

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>

    @include('components.footer')

    <!-- Chat Support -->
    <div id="chat-button" class="fixed bottom-4 right-4 bg-pink-600 text-white p-4 rounded-full shadow-lg cursor-pointer">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
    </div>

    <!-- Chat Window (hidden by default) -->
    <div id="chat-window" class="fixed bottom-16 right-4 w-80 h-96 bg-white border border-gray-300 rounded-lg shadow-lg hidden">
        <div class="bg-pink-600 text-white p-4 rounded-t-lg">
            <h3 class="font-semibold">Chat Support</h3>
        </div>
        <div id="chat-messages" class="p-4 h-64 overflow-y-auto">
            <!-- Messages will be added here -->
        </div>
        <div class="p-4 border-t">
            <div class="flex">
                <input id="chat-input" type="text" placeholder="Type your message..." class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                <button id="send-button" class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-r-lg">Send</button>
            </div>
        </div>
    </div>

    <script>
        // Simple chat functionality
        const chatButton = document.getElementById('chat-button');
        const chatWindow = document.getElementById('chat-window');
        const chatInput = document.getElementById('chat-input');
        const sendButton = document.getElementById('send-button');
        const chatMessages = document.getElementById('chat-messages');

        chatButton.addEventListener('click', () => {
            chatWindow.classList.toggle('hidden');
        });

        sendButton.addEventListener('click', () => {
            const message = chatInput.value.trim();
            if (message) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'mb-2';
                messageDiv.innerHTML = `<div class="bg-gray-200 p-2 rounded-lg">${message}</div>`;
                chatMessages.appendChild(messageDiv);
                chatInput.value = '';
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        });

        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendButton.click();
            }
        });
    </script>
</body>
</html>
