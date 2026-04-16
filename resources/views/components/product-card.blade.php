<a href="{{ route('products.show', $product->slug) }}" class="block overflow-hidden rounded-lg border border-[#F5F5F5] bg-white shadow-md transition duration-300 hover:-translate-y-1 hover:shadow-lg">
    <div class="relative">
        <img
            src="{{ $product->main_image_url ?: 'https://via.placeholder.com/300x200?text=' . urlencode($product->name) }}"
            alt="{{ $product->name }}"
            class="h-48 w-full object-cover"
        >

        @if($product->is_best_seller)
            <span class="absolute left-3 top-3 rounded-full bg-[#C94F7C] px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-white">
                Best Seller
            </span>
        @endif

        @if($product->status === 'pre_order')
            <span class="absolute right-3 top-3 rounded-full bg-amber-400 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-[#5A3A3A]">
                Pre-order
            </span>
        @endif
    </div>

    <div class="p-6">
        <h3 class="mb-2 text-xl font-semibold">{{ $product->name }}</h3>
        <p class="mb-1 text-gray-600">{{ $product->category?->name ?? 'Uncategorized' }}</p>
        <p class="mb-4 text-gray-600">
            {{ \Illuminate\Support\Str::limit($product->description ?: 'Freshly baked and ready for your celebration.', 70) }}
        </p>
        <div class="flex items-center justify-between">
            <div>
                <span class="text-2xl font-bold text-pink-600">&#8369;{{ number_format($product->effective_price, 2) }}</span>
                @if($product->hasDiscount())
                    <div class="text-sm text-gray-400 line-through">&#8369;{{ number_format($product->price, 2) }}</div>
                @endif
            </div>
        </div>
    </div>
</a>
