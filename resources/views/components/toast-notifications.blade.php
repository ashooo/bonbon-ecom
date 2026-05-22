@props(['toasts' => []])

@php
    $normalizedToasts = collect($toasts)
        ->filter(fn ($toast) => filled($toast['message'] ?? null))
        ->values()
        ->all();
@endphp

@if (! empty($normalizedToasts))
    <div id="bonbon-toast-stack" class="fixed right-4 top-4 z-[10050] flex w-[min(92vw,24rem)] flex-col gap-3 sm:right-6 sm:top-6">
        @foreach ($normalizedToasts as $index => $toast)
            @php
                $type = $toast['type'] ?? 'info';
                $styles = match ($type) {
                    'success' => 'border-[#CFE8DA] bg-[#F2FBF6] text-[#1F5A3F]',
                    'error' => 'border-[#F0C7CF] bg-[#FFF5F7] text-[#7A303F]',
                    'warning' => 'border-[#EED9DE] bg-[#FFF8FA] text-[#5A3A3A]',
                    default => 'border-[#EED9DE] bg-white text-[#5A3A3A]',
                };
            @endphp
            <div
                class="bonbon-toast pointer-events-auto rounded-2xl border px-4 py-3 shadow-[0_14px_30px_rgba(90,58,58,0.14)] transition-all duration-300 {{ $styles }}"
                data-toast
                data-toast-index="{{ $index }}"
                role="status"
                aria-live="polite"
            >
                <div class="flex items-start gap-3">
                    <div class="min-w-0 flex-1 text-sm font-medium leading-6">{{ $toast['message'] }}</div>
                    <button type="button" class="rounded-full p-1.5 text-current/70 transition hover:bg-white/60 hover:text-current" data-toast-close aria-label="Dismiss notification">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6l-12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif

<script>
    (() => {
        const stack = document.getElementById('bonbon-toast-stack');

        const removeToast = (toast) => {
            if (!toast) return;
            toast.classList.add('translate-y-1', 'opacity-0');
            setTimeout(() => {
                toast.remove();
                if (stack && !stack.querySelector('[data-toast]')) {
                    stack.remove();
                }
            }, 220);
        };

        const attachToastHandlers = (toast) => {
            const close = toast.querySelector('[data-toast-close]');
            close?.addEventListener('click', () => removeToast(toast));
            window.setTimeout(() => removeToast(toast), 4600);
        };

        stack?.querySelectorAll('[data-toast]').forEach(attachToastHandlers);

        window.BonbonNotify = (type, message) => {
            if (!message) return;
            const host = document.getElementById('bonbon-toast-stack') ?? (() => {
                const next = document.createElement('div');
                next.id = 'bonbon-toast-stack';
                next.className = 'fixed right-4 top-4 z-[10050] flex w-[min(92vw,24rem)] flex-col gap-3 sm:right-6 sm:top-6';
                document.body.appendChild(next);
                return next;
            })();

            const palette = {
                success: 'border-[#CFE8DA] bg-[#F2FBF6] text-[#1F5A3F]',
                error: 'border-[#F0C7CF] bg-[#FFF5F7] text-[#7A303F]',
                warning: 'border-[#EED9DE] bg-[#FFF8FA] text-[#5A3A3A]',
                info: 'border-[#EED9DE] bg-white text-[#5A3A3A]',
            };

            const toast = document.createElement('div');
            toast.className = `bonbon-toast pointer-events-auto rounded-2xl border px-4 py-3 shadow-[0_14px_30px_rgba(90,58,58,0.14)] transition-all duration-300 ${palette[type] ?? palette.info}`;
            toast.setAttribute('role', 'status');
            toast.setAttribute('aria-live', 'polite');
            toast.dataset.toast = 'dynamic';
            toast.innerHTML = `
                <div class="flex items-start gap-3">
                    <div class="min-w-0 flex-1 text-sm font-medium leading-6"></div>
                    <button type="button" class="rounded-full p-1.5 text-current/70 transition hover:bg-white/60 hover:text-current" data-toast-close aria-label="Dismiss notification">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6l-12 12" />
                        </svg>
                    </button>
                </div>
            `;
            toast.querySelector('div > div').textContent = message;
            host.appendChild(toast);
            attachToastHandlers(toast);
        };
    })();
</script>
