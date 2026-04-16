@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-5xl">
        <div class="rounded-[2rem] border border-[#F3D7DD] bg-gradient-to-br from-white via-[#FFF8F9] to-[#FDF0F3] p-8 shadow-[0_20px_45px_rgba(90,58,58,0.12)] md:p-12">
            <div class="text-center">
                <div class="mx-auto flex h-28 w-28 items-center justify-center rounded-full bg-[#FDECEF] shadow-[0_12px_25px_rgba(90,58,58,0.12)]">
                    <span class="text-6xl">🍰</span>
                </div>
                <p class="mt-6 text-sm font-semibold uppercase tracking-[0.3em] text-pink-600">Bonbon Assistant</p>
                <h1 class="mt-3 text-4xl font-bold text-[#5A3A3A] md:text-5xl">Ask me anything about Bonbon</h1>
                <p class="mx-auto mt-4 max-w-2xl text-base leading-relaxed text-[#7A5252] md:text-lg">
                    Get help with best sellers, menu choices, promos, and where to find us. Start with a quick prompt or type your own question below.
                </p>
            </div>

            <div class="mt-10 flex flex-wrap justify-center gap-3">
                <button type="button" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-[#5A3A3A] shadow-sm ring-1 ring-[#F0D5DB] transition hover:-translate-y-0.5 hover:bg-[#FFF0F3]">Best Sellers</button>
                <button type="button" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-[#5A3A3A] shadow-sm ring-1 ring-[#F0D5DB] transition hover:-translate-y-0.5 hover:bg-[#FFF0F3]">Location</button>
                <button type="button" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-[#5A3A3A] shadow-sm ring-1 ring-[#F0D5DB] transition hover:-translate-y-0.5 hover:bg-[#FFF0F3]">Menu</button>
                <button type="button" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-[#5A3A3A] shadow-sm ring-1 ring-[#F0D5DB] transition hover:-translate-y-0.5 hover:bg-[#FFF0F3]">Promos</button>
            </div>

            <div class="mx-auto mt-8 flex max-w-3xl items-center gap-3 rounded-full border border-[#F0D5DB] bg-white px-4 py-3 shadow-[0_14px_28px_rgba(90,58,58,0.08)]">
                <input
                    type="text"
                    placeholder="Ask something..."
                    class="flex-1 bg-transparent px-2 text-[#5A3A3A] outline-none placeholder:text-[#A5848B]"
                >
                <button type="button" class="rounded-full p-2 text-[#7A5252] transition hover:bg-[#FFF0F3]">➕</button>
            </div>
        </div>

    </section>
@endsection
