@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl">
        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif
        <div class="overflow-hidden rounded-2xl bg-white shadow-md">
            <div class="border-b border-gray-100 px-6 py-6">
                <div class="flex flex-col gap-5">
                    <div class="flex items-center justify-between gap-4">
                        <h1 class="text-3xl font-bold">Notifications</h1>
                           
                            <div class="inline-flex w-full max-w-[30rem] rounded-[0.8rem] bg-gray-100 p-1">
                                <a href="{{ route('notifications.index') }}"
                                class="flex min-w-0 flex-1 items-center justify-center gap-1.5 rounded-[0.8rem] px-3 py-2 text-xs font-semibold transition {{ ($filter ?? '') === '' ? 'bg-white text-[#2E2E2E]' : 'text-[#5A6278] hover:text-[#2E2E2E]' }}">
                                    <span>All</span>
                                    <span class="inline-flex h-4 w-4 items-center justify-center rounded-full text-[10px] {{ ($filter ?? '') === '' ? 'bg-gray-100 text-[#5A3A3A]' : 'bg-white text-[#8C6770]' }}">
                                        {{ $counts['all'] }}
                                    </span>
                                </a>

                                <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
                                class="flex min-w-0 flex-1 items-center justify-center gap-1.5 rounded-[0.8rem] px-3 py-2 text-xs font-semibold transition {{ ($filter ?? '') === 'unread' ? 'bg-white text-[#2E2E2E]' : 'text-[#5A6278] hover:text-[#2E2E2E]' }}">
                                    <span>Unread Messages</span>
                                    <span class="inline-flex h-4 w-4 items-center justify-center rounded-full text-[10px] {{ ($filter ?? '') === 'unread' ? 'bg-gray-100 text-[#5A3A3A]' : 'bg-white text-[#8C6770]' }}">
                                        {{ $counts['unread'] }}
                                    </span>
                                </a>

                                <a href="{{ route('notifications.index', ['filter' => 'archived']) }}"
                                class="flex min-w-0 flex-1 items-center justify-center gap-1.5 rounded-[0.8rem] px-3 py-2 text-xs font-semibold transition {{ ($filter ?? '') === 'archived' ? 'bg-white text-[#2E2E2E]' : 'text-[#5A6278] hover:text-[#2E2E2E]' }}">
                                    <span>Archive</span>
                                    <span class="inline-flex h-4 w-4 items-center justify-center rounded-full text-[10px] {{ ($filter ?? '') === 'archived' ? 'bg-gray-100 text-[#5A3A3A]' : 'bg-white text-[#8C6770]' }}">
                                        {{ $counts['archived'] }}
                                    </span>
                                </a>
                           </div>
                    </div>

                </div>
            </div>

            @forelse ($notifications as $notification)
                <div class="border-b border-gray-100 px-6 py-5 transition hover:bg-pink-50 {{ $notification->read_at ? 'bg-white' : 'bg-[#FFF8FA]' }}">
                    <div class="flex items-start justify-between gap-4">
                        <a href="{{ route('notifications.open', $notification) }}" class="min-w-0 flex-1">
                            <div class="flex items-center gap-3">
                                <h2 class="text-base font-semibold text-[#5A3A3A]">{{ $notification->title }}</h2>
                                @if (! $notification->read_at)
                                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-pink-500"></span>
                                @endif
                            </div>
                            <p class="mt-2 text-sm text-gray-600">{{ $notification->body }}</p>
                            <p class="mt-3 text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                        </a>

                        <div class="relative shrink-0" data-notification-item-menu>
                            <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-[#E7C7CF] bg-white text-[#B77B85] transition hover:bg-[#FFF4F6]" data-notification-item-toggle aria-label="Notification options">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a1.5 1.5 0 110-3 1.5 1.5 0 010 3Zm0 5.5A1.5 1.5 0 1110 8a1.5 1.5 0 010 3.5Zm0 5.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3Z"></path>
                                </svg>
                            </button>

                            <div class="absolute right-0 z-10 mt-2 hidden min-w-[10rem] rounded-xl border border-[#F0E2E5] bg-white p-2 shadow-lg" data-notification-item-dropdown>
                                @if ($notification->archived_at)
                                    <form method="POST" action="{{ route('notifications.restore', $notification) }}">
                                        @csrf
                                        <button type="submit" class="block w-full rounded-lg px-4 py-2 text-left text-sm font-medium text-[#5A3A3A] transition hover:bg-pink-50 hover:text-[#C94F7C]">Restore</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('notifications.archive', $notification) }}">
                                        @csrf
                                        <button type="submit" class="block w-full rounded-lg px-4 py-2 text-left text-sm font-medium text-[#5A3A3A] transition hover:bg-pink-50 hover:text-[#C94F7C]">Archive</button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('notifications.destroy', $notification) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="block w-full rounded-lg px-4 py-2 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50 hover:text-rose-700">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-600">
                    No notifications yet.
                </div>
            @endforelse
        </div>

        @if ($notifications->hasPages())
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

    <script>
        document.querySelectorAll('[data-notification-item-menu]').forEach((menu) => {
            const toggle = menu.querySelector('[data-notification-item-toggle]');
            const dropdown = menu.querySelector('[data-notification-item-dropdown]');

            if (!toggle || !dropdown) {
                return;
            }

            toggle.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                const isOpen = !dropdown.classList.contains('hidden');

                document.querySelectorAll('[data-notification-item-dropdown]').forEach((item) => {
                    item.classList.add('hidden');
                });

                dropdown.classList.toggle('hidden', isOpen);
            });
        });

        document.addEventListener('click', (event) => {
            document.querySelectorAll('[data-notification-item-menu]').forEach((menu) => {
                if (!menu.contains(event.target)) {
                    menu.querySelector('[data-notification-item-dropdown]')?.classList.add('hidden');
                }
            });
        });
    </script>
@endsection
