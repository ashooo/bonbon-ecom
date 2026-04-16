@extends('layouts.app')

@section('content')
    <section class="mb-12 -mt-2 relative z-10">
        <div class="relative w-full">
            @if($storeSettings?->hero_image_url)
                <img
                    src="{{ $storeSettings->hero_image_url }}"
                    alt="Homepage hero"
                    class="block w-full max-h-[180px] rounded-3xl object-contain object-center drop-shadow-[0_24px_40px_rgba(90,58,58,0.22)] md:max-h-[220px]"
                >
            @else
                <div class="flex h-[180px] items-center justify-center rounded-3xl border border-dashed border-[#E6B7BE] bg-white text-slate-400 shadow-[0_24px_40px_rgba(90,58,58,0.12)] md:h-[220px]">
                    Upload a homepage hero image from Admin Settings
                </div>
            @endif
        </div>
    </section>

    <section class="mb-15">
        <div class="flex flex-wrap justify-center gap-6 md:gap-8">
            @forelse($featuredCategories as $category)
                <a href="{{ url('/products?category=' . $category->id) }}" class="flex flex-col items-center text-center transition duration-300 hover:-translate-y-1">
                    <div class="h-28 w-28 rounded-full border-4 border-[#F5E6E8] bg-white p-1 shadow-md transition duration-300 hover:shadow-lg">
                        <div class="flex h-full w-full items-center justify-center overflow-hidden rounded-full bg-[#F5E6E8]">
                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="h-full w-full object-cover">
                            @else
                                <svg class="w-10 h-10 text-[#5A3A3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            @endif
                        </div>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-[#5A3A3A]">{{ $category->name }}</h3>
                </a>
            @empty
                <div class="w-full rounded-lg border border-dashed border-[#E6B7BE] bg-white p-10 text-center text-gray-500">
                    No active categories available yet.
                </div>
            @endforelse
        </div>
    </section>

    <section class="mb-12">
        <h2 class="text-3xl font-bold text-center mb-8">Featured Products</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            @forelse($featuredProducts as $product)
                @include('components.product-card', ['product' => $product])
            @empty
                <div class="md:col-span-2 xl:col-span-3 rounded-lg border border-dashed border-[#E6B7BE] bg-white p-10 text-center text-gray-500">
                    No featured products available yet.
                </div>
            @endforelse
        </div>
    </section>


@endsection
