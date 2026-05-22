@extends('layouts.app')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&display=swap');

        .shop-shell {
            border: 1px solid #E9C7D4;
            border-radius: 22px;
            background: linear-gradient(180deg, #FBEAF1 0%, #FFFFFF 42%, #FBF2F6 100%);
            box-shadow: 0 18px 48px rgba(77, 46, 56, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.65);
            padding: 1.2rem;
        }
        .shop-shell-title {
            font-family: 'Playfair Display', serif;
            color: #4D2E38;
            letter-spacing: -0.01em;
        }
        .shop-shell-sub {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #8F6172;
        }
        .shop-panel {
            border: 1px solid #E9C7D4;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.58);
            box-shadow: 0 8px 22px rgba(77, 46, 56, 0.08), inset 0 1px 0 rgba(255,255,255,0.55);
            backdrop-filter: blur(9px);
            -webkit-backdrop-filter: blur(9px);
            padding: 1rem;
        }
        .shop-title {
            color: #4D2E38;
            letter-spacing: -0.02em;
        }
        .shop-muted {
            color: #8A6A76;
        }
        .shop-input,
        .shop-select {
            border: 1px solid #E9C7D4 !important;
            border-radius: 12px !important;
            background: rgba(255,255,255,0.78) !important;
            color: #4D2E38 !important;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }
        .shop-input:focus,
        .shop-select:focus {
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(196, 122, 144, 0.15) !important;
            border-color: #C47A90 !important;
        }
        .shop-btn-primary {
            background: linear-gradient(135deg, #C47A90, #B66880) !important;
            color: #fff !important;
            border-radius: 12px !important;
            font-weight: 600;
        }
        .shop-btn-primary:hover {
            filter: brightness(0.97);
        }
        .shop-btn-secondary {
            border: 1px solid #E9C7D4 !important;
            background: #fff !important;
            color: #533843 !important;
            border-radius: 12px !important;
        }
        .shop-btn-secondary:hover {
            background: #FBEAF1 !important;
        }
        .shop-case {
            position: relative;
            border: 1px solid #E9C7D4;
            border-radius: 18px;
            overflow: hidden;
            background:
                linear-gradient(180deg, rgba(255,255,255,0.72) 0%, rgba(246,223,233,0.30) 56%, rgba(255,255,255,0.64) 100%),
                linear-gradient(120deg, rgba(196,122,144,0.12) 0%, rgba(255,255,255,0.04) 48%, rgba(196,122,144,0.08) 100%);
            box-shadow:
                0 24px 60px rgba(77,46,56,0.18),
                0 8px 20px rgba(77,46,56,0.14),
                inset 0 1px 0 rgba(255,255,255,0.8),
                inset 0 -1px 0 rgba(182,104,128,0.22);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 1.1rem;
        }
        .shop-stage {
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid #E9C7D4;
            background:
                repeating-linear-gradient(
                    90deg,
                    #FBF2F6 0,
                    #FBF2F6 88px,
                    #FBEAF1 88px,
                    #FBEAF1 176px
                );
            box-shadow: 0 16px 40px rgba(77,46,56,0.1), inset 0 1px 0 rgba(255,255,255,0.6);
        }
        .shop-stage-case-wrap {
            padding: 1.2rem;
            background: linear-gradient(180deg, rgba(255,255,255,0.28) 0%, rgba(255,255,255,0.08) 100%);
        }
        .shop-case-frame-top,
        .shop-case-frame-bottom {
            position: absolute;
            left: 0;
            right: 0;
            height: 16px;
            z-index: 3;
            pointer-events: none;
            background: linear-gradient(180deg, #F6DFE9 0%, #C47A90 52%, #B66880 100%);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,0.45),
                0 2px 6px rgba(77,46,56,0.15);
        }
        .shop-case-frame-top { top: 0; }
        .shop-case-frame-bottom { bottom: 0; }
        .shop-case::before {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                linear-gradient(120deg, rgba(255,255,255,0.58) 0%, rgba(255,255,255,0.14) 24%, transparent 55%),
                radial-gradient(circle at 16% 18%, rgba(255,255,255,0.32) 0%, transparent 26%);
        }
        .shop-case::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 12px;
            background: linear-gradient(180deg, #F6DFE9 0%, #C47A90 52%, #B66880 100%);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.45);
            pointer-events: none;
        }
        .shop-grid {
            position: relative;
            z-index: 2;
        }
        .shop-grid-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.9rem;
            padding: 0 0.3rem;
        }
        .shop-grid-inner {
            position: relative;
            z-index: 2;
            padding-bottom: 0.65rem;
        }
        .shop-shelf-row {
            position: relative;
            margin-bottom: 1.05rem;
            padding-bottom: 1.05rem;
        }
        .shop-shelf-divider {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 14px;
            border-radius: 10px;
            background: linear-gradient(180deg, #F6DFE9 0%, #C47A90 36%, #B66880 68%, #8F6172 100%);
            box-shadow:
                0 10px 18px rgba(77,46,56,0.22),
                0 2px 0 rgba(255,255,255,0.35) inset,
                0 -2px 0 rgba(91,49,67,0.24) inset;
            opacity: 0.75;
            pointer-events: none;
        }
        .shop-shelf-divider::before {
            content: '';
            position: absolute;
            left: 8%;
            right: 8%;
            top: 2px;
            height: 2px;
            border-radius: 999px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.65), transparent);
        }
        .shop-shelf-divider::after {
            content: '';
            position: absolute;
            left: 3%;
            right: 3%;
            bottom: -5px;
            height: 7px;
            border-radius: 999px;
            background: rgba(95, 55, 72, 0.24);
            filter: blur(4px);
        }
        .shop-shelf-light {
            position: absolute;
            left: 8%;
            right: 8%;
            top: -0.45rem;
            height: 2px;
            border-radius: 999px;
            background: linear-gradient(90deg, transparent, rgba(233,199,212,0.95), transparent);
            pointer-events: none;
        }
        .shop-grid-title {
            font-family: 'Playfair Display', serif;
            color: #4D2E38;
            font-weight: 600;
            font-size: 1.35rem;
        }
    </style>

    <div class="shop-shell flex flex-col gap-8 lg:flex-row">
        <aside class="shop-panel w-full lg:w-1/4 lg:pr-8">
            <h2 class="shop-title text-2xl font-bold mb-6">Filters</h2>

            <form method="GET" action="{{ route('shop.index') }}" class="space-y-6">
                @if(request()->boolean('featured'))
                    <input type="hidden" name="featured" value="1">
                @endif
                @if(request()->boolean('best_seller'))
                    <input type="hidden" name="best_seller" value="1">
                @endif
                <div class="mb-6">
                    <h3 class="shop-title text-lg font-semibold mb-3">Category</h3>
                    <div class="space-y-2">
                        <label class="shop-muted flex items-center">
                            <input type="radio" name="category" value="" class="mr-2" {{ request('category') ? '' : 'checked' }}>
                            All Categories
                        </label>
                        @foreach($categories as $category)
                            <label class="shop-muted flex items-center">
                                <input type="radio" name="category" value="{{ $category->id }}" class="mr-2" {{ (string) request('category') === (string) $category->id ? 'checked' : '' }}>
                                {{ $category->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="shop-title text-lg font-semibold mb-3">Sort</h3>
                    <select name="sort" class="shop-select w-full px-4 py-2">
                        <option value="">Sort by: Name</option>
                        <option value="latest" @selected(request('sort') === 'latest')>Sort by: Latest</option>
                        <option value="price_low" @selected(request('sort') === 'price_low')>Sort by: Price (Low to High)</option>
                        <option value="price_high" @selected(request('sort') === 'price_high')>Sort by: Price (High to Low)</option>
                    </select>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="shop-btn-primary px-4 py-2">Apply</button>
                    <a href="{{ route('shop.index') }}" class="shop-btn-secondary px-4 py-2">Reset</a>
                </div>
            </form>
        </aside>

        <main class="w-full lg:w-3/4">
            <div class="mb-4">
                <p class="shop-shell-sub">Display Case</p>
                <h1 class="shop-shell-title text-3xl font-semibold">Cake Showcase</h1>
            </div>

            <form method="GET" action="{{ route('shop.index') }}" class="shop-panel flex flex-col gap-4 mb-6 md:flex-row md:justify-between md:items-center">
                <div class="flex-1 max-w-md">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="shop-input w-full px-4 py-2">
                </div>
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                @if(request()->boolean('featured'))
                    <input type="hidden" name="featured" value="1">
                @endif
                @if(request()->boolean('best_seller'))
                    <input type="hidden" name="best_seller" value="1">
                @endif
                <div class="ml-0 md:ml-4 flex gap-3 items-center">
                    <select name="sort" class="shop-select px-4 py-2">
                        <option value="">Sort by: Name</option>
                        <option value="latest" @selected(request('sort') === 'latest')>Sort by: Latest</option>
                        <option value="price_low" @selected(request('sort') === 'price_low')>Sort by: Price (Low to High)</option>
                        <option value="price_high" @selected(request('sort') === 'price_high')>Sort by: Price (High to Low)</option>
                    </select>
                    <button type="submit" class="shop-btn-primary px-4 py-2">Search</button>
                </div>
            </form>

            <div class="mb-4 text-sm shop-muted">
                {{ $products->total() }} cakes available
            </div>

            <div class="shop-stage">
                <div class="shop-stage-case-wrap">
                    <div class="shop-case">
                        <div class="shop-case-frame-top"></div>
                        <div class="shop-case-frame-bottom"></div>
                        <div class="shop-grid">
                            <div class="shop-grid-head">
                                <h2 class="shop-grid-title">Featured Shelf</h2>
                            </div>

                            @forelse($products->getCollection()->chunk(3) as $rowIndex => $rowProducts)
                                <div class="shop-shelf-row">
                                    <div class="shop-grid-inner grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                        @foreach($rowProducts as $product)
                                            @include('components.product-card', ['product' => $product])
                                        @endforeach
                                    </div>
                                    @if(!$loop->last)
                                        <div class="shop-shelf-light"></div>
                                        <div class="shop-shelf-divider"></div>
                                    @endif
                                </div>
                            @empty
                                <div class="shop-grid-inner grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div class="col-span-full rounded-lg border border-dashed border-[#E9C7D4] bg-[#FFFFFF] p-12 text-center text-[#8A6A76]">
                                        No products found.
                                    </div>
                                </div>
                            @endforelse

                        </div>
                    </div>
                </div>
            </div>

            @if($products->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $products->links() }}
                </div>
            @endif
        </main>
    </div>
@endsection
