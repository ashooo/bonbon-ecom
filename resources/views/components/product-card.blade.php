<a href="{{ route('products.show', $product->slug) }}" class="group block overflow-hidden rounded-2xl border border-[#E9C7D4] bg-[linear-gradient(180deg,#FFFFFF,#FBEAF1)] shadow-[0_12px_26px_rgba(77,46,56,0.12)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_16px_34px_rgba(77,46,56,0.18)]">
    <div class="relative aspect-[4/3] bg-[radial-gradient(circle_at_50%_32%,#FFFFFF_0%,#FBF2F6_65%,#F6DFE9_100%)]">
        <img
            src="{{ $product->main_image_url ?: 'https://via.placeholder.com/300x200?text=' . urlencode($product->name) }}"
            alt="{{ $product->name }}"
            class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]"
        >
        <div class="absolute bottom-3 left-3 max-w-[78%] rounded-md border border-[#ECD8E0] bg-[linear-gradient(180deg,#FFFFFF_0%,#FBF2F6_100%)] px-3 py-2 shadow-[0_8px_16px_rgba(77,46,56,0.18)]">
            <div class="pointer-events-none absolute inset-0 rounded-md opacity-[0.22]" style="background-image: radial-gradient(rgba(132,98,110,0.20) 0.55px, transparent 0.55px); background-size: 3px 3px;"></div>
            <p class="truncate text-[0.78rem] font-semibold leading-tight text-[#4D2E38]">{{ $product->name }}</p>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-sm font-bold text-[#C47A90]">&#8369;{{ number_format($product->effective_price, 2) }}</span>
                @if($product->hasDiscount())
                <span class="text-[0.65rem] text-[#8A6A76] line-through">&#8369;{{ number_format($product->price, 2) }}</span>
                @endif
            </div>
        </div>
    </div>
</a>
