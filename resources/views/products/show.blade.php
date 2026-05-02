@extends('layouts.app')

@section('content')
    @php
        $galleryImages = collect([$product->main_image_url])
            ->merge($product->images->map->image_url)
            ->filter()
            ->unique()
            ->values();
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <div>
            <div class="mb-4 overflow-hidden rounded-3xl bg-white shadow-lg">
                <img
                    src="{{ $galleryImages->first() ?: 'https://via.placeholder.com/600x400?text=' . urlencode($product->name) }}"
                    alt="{{ $product->name }}"
                    class="h-[420px] w-full object-cover"
                >
            </div>

            @if($galleryImages->count() > 1)
                <div class="grid grid-cols-4 gap-3">
                    @foreach($galleryImages as $image)
                        <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
                            <img src="{{ $image }}" alt="{{ $product->name }}" class="h-20 w-full object-cover">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <div class="mb-4 flex items-center gap-3">
                <span class="rounded-full bg-[#F5E6E8] px-4 py-2 text-sm font-medium text-[#5A3A3A]">
                    {{ $product->category?->name ?? 'Uncategorized' }}
                </span>

                @if($product->is_best_seller)
                    <span class="rounded-full bg-[#C94F7C] px-4 py-2 text-sm font-semibold uppercase tracking-[0.18em] text-white">
                        Best Seller
                    </span>
                @endif

                @if($product->status === 'pre_order')
                    <span class="rounded-full bg-amber-400 px-4 py-2 text-sm font-semibold uppercase tracking-[0.18em] text-[#5A3A3A]">
                        Pre-order{{ ($product->pre_order_days ?? 0) > 0 ? ' • ' . $product->pre_order_days . ' days' : '' }}
                    </span>
                @endif
            </div>

            <h1 class="text-4xl font-bold mb-4">{{ $product->name }}</h1>

            <div class="mb-6 flex items-baseline gap-3">
                <p class="text-3xl font-bold text-[#5A3A3A]">&#8369;{{ number_format($product->effective_price, 2) }}</p>
                @if($product->hasDiscount())
                    <p class="text-lg text-gray-400 line-through">&#8369;{{ number_format($product->price, 2) }}</p>
                @endif
            </div>

            <div class="mb-6 rounded-3xl bg-white p-6 shadow-sm">
                <h2 class="mb-3 text-xl font-semibold">Description</h2>
                <p class="leading-relaxed text-gray-700">
                    {{ $product->description ?: 'No description available for this product yet.' }}
                </p>
            </div>

            <div class="mb-6 grid grid-cols-2 gap-4">
                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <p class="text-sm uppercase tracking-[0.18em] text-gray-500">Stock</p>
                    <p class="mt-2 text-2xl font-semibold {{ $product->stock_quantity > 0 || $product->status === 'pre_order' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $product->status === 'pre_order' ? ($product->pre_order_days ? $product->pre_order_days . ' day lead time' : 'Available for pre-order') : ($product->stock_quantity > 0 ? $product->stock_quantity . ' available' : 'Out of stock') }}
                    </p>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <p class="text-sm uppercase tracking-[0.18em] text-gray-500">Status</p>
                    <p class="mt-2 text-2xl font-semibold text-[#5A3A3A]">
                        {{ str_replace('_', ' ', ucfirst($product->status)) }}
                    </p>
                </div>
            </div>

            <form action="{{ route('cart.add') }}" method="POST" class="rounded-3xl bg-white p-6 shadow-sm">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div class="mb-4">
                    <label for="quantity" class="mb-2 block text-sm font-medium text-gray-700">Quantity</label>
                    <input
                        id="quantity"
                        name="quantity"
                        type="number"
                        min="1"
                        value="1"
                        class="w-28 rounded-xl border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-500"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full rounded-2xl bg-pink-600 px-6 py-3 text-lg font-bold text-white transition duration-300 hover:bg-pink-700"
                >
                    Add to Cart
                </button>
            </form>
        </div>
    </div>

    @if($relatedProducts->isNotEmpty())
        <section class="mt-12">
            <h2 class="mb-6 text-2xl font-bold">You Might Also Like</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                    <a href="{{ route('products.show', $relatedProduct->slug) }}" class="overflow-hidden rounded-3xl bg-white shadow-md transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                        <img
                            src="{{ $relatedProduct->main_image_url ?: 'https://via.placeholder.com/300x200?text=' . urlencode($relatedProduct->name) }}"
                            alt="{{ $relatedProduct->name }}"
                            class="h-48 w-full object-cover"
                        >
                        <div class="p-5">
                            <h3 class="text-lg font-semibold">{{ $relatedProduct->name }}</h3>
                            @if($relatedProduct->status === 'pre_order' && ($relatedProduct->pre_order_days ?? 0) > 0)
                                <p class="mt-1 text-sm font-medium text-amber-700">
                                    Pre-order: {{ $relatedProduct->pre_order_days }} day lead time
                                </p>
                            @endif
                            <p class="mt-2 text-[#5A3A3A] font-bold">&#8369;{{ number_format($relatedProduct->effective_price, 2) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
@endsection
