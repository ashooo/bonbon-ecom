@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-4xl">
        <div class="rounded-[2rem] border border-[#F1DADF] bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.98),_rgba(255,245,247,0.95)_55%,_rgba(248,225,230,0.92))] px-8 py-12 text-center shadow-[0_18px_45px_rgba(90,58,58,0.12)]">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#C88A92]">Bonbon Chat</p>
            <h1 class="mt-4 text-4xl font-semibold text-[#5A3A3A]">Chat with Bonbon without leaving the page.</h1>
            <p class="mx-auto mt-4 max-w-2xl text-base leading-8 text-[#7A5252]">
                Ask about delivery, flavors, custom cakes, or upload pegs and inspiration photos. Use the chat panel beside the page to message support in real time.
            </p>
            <button
                type="button"
                id="assistant-open-panel"
                class="mt-8 inline-flex items-center gap-3 rounded-full bg-[#5A3A3A] px-6 py-4 text-sm font-semibold text-white shadow-[0_18px_35px_rgba(90,58,58,0.18)] transition hover:-translate-y-0.5 hover:bg-[#7A5252]"
            >
                Open Bonbon Chat
            </button>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.dispatchEvent(new CustomEvent('bonbon-chat:open'));
        document.getElementById('assistant-open-panel')?.addEventListener('click', () => {
            window.dispatchEvent(new CustomEvent('bonbon-chat:open'));
        });
    });
</script>
@endpush
