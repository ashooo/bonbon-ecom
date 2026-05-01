@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-16 space-y-16">
    <h1 class="text-3xl font-bold text-[#5A3A3A] text-center">🎂 Cake Loader Preview</h1>

    {{-- Small --}}
    <div class="text-center space-y-4">
        <h2 class="text-sm font-black text-[#C88A92] uppercase tracking-[0.2em]">Small</h2>
        <div class="bg-white rounded-[2rem] border border-[#F5E6E8] p-8 flex items-center justify-center">
            <x-cake-loader size="sm" />
        </div>
    </div>

    {{-- Medium (default) --}}
    <div class="text-center space-y-4">
        <h2 class="text-sm font-black text-[#C88A92] uppercase tracking-[0.2em]">Medium (Default)</h2>
        <div class="bg-white rounded-[2rem] border border-[#F5E6E8] p-12 flex items-center justify-center">
            <x-cake-loader text="Loading your treats..." />
        </div>
    </div>

    {{-- Large --}}
    <div class="text-center space-y-4">
        <h2 class="text-sm font-black text-[#C88A92] uppercase tracking-[0.2em]">Large</h2>
        <div class="bg-white rounded-[2rem] border border-[#F5E6E8] p-16 flex items-center justify-center">
            <x-cake-loader size="lg" text="Preparing your order..." />
        </div>
    </div>

    {{-- Dark background example --}}
    <div class="text-center space-y-4">
        <h2 class="text-sm font-black text-[#C88A92] uppercase tracking-[0.2em]">On Dark Background</h2>
        <div class="bg-[#5A3A3A] rounded-[2rem] p-12 flex items-center justify-center">
            <x-cake-loader text="Almost there..." />
        </div>
    </div>
</div>
@endsection
