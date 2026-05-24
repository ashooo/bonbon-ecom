@props(['toasts' => []])

@php
    $normalizedToasts = collect($toasts)
        ->filter(fn ($toast) => filled($toast['message'] ?? null))
        ->values()
        ->all();
@endphp

@if (! empty($normalizedToasts))
    <div id="bonbon-toast-stack" class="fixed right-4 top-20 z-[10050] flex w-[min(92vw,24rem)] flex-col gap-3 sm:right-6 sm:top-24">
        @foreach ($normalizedToasts as $index => $toast)
            @php
                $type = $toast['type'] ?? 'info';
                $styles = 'border-[#f3bfd8] bg-[#fff6fb] text-[#7a2f56]';
                $barColor = 'bg-[#ec4899]';
            @endphp
            <div
                class="bonbon-toast pointer-events-auto overflow-hidden rounded-xl border px-4 py-3 shadow-sm transition-all duration-200 {{ $styles }}"
                data-toast
                data-toast-index="{{ $index }}"
                data-toast-type="{{ $type }}"
                role="status"
                aria-live="polite"
            >
                <div class="flex items-start gap-2">
                    <div class="min-w-0 flex-1 text-sm font-medium leading-6">{{ $toast['message'] }}</div>
                    <button type="button" class="rounded-md p-1 text-current/70 transition hover:bg-[#f8e5f0] hover:text-current" data-toast-close aria-label="Dismiss notification">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6l-12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-2 h-0.5 overflow-hidden rounded-full bg-[#f5d3e4]">
                    <div class="bonbon-toast-progress h-full origin-left {{ $barColor }}"></div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<script>
    (() => {
        const stack = document.getElementById('bonbon-toast-stack');
        const TOAST_DURATION = 4600;

        const removeToast = (toast) => {
            if (!toast) return;
            toast.classList.remove('translate-y-0', 'opacity-100', 'scale-100');
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
            const progress = toast.querySelector('.bonbon-toast-progress');
            if (progress) {
                requestAnimationFrame(() => {
                    progress.style.transition = `transform ${TOAST_DURATION}ms linear`;
                    progress.style.transform = 'scaleX(0)';
                });
            }
            window.setTimeout(() => removeToast(toast), TOAST_DURATION);
        };

        stack?.querySelectorAll('[data-toast]').forEach((toast) => {
            toast.classList.add('translate-y-2', 'opacity-0');
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
            });
            attachToastHandlers(toast);
        });

        window.BonbonNotify = (type, message) => {
            if (!message) return;
            const host = document.getElementById('bonbon-toast-stack') ?? (() => {
                const next = document.createElement('div');
                next.id = 'bonbon-toast-stack';
                next.className = 'fixed right-4 top-20 z-[10050] flex w-[min(92vw,24rem)] flex-col gap-3 sm:right-6 sm:top-24';
                document.body.appendChild(next);
                return next;
            })();

            const toneMap = {
                success: { shell: 'border-[#f3bfd8] bg-[#fff6fb] text-[#7a2f56]', bar: 'bg-[#ec4899]' },
                error: { shell: 'border-[#f3bfd8] bg-[#fff6fb] text-[#7a2f56]', bar: 'bg-[#ec4899]' },
                warning: { shell: 'border-[#f3bfd8] bg-[#fff6fb] text-[#7a2f56]', bar: 'bg-[#ec4899]' },
                info: { shell: 'border-[#f3bfd8] bg-[#fff6fb] text-[#7a2f56]', bar: 'bg-[#ec4899]' },
            };
            const tone = toneMap[type] ?? toneMap.info;

            const toast = document.createElement('div');
            toast.className = `bonbon-toast pointer-events-auto overflow-hidden rounded-xl border px-4 py-3 shadow-sm transition-all duration-200 ${tone.shell}`;
            toast.setAttribute('role', 'status');
            toast.setAttribute('aria-live', 'polite');
            toast.dataset.toast = 'dynamic';
            toast.innerHTML = `
                <div class="flex items-start gap-2">
                    <div class="min-w-0 flex-1 text-sm font-medium leading-6"></div>
                    <button type="button" class="rounded-md p-1 text-current/70 transition hover:bg-[#f8e5f0] hover:text-current" data-toast-close aria-label="Dismiss notification">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6l-12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-2 h-0.5 overflow-hidden rounded-full bg-[#f5d3e4]">
                    <div class="bonbon-toast-progress h-full origin-left ${tone.bar}"></div>
                </div>
            `;
            toast.querySelector('.min-w-0.flex-1').textContent = message;
            toast.classList.add('translate-y-2', 'opacity-0');
            host.appendChild(toast);
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
            });
            attachToastHandlers(toast);
        };
    })();
</script>
