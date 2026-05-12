@extends('layouts.app')

@section('content')
    @php
        $galleryImages = collect([$product->main_image_url])
            ->merge($product->images->map->image_url)
            ->filter()
            ->unique()
            ->values();
        $variants = $product->variants ?? collect();
        $selectableVariants = $variants->filter(fn ($variant) => (bool) $variant->is_active);
        $defaultVariant = $selectableVariants->firstWhere('is_default', true) ?? $selectableVariants->first();
        $initialPrice = $defaultVariant ? (float) $defaultVariant->price_adjustment : (float) $product->effective_price;
        $initialStock = (int) ($defaultVariant?->stock_quantity ?? $product->stock_quantity);
        $initialStatusText = $product->status === 'pre_order' ? 'Pre order' : ($initialStock > 0 ? 'Available' : 'Out of stock');
        $fallbackImage = $galleryImages->first() ?: 'https://via.placeholder.com/600x400?text=' . urlencode($product->name);
        $initialImage = $fallbackImage;
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <div>
            <div class="mb-6 overflow-hidden rounded-3xl bg-white shadow-lg">
                <img
                    id="main-product-image"
                    src="{{ $initialImage }}"
                    alt="{{ $product->name }}"
                    class="h-[480px] w-full object-cover"
                >
            </div>

            @if($variants->whereNotNull('image_path')->isNotEmpty() || $galleryImages->count() > 1)
                <div class="relative mt-4 overflow-hidden rounded-3xl px-3 py-2">
                    <button
                        type="button"
                        id="thumb-scroll-left"
                        class="absolute left-3 top-[calc(50%+4px)] z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/95 text-[#5A3A3A] shadow-md ring-1 ring-pink-100 transition hover:bg-pink-50"
                        aria-label="Scroll thumbnails left"
                    >
                        <span aria-hidden="true">&lt;</span>
                    </button>

                    <div
                        id="product-thumb-rail"
                        class="flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth px-14 py-2 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                    >
                        @foreach($galleryImages as $image)
                            <button
                                type="button"
                                class="product-image-thumb h-28 w-32 flex-none snap-start overflow-hidden rounded-2xl bg-white shadow-sm ring-2 ring-transparent transition hover:ring-pink-300"
                                data-image="{{ $image }}"
                                aria-label="Show product image"
                            >
                                <img src="{{ $image }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                            </button>
                        @endforeach

                        @foreach($variants->filter(fn ($variant) => filled($variant->image_url)) as $variant)
                            <button
                                type="button"
                                class="variant-image-thumb h-28 w-32 flex-none snap-start overflow-hidden rounded-2xl bg-white shadow-sm ring-2 ring-transparent transition hover:ring-pink-300"
                                data-variant-id="{{ $variant->id }}"
                                data-image="{{ $variant->image_url }}"
                                aria-label="Show {{ $variant->name }} image"
                            >
                                <img src="{{ $variant->image_url }}" alt="{{ $product->name }} - {{ $variant->name }}" class="h-full w-full object-cover">
                            </button>
                        @endforeach
                    </div>

                    <button
                        type="button"
                        id="thumb-scroll-right"
                        class="absolute right-3 top-[calc(50%+4px)] z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/95 text-[#5A3A3A] shadow-md ring-1 ring-pink-100 transition hover:bg-pink-50"
                        aria-label="Scroll thumbnails right"
                    >
                        <span aria-hidden="true">&gt;</span>
                    </button>
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

            <h1 class="text-4xl font-bold mb-2">{{ $product->name }}</h1>
            @if($defaultVariant)
                <p id="selected-variant-name" class="mb-4 text-lg font-semibold text-[#8C6770]">
                    {{ $defaultVariant->name }}
                </p>
            @endif

            <div class="mb-6 flex items-baseline gap-3">
                <p class="text-3xl font-bold text-[#5A3A3A]">&#8369;<span id="product-price">{{ number_format($initialPrice, 2) }}</span></p>
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
                    <p id="product-stock" class="mt-2 text-2xl font-semibold {{ $initialStock > 0 || $product->status === 'pre_order' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $product->status === 'pre_order' ? ($product->pre_order_days ? $product->pre_order_days . ' day lead time' : 'Available for pre-order') : ($initialStock > 0 ? $initialStock . ' available' : 'Out of stock') }}
                    </p>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <p class="text-sm uppercase tracking-[0.18em] text-gray-500">Status</p>
                    <p id="product-status" class="mt-2 text-2xl font-semibold {{ $product->status === 'pre_order' ? 'text-[#5A3A3A]' : ($initialStock > 0 ? 'text-green-600' : 'text-red-600') }}">
                        {{ $initialStatusText }}
                    </p>
                </div>
            </div>

            <form action="{{ route('cart.add') }}" method="POST" class="rounded-3xl bg-white p-6 shadow-sm">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                @if($variants->isNotEmpty())
                    <div class="mb-5">
                        <p class="mb-2 block text-sm font-medium text-gray-700">Variant</p>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach($variants as $variant)
                                @php
                                    $variantPrice = (float) $variant->price_adjustment;
                                    $variantStock = (int) $variant->stock_quantity;
                                    $isActive = (bool) $variant->is_active;
                                    $isAvailable = $isActive && ($product->status === 'pre_order' || $variantStock > 0);
                                @endphp
                                <label class="flex items-start gap-3 rounded-2xl border border-gray-200 p-4 transition has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50 {{ $isAvailable ? 'cursor-pointer hover:border-pink-300' : 'cursor-not-allowed opacity-60' }}">
                                    <input
                                        type="radio"
                                        name="variant_id"
                                        value="{{ $variant->id }}"
                                        class="mt-1 text-pink-600 focus:ring-pink-500"
                                        data-name="{{ $variant->name }}"
                                        data-price="{{ number_format($variantPrice, 2, '.', '') }}"
                                        data-stock="{{ $variantStock }}"
                                        data-image="{{ $variant->image_url ?: $fallbackImage }}"
                                        @checked($defaultVariant?->id === $variant->id)
                                        @disabled(! $isAvailable)
                                    >
                                    <span>
                                        <span class="block font-semibold text-[#5A3A3A]">{{ $variant->name }}</span>
                                        <span class="block text-sm text-gray-600">&#8369;{{ number_format($variantPrice, 2) }}</span>
                                        <span class="block text-xs {{ $isAvailable ? 'text-green-600' : 'text-red-600' }}">
                                            {{ ! $isActive ? 'Not available' : ($product->status === 'pre_order' ? 'Pre-order available' : ($variantStock > 0 ? $variantStock . ' available' : 'Out of stock')) }}
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('variant_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

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

@push('scripts')
    <script>
        (function () {
            const stockEl = document.getElementById('product-stock');
            const statusEl = document.getElementById('product-status');
            const priceEl = document.getElementById('product-price');
            const variantNameEl = document.getElementById('selected-variant-name');
            const quantityEl = document.getElementById('quantity');
            const mainImageEl = document.getElementById('main-product-image');
            const thumbRail = document.getElementById('product-thumb-rail');
            const thumbLeft = document.getElementById('thumb-scroll-left');
            const thumbRight = document.getElementById('thumb-scroll-right');
            const isPreOrder = @json($product->status === 'pre_order');
            const preOrderText = @json($product->pre_order_days ? $product->pre_order_days . ' day lead time' : 'Available for pre-order');
            const markActiveThumb = (imageUrl) => {
                document.querySelectorAll('.variant-image-thumb, .product-image-thumb').forEach((button) => {
                    button.classList.toggle('ring-pink-500', button.dataset.image === imageUrl);
                    button.classList.toggle('ring-transparent', button.dataset.image !== imageUrl);
                });
            };
            const setMainImage = (imageUrl) => {
                if (mainImageEl && imageUrl) {
                    mainImageEl.src = imageUrl;
                    markActiveThumb(imageUrl);
                }
            };

            markActiveThumb(@json($initialImage));

            const scrollThumbs = (direction) => {
                if (!thumbRail) return;
                const amount = Math.max(160, Math.floor(thumbRail.clientWidth * 0.75));
                thumbRail.scrollBy({
                    left: direction * amount,
                    behavior: 'smooth',
                });
            };

            thumbLeft?.addEventListener('click', () => scrollThumbs(-1));
            thumbRight?.addEventListener('click', () => scrollThumbs(1));

            document.querySelectorAll('input[name="variant_id"]').forEach((input) => {
                input.addEventListener('change', () => {
                    if (!input.checked) return;

                    const price = Number(input.dataset.price || 0);
                    const stock = Number(input.dataset.stock || 0);
                    setMainImage(input.dataset.image);

                    if (variantNameEl) {
                        variantNameEl.textContent = input.dataset.name || '';
                    }

                    if (priceEl) {
                        priceEl.textContent = price.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        });
                    }

                    if (stockEl) {
                        stockEl.textContent = isPreOrder ? preOrderText : (stock > 0 ? `${stock} available` : 'Out of stock');
                        stockEl.classList.toggle('text-green-600', isPreOrder || stock > 0);
                        stockEl.classList.toggle('text-red-600', !isPreOrder && stock <= 0);
                    }

                    if (statusEl) {
                        statusEl.textContent = isPreOrder ? 'Pre order' : (stock > 0 ? 'Available' : 'Out of stock');
                        statusEl.classList.toggle('text-[#5A3A3A]', isPreOrder);
                        statusEl.classList.toggle('text-green-600', !isPreOrder && stock > 0);
                        statusEl.classList.toggle('text-red-600', !isPreOrder && stock <= 0);
                    }

                    if (quantityEl && !isPreOrder && stock > 0) {
                        quantityEl.max = String(stock);
                    }
                });
            });

            document.querySelectorAll('.variant-image-thumb').forEach((button) => {
                button.addEventListener('click', () => {
                    const variantInput = document.querySelector(`input[name="variant_id"][value="${button.dataset.variantId}"]`);
                    if (variantInput && !variantInput.disabled) {
                        variantInput.checked = true;
                        variantInput.dispatchEvent(new Event('change', { bubbles: true }));
                    } else {
                        setMainImage(button.dataset.image);
                    }
                });
            });

            document.querySelectorAll('.product-image-thumb').forEach((button) => {
                button.addEventListener('click', () => setMainImage(button.dataset.image));
            });
        })();
    </script>
@endpush
