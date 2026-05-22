@extends('layouts.app')

@section('hideGlobalLoader', true)
@section('hideChatbot', false)
@section('hideFooter', true)

@php
    $cakesCategory = $featuredCategories->first(fn ($category) => str_contains(strtolower($category->name), 'cake'));
    $cakesUrl = url('/#shop');
    $shelfProducts = $shelfProducts ?? collect();
@endphp

@section('content')
    {{-- Preloader --}}
    <div id="cake-preloader" class="cake-preloader" aria-live="polite">
        <div class="preloader-inner">
            <div class="preloader-ring"></div>
            <p class="preloader-text">BonBon</p>
        </div>
    </div>

    {{-- Scrollytelling Section --}}
    <section id="cake-scroll-section" class="cake-scroll-section">
        <div id="cake-sticky-panel" class="cake-sticky-panel">
            {{-- 3D Canvas --}}
            <div id="cake-canvas-container" class="cake-canvas-container"></div>

            {{-- Ambient grain overlay --}}
            <div class="cake-grain" aria-hidden="true"></div>

            {{-- Vignette --}}
            <div class="cake-vignette" aria-hidden="true"></div>

            {{-- Story Messages —  positioned bottom-left for desktop --}}
            <div class="story-messages">
                <div id="story-msg-1" class="story-msg" style="opacity: 0;">
                    <span class="story-label">The Crown</span>
                    <h2 class="story-heading">Every masterpiece<br>starts at the top.</h2>
                </div>

                <div id="story-msg-2" class="story-msg" style="opacity: 0;">
                    <span class="story-label">The Heart</span>
                    <h2 class="story-heading">Layered with richness,<br>depth &amp; texture.</h2>
                </div>

                <div id="story-msg-3" class="story-msg" style="opacity: 0;">
                    <span class="story-label">The Foundation</span>
                    <h2 class="story-heading">Built on craft.<br>Completed with care.</h2>
                </div>

                <div id="story-msg-final" class="story-msg story-msg-final" style="opacity: 0;">
                    <h2 class="story-heading-final">Handcrafted<br>Chocolate Perfection</h2>
                    <a id="story-cta-btn" href="#home-showcase" class="story-cta" style="opacity: 0;">
                        Explore Our Shop
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    </a>
                </div>
            </div>

            {{-- Signboard Overlay (top-left) --}}
            <!-- <div id="signboard-overlay" class="side-overlay side-overlay-left" style="opacity: 0;">
                <a href="{{ url('/#shop') }}" class="side-overlay-btn side-overlay-btn-sign">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 L15.09 8.26 L22 9.27 L17 14.14 L18.18 21.02 L12 17.77 L5.82 21.02 L7 14.14 L2 9.27 L8.91 8.26Z"/></svg>
                    Recommendations
                </a>
            </div> -->

            {{-- Cupcake Overlay (top-right) --}}
            <!-- <div id="cupcake-overlay" class="side-overlay side-overlay-right" style="opacity: 0;">
                <a href="{{ url('/#shop') }}" class="side-overlay-btn side-overlay-btn-cupcake">
                    Explore Our Pastries
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </a>
            </div> -->

            {{-- Gallery Back Button --}}
            <button id="gallery-back-btn" class="gallery-back-btn" style="display: none; opacity: 0;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                Go Back
            </button>

            {{-- Gallery Filter Bar --}}
            <div id="gallery-filter-bar" class="gallery-filter-bar" style="display: none; opacity: 0;">
                <div class="filter-search-wrap">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <input type="text" id="gallery-search" placeholder="Search cakes...">
                </div>
                <select id="gallery-category" class="filter-select">
                    <option value="">All Categories</option>
                    @foreach($featuredCategories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select id="gallery-sort" class="filter-select">
                    <option value="name_asc">Name: A-Z</option>
                    <option value="name_desc">Name: Z-A</option>
                    <option value="price_low">Price: Low to High</option>
                    <option value="price_high">Price: High to Low</option>
                </select>
            </div>

            {{-- Mobile Gallery Scroll Area --}}
            <div id="gallery-scroll-overlay">
                <div class="gallery-scroll-overlay-content" id="gallery-scroll-overlay-content"></div>
            </div>

            {{-- Scroll indicator --}}
            <div class="scroll-indicator" id="scroll-indicator">
                <div class="scroll-mouse">
                    <div class="scroll-wheel"></div>
                </div>
                <span>Scroll to discover</span>
            </div>

            {{-- Product Details Modal --}}
            <div id="gallery-product-modal" class="gallery-product-modal" style="display: none; opacity: 0;">
                <div class="gallery-modal-backdrop" id="gallery-modal-backdrop"></div>
                <div class="gallery-modal-content">
                    <button id="gallery-modal-close" class="gallery-modal-close">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    </button>
                    <div class="gallery-modal-body">
                        <h3 id="gallery-modal-title" class="gallery-modal-title"></h3>
                        <p id="gallery-modal-price" class="gallery-modal-price"></p>
                        <span id="gallery-modal-stock" class="gallery-modal-stock"></span>
                        <p id="gallery-modal-desc" class="gallery-modal-desc"></p>

                        <form id="gallery-modal-form" action="{{ route('cart.add') }}" method="POST" class="mt-6">
                            @csrf
                            <input type="hidden" name="product_id" id="gallery-modal-product-id" value="">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="gallery-modal-add-btn">
                                Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════
         HOME SHOWCASE — Premium Bakery Layout
         ═══════════════════════════════════════════════════ --}}
    <section id="home-showcase" class="bb-showcase" style="display:none;">

        {{-- ── Marquee Tag Strip ── --}}
        <div class="bb-marquee-wrap" aria-hidden="true">
            <div class="bb-marquee-track">
                @foreach(['Handcrafted Daily', '✦ Custom Cakes', 'Free Delivery Over ₱1500', '✦ Filipino Flavors', 'Made with Love', '✦ Best Sellers', 'Birthday Cakes', '✦ Wedding Tiers', 'Handcrafted Daily', '✦ Custom Cakes', 'Free Delivery Over ₱1500', '✦ Filipino Flavors', 'Made with Love', '✦ Best Sellers', 'Birthday Cakes', '✦ Wedding Tiers'] as $tag)
                <span class="bb-marquee-item">{{ $tag }}</span>
                @endforeach
            </div>
        </div>

        {{-- ── Hero Banner ── --}}
        <div class="bb-hero-banner">
            <div class="bb-hero-img-wrap">
                <img src="/images/bonbon_showcase_hero.png" alt="BonBon artisan cakes and pastries" class="bb-hero-img">
                <div class="bb-hero-overlay"></div>
            </div>
            <div class="bb-hero-text">
                <h2 class="bb-hero-heading">Handcrafted<br>with Devotion.</h2>
                <p class="bb-hero-sub">Every cake is a celebration — made from scratch, designed with care, delivered with love.</p>
                <div class="bb-hero-actions">
                    <a href="{{ url('/test-products') }}" class="bb-btn-primary">Shop Now</a>
                    <a href="/customize" class="bb-btn-ghost">Custom Order</a>
                </div>
            </div>
        </div>

        {{-- ── Featured Products ── --}}
        <div class="bb-section-wrap">
            <div class="bb-section-header">
                <div>
                    <span class="bb-eyebrow">Our Signature Selection</span>
                    <h2 class="bb-section-title">Featured Products</h2>
                </div>
                <a href="{{ url('/test-products') }}" class="bb-see-all">See all
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </a>
            </div>

            <div class="bb-product-grid">
                @forelse($featuredProducts->take(8) as $product)
                <a href="{{ route('products.show', $product->slug) }}" class="bb-product-card">
                    <div class="bb-card-img-wrap">
                        <img
                            src="{{ $product->main_image_url ?: 'https://via.placeholder.com/480x480?text=' . urlencode($product->name) }}"
                            alt="{{ $product->name }}"
                            class="bb-card-img"
                            loading="lazy"
                        >
                        <div class="bb-card-hover-overlay">
                            <span class="bb-card-cta">View Product</span>
                        </div>
                        @if($product->hasDiscount())
                        <span class="bb-sale-badge">Sale</span>
                        @endif
                    </div>
                    <div class="bb-card-body">
                        <span class="bb-card-cat">{{ $product->category?->name ?? 'Pastry' }}</span>
                        <p class="bb-card-name">{{ $product->name }}</p>
                        <div class="bb-card-price-row">
                            <span class="bb-card-price">&#8369;{{ number_format($product->effective_price, 2) }}</span>
                            @if($product->hasDiscount())
                            <span class="bb-card-original">&#8369;{{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>
                    </div>
                </a>
                @empty
                <div class="bb-empty-state">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    <p>No featured products yet — check back soon!</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ── Best Sellers ── --}}
        @php
            $bestSellers = $shelfProducts->where('is_best_seller', true)->take(4)->values();
        @endphp
        @if($bestSellers->count() > 0)
        <div class="bb-bestsellers-band">
            <div class="bb-section-wrap">
                <div class="bb-section-header">
                    <div>
                        <span class="bb-eyebrow" style="color:#fff8fb; opacity:0.75;">Community Favorites</span>
                        <h2 class="bb-section-title" style="color:#fff;">Best Sellers</h2>
                    </div>
                    <a href="{{ url('/test-products') }}" class="bb-see-all" style="color:rgba(255,255,255,0.8); border-color:rgba(255,255,255,0.3);">See all
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    </a>
                </div>
                <div class="bb-bestseller-grid">
                    @foreach($bestSellers as $i => $product)
                    <a href="{{ route('products.show', $product->slug) }}" class="bb-bs-card">
                        <div class="bb-bs-img-wrap">
                            <img src="{{ $product->main_image_url ?: 'https://via.placeholder.com/400x400?text=' . urlencode($product->name) }}" alt="{{ $product->name }}" class="bb-bs-img" loading="lazy">
                            <span class="bb-bs-rank">#{{ $i + 1 }}</span>
                        </div>
                        <div class="bb-bs-body">
                            <p class="bb-bs-name">{{ $product->name }}</p>
                            <p class="bb-bs-price">&#8369;{{ number_format($product->effective_price, 2) }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- ── Why BonBon strip ── --}}
        <div class="bb-perks-row">
            <div class="bb-perk">
                <div class="bb-perk-icon">🎂</div>
                <p class="bb-perk-title">Made Fresh Daily</p>
                <p class="bb-perk-sub">Baked from scratch every morning — no preservatives, ever.</p>
            </div>
            <div class="bb-perk-divider"></div>
            <div class="bb-perk">
                <div class="bb-perk-icon">🎀</div>
                <p class="bb-perk-title">Fully Customizable</p>
                <p class="bb-perk-sub">Design your dream cake — flavors, tiers, decor, messages.</p>
            </div>
            <div class="bb-perk-divider"></div>
            <div class="bb-perk">
                <div class="bb-perk-icon">🚚</div>
                <p class="bb-perk-title">Swift Delivery</p>
                <p class="bb-perk-sub">Same-day Metro Manila delivery available. Free over ₱1500.</p>
            </div>
            <div class="bb-perk-divider"></div>
            <div class="bb-perk">
                <div class="bb-perk-icon">💌</div>
                <p class="bb-perk-title">Gift-Ready Packaging</p>
                <p class="bb-perk-sub">Every order arrives beautifully boxed and ribbon-tied.</p>
            </div>
        </div>

        {{-- ── Testimonials ── --}}
        <div class="bb-section-wrap">
            <div class="bb-section-header">
                <div>
                    <span class="bb-eyebrow">What Customers Say</span>
                    <h2 class="bb-section-title">Reviews</h2>
                </div>
                <a href="https://www.facebook.com/BonbonsPHofficial" target="_blank" rel="noopener" class="bb-see-all">
                    Facebook Page
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </a>
            </div>
            <div class="bb-reviews-grid">
                <div class="bb-review-card bb-review-large">
                    <div class="bb-review-stars">★★★★★</div>
                    <p class="bb-review-text">"Super ganda and sobrang sarap. Exactly what we needed for our daughter's debut. Everyone was asking where we got it!"</p>
                    <div class="bb-review-author">
                        <div class="bb-review-avatar">M</div>
                        <div>
                            <p class="bb-review-name">Maria Santos</p>
                            <p class="bb-review-source">via Facebook</p>
                        </div>
                    </div>
                </div>
                <div class="bb-review-card">
                    <div class="bb-review-stars">★★★★★</div>
                    <p class="bb-review-text">"Reliable delivery and very responsive team. Will definitely order again for every occasion!"</p>
                    <div class="bb-review-author">
                        <div class="bb-review-avatar">J</div>
                        <div>
                            <p class="bb-review-name">James Reyes</p>
                            <p class="bb-review-source">via Facebook</p>
                        </div>
                    </div>
                </div>
                <div class="bb-review-card">
                    <div class="bb-review-stars">★★★★★</div>
                    <p class="bb-review-text">"The custom design was perfect. Great balance of sweetness — not too sweet, just right."</p>
                    <div class="bb-review-author">
                        <div class="bb-review-avatar">A</div>
                        <div>
                            <p class="bb-review-name">Anna Cruz</p>
                            <p class="bb-review-source">via Facebook</p>
                        </div>
                    </div>
                </div>
                <div class="bb-review-card">
                    <div class="bb-review-stars">★★★★★</div>
                    <p class="bb-review-text">"Ordered twice already. The packaging alone is worth it — so pretty and gift-ready!"</p>
                    <div class="bb-review-author">
                        <div class="bb-review-avatar">L</div>
                        <div>
                            <p class="bb-review-name">Liza Mendoza</p>
                            <p class="bb-review-source">via Facebook</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── CTA Band ── --}}
        <div class="bb-cta-band">
            <div class="bb-cta-content">
                <h2 class="bb-cta-heading">Ready to place your order?</h2>
                <p class="bb-cta-sub">Browse our full collection or start building your custom cake today.</p>
                <div class="bb-cta-actions">
                    <a href="{{ url('/test-products') }}" class="bb-btn-primary">Browse Collection</a>
                    <a href="/customize" class="bb-btn-ghost-dark">Customize a Cake</a>
                </div>
            </div>
        </div>

    </section>

    <style>
        /* ═══════════════════════════════════════════════════
           BONBON HOME SHOWCASE — Premium Redesign
           ═══════════════════════════════════════════════════ */

        .bb-showcase {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0 0 clamp(2rem, 4vw, 3.5rem);
            background: #FFFFFF;
            font-family: 'Inter', sans-serif;
        }

        /* ── Marquee ── */
        .bb-marquee-wrap {
            width: 100%;
            overflow: hidden;
            background: linear-gradient(90deg, #4D2E38 0%, #533843 100%);
            padding: 0.7rem 0;
            border-top: 1px solid rgba(255,255,255,0.06);
            margin-bottom: 0;
        }
        .bb-marquee-track {
            display: flex;
            gap: 0;
            width: max-content;
            animation: bbMarquee 28s linear infinite;
        }
        .bb-marquee-item {
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #FBEAF1;
            padding: 0 2.8rem;
            white-space: nowrap;
        }
        @keyframes bbMarquee {
            from { transform: translateX(0); }
            to   { transform: translateX(-50%); }
        }

        /* ── Hero Banner ── */
        .bb-hero-banner {
            position: relative;
            width: 100%;
            height: clamp(420px, 55vh, 680px);
            overflow: hidden;
            display: flex;
            align-items: center;
            padding: 0 0 clamp(0.85rem, 2vw, 1.25rem);
        }
        .bb-hero-img-wrap {
            position: absolute;
            inset: 0 0 clamp(0.85rem, 2vw, 1.25rem);
            z-index: 0;
            border-radius: 0 0 1.25rem 1.25rem;
            overflow: hidden;
        }
        .bb-hero-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center 40%;
            transform: scale(1.02);
            transition: transform 8s ease;
        }
        .bb-hero-banner:hover .bb-hero-img {
            transform: scale(1.05);
        }
        .bb-hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(102deg, rgba(77, 46, 56, 0.82) 0%, rgba(143, 97, 114, 0.6) 44%, rgba(77, 46, 56, 0.2) 100%);
        }
        .bb-hero-text {
            position: relative;
            z-index: 2;
            padding: clamp(3.6rem, 8vw, 7rem) clamp(2rem, 6vw, 6rem);
            max-width: 580px;
        }

        @media (max-width: 768px) {
            .bb-hero-text {
                padding: clamp(2.25rem, 8vw, 3rem) clamp(1.25rem, 5vw, 2rem);
            }
        }
        .bb-hero-eyebrow {
            display: inline-block;
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: #FFFFFF;
            border: 1px solid rgba(255,255,255,0.72);
            background: rgba(255,255,255,0.16);
            padding: 0.32rem 0.9rem;
            border-radius: 999px;
            margin-top: 0;
            margin-bottom: 1.1rem;
            text-shadow: 0 2px 10px rgba(61,20,40,0.28);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.35), 0 6px 20px rgba(61,20,40,0.2);
        }
        .bb-hero-heading {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.2rem, 5vw, 4.2rem);
            font-weight: 600;
            line-height: 1.1;
            color: #FFFFFF;
            text-shadow: 0 4px 24px rgba(0,0,0,0.35);
            margin: 0 0 1rem;
        }
        .bb-hero-sub {
            font-size: clamp(0.85rem, 1.2vw, 1rem);
            line-height: 1.7;
            color: rgba(255,255,255,0.86);
            margin: 0 0 2rem;
            max-width: 420px;
        }
        .bb-hero-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        /* ── Buttons ── */
        .bb-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #C47A90 0%, #B66880 100%);
            color: #fff;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            text-decoration: none;
            padding: 0.85rem 2rem;
            border-radius: 999px;
            box-shadow: none;
            transition: transform 0.22s ease, box-shadow 0.22s ease, background 0.22s ease;
        }
        .bb-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: none;
            background: linear-gradient(135deg, #D4879E 0%, #C47A90 100%);
        }
        .bb-btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.22);
            color: #FFFFFF;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            text-decoration: none;
            padding: 0.85rem 2rem;
            border-radius: 999px;
            border: 1.5px solid rgba(255,255,255,0.82);
            backdrop-filter: blur(8px);
            text-shadow: 0 1px 10px rgba(61,20,40,0.35);
            transition: background 0.22s ease, border-color 0.22s ease, transform 0.22s ease, box-shadow 0.22s ease;
        }
        .bb-btn-ghost:hover {
            background: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.95);
            transform: translateY(-1px);
            box-shadow: none;
        }
        .bb-btn-ghost-dark {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: transparent;
            color: #4D2E38;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            text-decoration: none;
            padding: 0.85rem 2rem;
            border-radius: 999px;
            border: 1.5px solid rgba(77,46,56,0.26);
            transition: background 0.22s ease, border-color 0.22s ease, transform 0.22s ease;
        }
        .bb-btn-ghost-dark:hover {
            background: rgba(233,199,212,0.32);
            border-color: rgba(77,46,56,0.42);
            transform: translateY(-1px);
        }

        /* ── Section Wrapper ── */
        .bb-section-wrap {
            max-width: 1280px;
            margin: 0 auto;
            padding: clamp(2.5rem, 5vw, 4.5rem) clamp(1.2rem, 4vw, 3rem);
        }
        .bb-section-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 2.2rem;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .bb-eyebrow {
            display: block;
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: #8F6172;
            margin-bottom: 0.4rem;
        }
        .bb-section-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.7rem, 3vw, 2.6rem);
            font-weight: 600;
            color: #4D2E38;
            margin: 0;
            line-height: 1.15;
        }
        .bb-see-all {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #8F6172;
            text-decoration: none;
            border-bottom: 1.5px solid rgba(143,97,114,0.32);
            padding-bottom: 2px;
            transition: gap 0.2s ease, border-color 0.2s ease, color 0.2s ease;
            white-space: nowrap;
        }
        .bb-see-all:hover {
            gap: 0.7rem;
            border-color: #c0537c;
            color: #4D2E38;
        }

        /* ── Product Grid ── */
        .bb-product-grid {
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: minmax(230px, 1fr);
            gap: 1.2rem;
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: 0.35rem;
            scrollbar-width: thin;
            scrollbar-color: #E9C7D4 #FBF2F6;
        }
        .bb-product-grid::-webkit-scrollbar { height: 8px; }
        .bb-product-grid::-webkit-scrollbar-track { background: #FBF2F6; border-radius: 999px; }
        .bb-product-grid::-webkit-scrollbar-thumb { background: #E9C7D4; border-radius: 999px; }
        .bb-product-grid > * {
            min-width: 230px;
        }

        /* ── Product Card ── */
        .bb-product-card {
            background: #fff;
            border-radius: 1.25rem;
            border: 1px solid #f0d4e0;
            overflow: hidden;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            transition: transform 0.28s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.28s ease;
            box-shadow: 0 4px 18px rgba(77,46,56,0.07);
        }
        .bb-product-card:hover {
            transform: translateY(-6px) scale(1.012);
            box-shadow: 0 16px 40px rgba(77,46,56,0.16);
        }
        .bb-card-img-wrap {
            position: relative;
            aspect-ratio: 1 / 1;
            overflow: hidden;
            background: #fdf0f5;
        }
        .bb-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .bb-product-card:hover .bb-card-img {
            transform: scale(1.06);
        }
        .bb-card-hover-overlay {
            position: absolute;
            inset: 0;
            background: rgba(61,20,40,0.42);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.28s ease;
            backdrop-filter: blur(2px);
        }
        .bb-product-card:hover .bb-card-hover-overlay {
            opacity: 1;
        }
        .bb-card-cta {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #fff;
            border: 1.5px solid rgba(255,255,255,0.6);
            padding: 0.55rem 1.4rem;
            border-radius: 999px;
            backdrop-filter: blur(4px);
        }
        .bb-sale-badge {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            font-size: 0.6rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #fff;
            background: linear-gradient(135deg, #e8609a, #c0437b);
            padding: 0.28rem 0.72rem;
            border-radius: 999px;
            box-shadow: 0 2px 8px rgba(196,67,123,0.4);
        }
        .bb-card-body {
            padding: 1rem 1.15rem 1.2rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        .bb-card-cat {
            font-size: 0.6rem;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #c0537c;
        }
        .bb-card-name {
            font-size: 0.95rem;
            font-weight: 600;
            color: #3d1428;
            margin: 0;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .bb-card-price-row {
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
            margin-top: 0.4rem;
        }
        .bb-card-price {
            font-size: 1rem;
            font-weight: 700;
            color: #c0437b;
        }
        .bb-card-original {
            font-size: 0.78rem;
            color: #b09aa8;
            text-decoration: line-through;
        }

        /* ── Empty State ── */
        .bb-empty-state {
            grid-column: 1 / -1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            padding: 4rem 2rem;
            border: 2px dashed #f0d4e0;
            border-radius: 1.5rem;
            color: #b09aa8;
            text-align: center;
        }

        /* ── Best Sellers Band ── */
        .bb-bestsellers-band {
            background: linear-gradient(135deg, #3d1428 0%, #5c2240 50%, #3d1428 100%);
            width: 100%;
        }
        .bb-bestseller-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.4rem;
        }
        @media (min-width: 640px) {
            .bb-bestseller-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (min-width: 1024px) {
            .bb-bestseller-grid { grid-template-columns: repeat(4, 1fr); }
        }
        .bb-bs-card {
            text-decoration: none;
            display: flex;
            flex-direction: column;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 1.1rem;
            overflow: hidden;
            transition: transform 0.26s ease, background 0.26s ease, box-shadow 0.26s ease;
            backdrop-filter: blur(4px);
        }
        .bb-bs-card:hover {
            transform: translateY(-5px);
            background: rgba(255,255,255,0.13);
            box-shadow: 0 12px 32px rgba(0,0,0,0.22);
        }
        .bb-bs-img-wrap {
            position: relative;
            aspect-ratio: 1;
            overflow: hidden;
        }
        .bb-bs-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
            filter: saturate(0.9) brightness(0.95);
        }
        .bb-bs-card:hover .bb-bs-img {
            transform: scale(1.06);
            filter: saturate(1.05) brightness(1.0);
        }
        .bb-bs-rank {
            position: absolute;
            top: 0.65rem;
            left: 0.65rem;
            font-size: 0.62rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            color: #3d1428;
            background: linear-gradient(135deg, #ffd6ea, #ffb3d1);
            padding: 0.26rem 0.65rem;
            border-radius: 999px;
        }
        .bb-bs-body {
            padding: 0.85rem 1rem 1rem;
        }
        .bb-bs-name {
            font-size: 0.88rem;
            font-weight: 600;
            color: #fff8fb;
            margin: 0 0 0.3rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .bb-bs-price {
            font-size: 0.92rem;
            font-weight: 700;
            color: #ffb3d1;
            margin: 0;
        }

        /* ── Perks Row ── */
        .bb-perks-row {
            display: flex;
            align-items: stretch;
            justify-content: center;
            gap: 0;
            padding: 2.8rem clamp(1.2rem, 4vw, 3rem);
            background: #fff;
            border-top: 1px solid #f0d4e0;
            border-bottom: 1px solid #f0d4e0;
            flex-wrap: wrap;
        }
        .bb-perk {
            flex: 1;
            min-width: 180px;
            text-align: center;
            padding: 1.2rem 1.5rem;
        }
        .bb-perk-icon {
            font-size: 2rem;
            margin-bottom: 0.7rem;
            line-height: 1;
        }
        .bb-perk-title {
            font-size: 0.88rem;
            font-weight: 700;
            color: #3d1428;
            margin: 0 0 0.35rem;
        }
        .bb-perk-sub {
            font-size: 0.78rem;
            color: #9a7585;
            margin: 0;
            line-height: 1.55;
        }
        .bb-perk-divider {
            width: 1px;
            background: #f0d4e0;
            align-self: stretch;
            margin: 0.5rem 0;
            flex-shrink: 0;
        }
        @media (max-width: 640px) {
            .bb-perk-divider { display: none; }
        }

        /* ── Reviews ── */
        .bb-reviews-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.2rem;
        }
        @media (min-width: 640px) {
            .bb-reviews-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (min-width: 1024px) {
            .bb-reviews-grid {
                grid-template-columns: 2fr 1fr 1fr 1fr;
            }
        }
        .bb-review-card {
            background: #fff;
            border: 1px solid #f0d4e0;
            border-radius: 1.2rem;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            box-shadow: 0 4px 16px rgba(77,46,56,0.06);
            transition: transform 0.24s ease, box-shadow 0.24s ease;
        }
        .bb-review-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(77,46,56,0.12);
        }
        .bb-review-large {
            background: linear-gradient(135deg, #fff0f7 0%, #fff8fb 100%);
        }
        .bb-review-stars {
            font-size: 0.9rem;
            color: #f4a429;
            letter-spacing: 0.08em;
        }
        .bb-review-text {
            font-size: 0.88rem;
            line-height: 1.65;
            color: #4d2538;
            margin: 0;
            flex: 1;
            font-style: italic;
        }
        .bb-review-large .bb-review-text {
            font-size: 0.96rem;
        }
        .bb-review-author {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            margin-top: auto;
        }
        .bb-review-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e8609a, #c0437b);
            color: #fff;
            font-size: 0.78rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .bb-review-name {
            font-size: 0.82rem;
            font-weight: 700;
            color: #3d1428;
            margin: 0;
        }
        .bb-review-source {
            font-size: 0.68rem;
            color: #b09aa8;
            margin: 0;
        }

        /* ── CTA Band ── */
        .bb-cta-band {
            background: linear-gradient(118deg, #ffeaf4 0%, #fff2f8 50%, #ffd6ea 100%);
            border-top: 1px solid #f0d4e0;
            border-bottom: 1px solid #f0d4e0;
            text-align: center;
            padding: clamp(3rem, 6vw, 5rem) clamp(1.2rem, 4vw, 3rem);
        }
        .bb-cta-content {
            max-width: 560px;
            margin: 0 auto;
        }
        .bb-cta-heading {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.8rem, 3.5vw, 2.8rem);
            font-weight: 600;
            color: #3d1428;
            margin: 0 0 0.8rem;
        }
        .bb-cta-sub {
            font-size: 0.95rem;
            color: #8a6070;
            margin: 0 0 2rem;
            line-height: 1.65;
        }
        .bb-cta-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* ── Mobile overrides ── */
        @media (max-width: 768px) {
            .bb-hero-banner {
                height: clamp(360px, 65vw, 500px);
            }
            .bb-hero-text {
                padding: 2rem 1.4rem;
            }
            .bb-hero-overlay {
                background: linear-gradient(
                    160deg,
                    rgba(61, 20, 40, 0.9) 0%,
                    rgba(88, 32, 56, 0.75) 50%,
                    rgba(61, 20, 40, 0.4) 100%
                );
            }
            .bb-perks-row {
                padding: 2rem 1.2rem;
            }
        }
    </style>

    </section>

    <div id="shop"></div>
    <section id="cake-shelf" class="shelf-section" style="display:none;">
        <div class="shelf-header">
            <span class="shelf-label">Our Collection</span>
            <h2 class="shelf-title">The Display Case</h2>
            <p class="shelf-subtitle">Handcrafted cakes for every occasion</p>
        </div>

        <div class="shelf-toolbar">
            <div class="shelf-search">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" id="shelf-search-input" placeholder="Search products..." />
            </div>
            <div class="shelf-filters">
                <button class="shelf-filter-btn active" data-filter="all">All</button>
                @foreach($shelfProducts->pluck('category.name')->filter()->unique() as $catName)
                    <button class="shelf-filter-btn" data-filter="{{ Str::slug($catName) }}">{{ $catName }}</button>
                @endforeach
            </div>
            <select id="shelf-sort" class="shelf-sort">
                <option value="default">Sort by</option>
                <option value="price-low">Price: Low → High</option>
                <option value="price-high">Price: High → Low</option>
                <option value="name-az">Name: A → Z</option>
            </select>
        </div>

        <div class="shelf-layout">
            <aside id="menu-card" class="menu-card" style="display:none;">
                <button id="menu-card-close" class="menu-card-close" aria-label="Close">&times;</button>
                <div id="menu-card-img" class="menu-card-img"></div>
                <div class="menu-card-body">
                    <span id="menu-card-category" class="menu-card-category"></span>
                    <h3 id="menu-card-name" class="menu-card-name"></h3>
                    <p id="menu-card-desc" class="menu-card-desc"></p>
                    <div id="menu-card-price" class="menu-card-price"></div>
                    <div id="menu-card-variants" class="menu-card-variants" style="display:none;">
                        <label class="menu-card-variants-label">Select Variant</label>
                        <div id="menu-card-variants-list" class="menu-card-variants-list"></div>
                    </div>
                    <form id="menu-card-cart-form" method="POST" action="{{ route('cart.add') }}">
                        @csrf
                        <input type="hidden" name="product_id" id="menu-card-product-id" />
                        <input type="hidden" name="variant_id" id="menu-card-variant-id" />
                        <div class="menu-card-qty">
                            <button type="button" id="menu-card-qty-minus" class="qty-btn">−</button>
                            <input type="number" name="quantity" id="menu-card-qty" value="1" min="1" max="99" />
                            <button type="button" id="menu-card-qty-plus" class="qty-btn">+</button>
                        </div>
                        <button type="submit" id="menu-card-add-btn" class="menu-card-add-btn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13v8a2 2 0 002 2h10a2 2 0 002-2v-3"/></svg>
                            Add to Cart
                        </button>
                        <a id="menu-card-view-link" href="#" class="menu-card-view-link">View Full Details →</a>
                    </form>
                </div>
            </aside>

            <div class="shelf-display">
                <div class="shelf-glass-case">
                    <div class="case-frame case-frame-top"></div>
                    <div class="case-frame case-frame-bottom"></div>
                    <div class="case-frame case-frame-left"></div>
                    <div class="case-frame case-frame-right"></div>
                    <div class="case-interior-light case-interior-light-left"></div>
                    <div class="case-interior-light case-interior-light-right"></div>
                    <div class="case-reflection-sweep"></div>
                    @foreach($shelfProducts->chunk(3) as $rowProducts)
                    <div class="shelf-row">
                        <div class="shelf-light"></div>
                        <div class="shelf-items">
                            @foreach($rowProducts as $product)
                            <div class="shelf-item"
                                 data-product-id="{{ $product->id }}"
                                 data-name="{{ $product->name }}"
                                 data-slug="{{ $product->slug }}"
                                 data-desc="{{ Str::limit($product->description, 200) }}"
                                 data-price="{{ $product->effective_price }}"
                                 data-original-price="{{ $product->price }}"
                                 data-sale-price="{{ $product->sale_price }}"
                                 data-category="{{ Str::slug($product->category->name ?? '') }}"
                                 data-category-name="{{ $product->category->name ?? 'Uncategorized' }}"
                                 data-image="{{ $product->main_image_url ?? '/images/cakes/chocolate_3layer.png' }}"
                                 data-variants='{!! json_encode($product->variants->map(fn($v) => ["id" => $v->id, "name" => $v->name, "price" => $v->price, "stock" => $v->stock_quantity])) !!}'
                            >
                                <div class="cake-img-wrap">
                                    <img src="{{ $product->main_image_url ?? '/images/cakes/chocolate_3layer.png' }}" alt="{{ $product->name }}" class="cake-img" loading="lazy" />
                                </div>
                                <div class="price-tag">
                                    <span class="tag-flavor">{{ Str::limit($product->name, 18) }}</span>
                                    <span class="tag-layers">{{ $product->category->name ?? '' }}</span>
                                    <span class="tag-price">₱{{ number_format($product->effective_price, 0) }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="shelf-plank"></div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Cart Panel (right side) --}}
            <aside id="cart-panel" class="cart-panel" style="display:none;">
                {{-- Collapsed header (always visible when cart has items) --}}
                <button id="cart-panel-toggle" class="cart-panel-header" type="button" aria-expanded="false">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13v8a2 2 0 002 2h10a2 2 0 002-2v-3"/></svg>
                    <span>Your Cart</span>
                    <span id="cart-badge" class="cart-badge">0</span>
                    <svg class="cart-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                </button>

                {{-- Collapsible body --}}
                <div id="cart-panel-body" class="cart-panel-body">
                    {{-- Cake Box Visual --}}
                    <div class="cake-box">
                        <div class="cake-box-glass">
                            <div id="cake-box-items" class="cake-box-items">
                                {{-- Cake images rendered by JS --}}
                            </div>
                        </div>
                        <div class="cake-box-base"></div>
                    </div>

                    {{-- Cart Items List --}}
                    <div id="cart-items-list" class="cart-items-list">
                        {{-- Rendered by JS --}}
                    </div>

                    {{-- Order Summary --}}
                    <div class="cart-summary">
                        <div class="cart-summary-row"><span>Subtotal</span><span id="cart-subtotal">₱0</span></div>
                        <div class="cart-summary-row"><span>Delivery</span><span id="cart-delivery">₱5.99</span></div>
                        <div class="cart-summary-row"><span>Tax (10%)</span><span id="cart-tax">₱0</span></div>
                        <div class="cart-summary-divider"></div>
                        <div class="cart-summary-row cart-total"><span>Total</span><span id="cart-total">₱0</span></div>
                    </div>

                    <a href="/checkout" class="cart-checkout-btn">Proceed to Checkout</a>
                    <a href="/cart" class="cart-view-link">View Full Cart →</a>
                </div>
            </aside>
        </div>

        <div id="shelf-no-results" class="shelf-no-results" style="display:none;">
            <p>No products match your search.</p>
        </div>
    </section>

    <style>
        html,
        body {
            overflow-x: clip;
        }

        #app-shell main.container {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        /* ═══════════════════════════════════════════════════
           CAKE SCROLLYTELLING — PROFESSIONAL DARK THEME
           ═══════════════════════════════════════════════════ */

        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap');

        .cake-scroll-section {
            position: relative;
            width: 100%;
            margin-left: 0;
            height: 500vh;
            background: linear-gradient(
                180deg,
                #F88379 0%,
                #DE3163 12%,
                #150906 45%,
                #1a0e0a 70%,
                #0d0705 100%
            );
            overflow: clip;
        }

        .cake-sticky-panel {
            position: sticky;
            top: 0;
            height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .cake-canvas-container {
            position: absolute;
            inset: 0;
            z-index: 1;
        }

        .cake-canvas-container canvas {
            display: block;
            width: 100% !important;
            height: 100% !important;
        }

        /* Grain overlay */
        .cake-grain {
            position: absolute;
            inset: 0;
            z-index: 2;
            pointer-events: none;
            opacity: 0.04;
            background-image:
                radial-gradient(circle, rgba(212, 165, 116, 0.4) 1px, transparent 1px);
            background-size: 3px 3px;
            mix-blend-mode: overlay;
        }

        /* Vignette */
        .cake-vignette {
            position: absolute;
            inset: 0;
            z-index: 3;
            pointer-events: none;
            background: radial-gradient(
                ellipse 70% 60% at 50% 50%,
                transparent 30%,
                rgba(13, 7, 5, 0.55) 100%
            );
        }

        /* ─── Story Messages ─── */
        .story-messages {
            position: absolute;
            z-index: 10;
            inset: 0;
            pointer-events: none;
        }

        .story-msg {
            position: absolute;
            left: clamp(2rem, 6vw, 6rem);
            bottom: clamp(3rem, 8vh, 6rem);
            max-width: 30rem;
            pointer-events: none;
        }

        .story-label {
            display: inline-block;
            font-family: 'Inter', sans-serif;
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: #c9a84c;
            background: rgba(201, 168, 76, 0.06);
            border: 1px solid rgba(201, 168, 76, 0.12);
            padding: 0.35rem 0.95rem;
            border-radius: 999px;
            margin-bottom: 1rem;
        }

        .story-heading {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.8rem, 3.8vw, 3.2rem);
            font-weight: 500;
            line-height: 1.2;
            color: #f5ebe0;
            text-shadow: 0 4px 30px rgba(0, 0, 0, 0.6);
            margin: 0;
        }

        /* Final message — centered */
        .story-msg-final {
            left: 50% !important;
            bottom: auto !important;
            top: 50%;
            transform: translate(-50%, -50%) !important;
            text-align: center;
            max-width: 40rem;
        }

        .story-heading-final {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.2rem, 5vw, 4.2rem);
            font-weight: 600;
            line-height: 1.12;
            color: #f5ebe0;
            text-shadow: 0 4px 50px rgba(0, 0, 0, 0.7);
            margin: 0 0 0.8rem;
        }

        .story-sub {
            font-family: 'Inter', sans-serif;
            font-size: clamp(0.85rem, 1.2vw, 1.05rem);
            font-weight: 300;
            color: rgba(245, 235, 224, 0.45);
            letter-spacing: 0.08em;
            margin: 0 0 2.2rem;
        }

        .story-cta {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            font-family: 'Inter', sans-serif;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            text-decoration: none;
            color: #0d0705;
            background: linear-gradient(135deg, #c9a84c 0%, #e8cc6e 50%, #c9a84c 100%);
            padding: 0.95rem 2rem;
            border-radius: 999px;
            box-shadow:
                0 8px 32px rgba(201, 168, 76, 0.22),
                0 2px 8px rgba(0, 0, 0, 0.35);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            pointer-events: auto;
        }

        .story-cta:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow:
                0 14px 44px rgba(201, 168, 76, 0.32),
                0 4px 14px rgba(0, 0, 0, 0.35);
        }

        .story-cta svg {
            transition: transform 0.25s ease;
        }

        .story-cta:hover svg {
            transform: translateX(3px);
        }

        /* ─── Side Overlays (Signboard & Cupcake) ─── */
        .side-overlay {
            position: absolute;
            z-index: 12;
            pointer-events: none;
        }

        .side-overlay-left {
            left: clamp(1.5rem, 5vw, 5rem);
            top: 18%;
        }

        .side-overlay-right {
            right: clamp(1.5rem, 5vw, 5rem);
            top: 18%;
        }

        .side-overlay-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-family: 'Inter', sans-serif;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            text-decoration: none;
            padding: 0.7rem 1.4rem;
            border-radius: 999px;
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
            pointer-events: auto;
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }

        .side-overlay-btn-sign {
            color: #f5ebe0;
            background: rgba(181, 136, 74, 0.18);
            border: 1px solid rgba(201, 168, 76, 0.25);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .side-overlay-btn-sign:hover {
            background: rgba(201, 168, 76, 0.3);
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 8px 28px rgba(201, 168, 76, 0.2);
        }

        .side-overlay-btn-cupcake {
            color: #f5ebe0;
            background: rgba(255, 248, 238, 0.1);
            border: 1px solid rgba(255, 248, 238, 0.2);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .side-overlay-btn-cupcake:hover {
            background: rgba(255, 248, 238, 0.2);
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 8px 28px rgba(255, 248, 238, 0.12);
        }

        .side-overlay-btn svg {
            transition: transform 0.25s ease;
        }

        .side-overlay-btn:hover svg {
            transform: translateX(2px);
        }

        .side-overlay-btn-sign:hover svg {
            transform: rotate(15deg) scale(1.1);
        }

        /* ─── Scroll Indicator ─── */
        .scroll-indicator {
            position: absolute;
            top: clamp(7.5rem, 28vh, 13rem);
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.7rem;
            animation: scrollPulse 2.8s ease-in-out infinite;
        }

        .scroll-indicator span {
            font-family: 'Inter', sans-serif;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: rgba(255, 244, 250, 0.9);
            text-shadow: 0 2px 10px rgba(74, 31, 53, 0.45);
        }

        .scroll-mouse {
            width: 28px;
            height: 42px;
            border: 1.7px solid rgba(255, 244, 250, 0.75);
            border-radius: 11px;
            display: flex;
            justify-content: center;
            padding-top: 6px;
        }

        .scroll-wheel {
            width: 3px;
            height: 10px;
            background: rgba(255, 221, 238, 0.95);
            border-radius: 2px;
            animation: scrollWheel 2s ease-in-out infinite;
        }

        @keyframes scrollWheel {
            0% { transform: translateY(0); opacity: 1; }
            60% { transform: translateY(8px); opacity: 0; }
            100% { transform: translateY(0); opacity: 0; }
        }

        @keyframes scrollPulse {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }

        /* ─── Preloader ─── */
        .cake-preloader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0d0705;
        }

        .preloader-inner {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .preloader-ring {
            position: absolute;
            width: 90px;
            height: 90px;
            border: 1.5px solid transparent;
            border-top-color: #c9a84c;
            border-right-color: rgba(201, 168, 76, 0.25);
            border-radius: 50%;
            animation: preloaderSpin 1.1s linear infinite;
        }

        .preloader-text {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: #c9a84c;
            letter-spacing: 0.18em;
            animation: preloaderPulse 1.6s ease-in-out infinite;
        }

        @keyframes preloaderSpin {
            to { transform: rotate(360deg); }
        }

        @keyframes preloaderPulse {
            0%, 100% { opacity: 0.45; }
            50% { opacity: 1; }
        }

        /* ─── Responsive ─── */
        @media (max-width: 768px) {
            .story-msg {
                left: 50% !important;
                bottom: clamp(2rem, 6vh, 4rem) !important;
                transform: translateX(-50%) !important;
                text-align: center;
                max-width: 88vw;
                padding: 0 1rem;
            }

            .story-msg-final {
                bottom: auto !important;
                top: 50%;
                transform: translate(-50%, -50%) !important;
            }

            .story-label {
                font-size: 0.58rem;
            }

            .story-heading {
                font-size: clamp(1.5rem, 6.5vw, 2.4rem);
            }

            .story-heading-final {
                font-size: clamp(1.8rem, 7.5vw, 2.8rem);
            }

            .story-sub {
                font-size: 0.82rem;
            }

            .story-cta {
                font-size: 0.68rem;
                padding: 0.8rem 1.6rem;
            }

            .cake-scroll-section {
                height: 550vh;
            }

            .side-overlay-left,
            .side-overlay-right {
                top: auto;
                bottom: 12%;
                left: 50% !important;
                right: auto !important;
                transform: translateX(-50%);
            }

            .side-overlay-left {
                bottom: 18%;
            }

            .side-overlay-right {
                bottom: 10%;
            }

            .side-overlay-btn {
                font-size: 0.62rem;
                padding: 0.6rem 1.2rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .cake-scroll-section *,
            .cake-scroll-section *::before,
            .cake-scroll-section *::after {
                animation-duration: 0.001ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.001ms !important;
            }
        }

        /* ─── Sticky Navbar Override ─── */
        header {
            position: sticky !important;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    
        /* Pink Theme Overrides */
        :root {
            --bb-pink-50: #fff9fc;
            --bb-pink-100: #ffeef6;
            --bb-pink-200: #ffd7e9;
            --bb-pink-300: #ffbedd;
            --bb-pink-400: #f8a2cb;
            --bb-pink-500: #ed86b8;
            --bb-pink-600: #db699f;
            --bb-pink-700: #c65187;
            --bb-pink-800: #a83d70;
            --bb-plum-900: #5a2f46;
            --bb-rose-900: #6c3751;
            --bb-text: #4d2b3d;
            --bb-soft-text: rgba(77, 43, 61, 0.72);
            --bb-accent: #f49ac4;
        }

        .cake-scroll-section {
            background: linear-gradient(
                180deg,
                #fff8fc 0%,
                #ffe9f4 24%,
                #ffd3e8 54%,
                #ffc0de 78%,
                #f6afd2 100%
            ) !important;
        }

        .cake-grain {
            opacity: 0.04 !important;
            background-image: radial-gradient(circle, rgba(243, 160, 198, 0.28) 1px, transparent 1px) !important;
        }

        .cake-vignette {
            background: radial-gradient(
                ellipse 72% 62% at 50% 50%,
                rgba(255, 255, 255, 0.0) 36%,
                rgba(232, 167, 200, 0.34) 100%
            ) !important;
        }

        .cake-canvas-container canvas {
            filter: saturate(1.03) brightness(1.08);
        }

        .story-label {
            color: #7b3757 !important;
            background: rgba(255, 236, 246, 0.88) !important;
            border-color: rgba(219, 137, 179, 0.36) !important;
        }

        .story-heading,
        .story-heading-final {
            color: #fff9fd !important;
            text-shadow:
                0 2px 8px rgba(74, 31, 53, 0.65),
                0 8px 20px rgba(74, 31, 53, 0.45) !important;
        }

        .story-sub {
            color: rgba(255, 247, 252, 0.94) !important;
            text-shadow: 0 2px 8px rgba(74, 31, 53, 0.35) !important;
        }

        .story-cta {
            color: #5b2440 !important;
            background: linear-gradient(135deg, #fff7fb 0%, #ffd8ea 52%, #f6a7cb 100%) !important;
            box-shadow: 0 10px 30px rgba(237, 134, 184, 0.34), 0 4px 14px rgba(198, 93, 145, 0.25) !important;
        }

        .story-cta:hover {
            box-shadow: 0 14px 40px rgba(237, 134, 184, 0.44), 0 4px 16px rgba(198, 93, 145, 0.35) !important;
        }

        .side-overlay-btn-sign {
            color: var(--bb-pink-50) !important;
            background: rgba(248, 106, 162, 0.24) !important;
            border-color: rgba(255, 188, 217, 0.42) !important;
        }

        .side-overlay-btn-sign:hover {
            background: rgba(248, 106, 162, 0.34) !important;
            box-shadow: 0 8px 28px rgba(248, 106, 162, 0.28) !important;
        }

        .side-overlay-btn-cupcake {
            color: var(--bb-pink-50) !important;
            background: rgba(255, 214, 234, 0.16) !important;
            border-color: rgba(255, 214, 234, 0.34) !important;
        }

        .side-overlay-btn-cupcake:hover {
            background: rgba(255, 214, 234, 0.26) !important;
            box-shadow: 0 8px 28px rgba(255, 186, 215, 0.2) !important;
        }

        .scroll-indicator span {
            color: rgba(102, 53, 75, 0.72) !important;
        }

        .scroll-mouse {
            border-color: rgba(180, 97, 138, 0.45) !important;
        }

        .scroll-wheel {
            background: var(--bb-accent) !important;
        }

        .cake-preloader {
            background: var(--bb-rose-900) !important;
        }

        .preloader-ring {
            border-top-color: #ff9cc4 !important;
            border-right-color: rgba(255, 156, 196, 0.3) !important;
        }

        .preloader-text {
            color: #ffd2e6 !important;
        }

        .cake-img {
            filter: saturate(1.06) hue-rotate(-8deg) contrast(1.03) brightness(1.02);
        }



        .price-tag {
            background: linear-gradient(145deg, rgba(255, 221, 235, 0.93), rgba(255, 188, 217, 0.9)) !important;
            color: #5a1f3b !important;
            border-color: rgba(186, 58, 109, 0.25) !important;
        }

        .tag-flavor,
        .tag-layers {
            color: #6b2645 !important;
        }

        .tag-price {
            color: #8f2a53 !important;
        }

        header {
            background: rgba(255, 242, 249, 0.86) !important;
        }
    </style>
    @if (file_exists(public_path('css/shelf.css')))
        <link rel="stylesheet" href="/css/shelf.css">
    @endif

    @push('scripts')
        @vite('resources/js/cake-entry.js')

        <script>
            window.__cakeProducts = @json($shelfProducts->values());
        </script>

        <script>
            // Fallback: never let preloader block the page if a script fails.
            (() => {
                const hidePreloader = () => {
                    const preloader = document.getElementById('cake-preloader');
                    if (!preloader) return;
                    preloader.style.opacity = '0';
                    preloader.style.visibility = 'hidden';
                    setTimeout(() => preloader.remove(), 500);
                };

                window.addEventListener('load', hidePreloader, { once: true });
                setTimeout(hidePreloader, 2200);
            })();
        </script>

        

        <script>
            // ═══ Shelf: Reveal, Search, Filter, Sort + Menu Card ═══
            (() => {
                const shelf = document.getElementById('cake-shelf');
                if (!shelf) return;

                // --- Reveal showcase ---
                function revealShelf(e) {
                    if (e) e.preventDefault();
                    const showcase = document.getElementById('home-showcase');
                    if (showcase) {
                        if (showcase.style.display === 'none') {
                            showcase.style.display = '';
                        }
                        showcase.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }

                const ctaBtn = document.getElementById('story-cta-btn');
                if (ctaBtn) ctaBtn.addEventListener('click', revealShelf);

                // --- Search & Filter ---
                const searchInput = document.getElementById('shelf-search-input');
                const items = shelf.querySelectorAll('.shelf-item');
                const noResults = document.getElementById('shelf-no-results');

                function applyFilters() {
                    const query = (searchInput?.value || '').toLowerCase();
                    const activeFilter = shelf.querySelector('.shelf-filter-btn.active')?.dataset.filter || 'all';
                    let visible = 0;
                    items.forEach(item => {
                        const name = (item.dataset.name || '').toLowerCase();
                        const cat = item.dataset.category || '';
                        const matchSearch = !query || name.includes(query) || cat.includes(query);
                        const matchFilter = activeFilter === 'all' || cat === activeFilter;
                        const show = matchSearch && matchFilter;
                        item.classList.toggle('hidden', !show);
                        if (show) visible++;
                    });
                    if (noResults) noResults.style.display = visible === 0 ? '' : 'none';
                }

                if (searchInput) searchInput.addEventListener('input', applyFilters);

                shelf.querySelectorAll('.shelf-filter-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        shelf.querySelectorAll('.shelf-filter-btn').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        applyFilters();
                    });
                });

                // --- Sort ---
                const sortSelect = document.getElementById('shelf-sort');
                if (sortSelect) {
                    sortSelect.addEventListener('change', () => {
                        const val = sortSelect.value;
                        const rows = shelf.querySelectorAll('.shelf-row');
                        rows.forEach(row => {
                            const container = row.querySelector('.shelf-items');
                            const rowItems = [...container.querySelectorAll('.shelf-item')];
                            rowItems.sort((a, b) => {
                                const pa = parseFloat(a.dataset.price), pb = parseFloat(b.dataset.price);
                                const na = (a.dataset.name || ''), nb = (b.dataset.name || '');
                                if (val === 'price-low') return pa - pb;
                                if (val === 'price-high') return pb - pa;
                                if (val === 'name-az') return na.localeCompare(nb);
                                return 0;
                            });
                            rowItems.forEach(item => container.appendChild(item));
                        });
                    });
                }

                // ═══ Menu Card ═══
                const card = document.getElementById('menu-card');
                const cardClose = document.getElementById('menu-card-close');
                const cardImg = document.getElementById('menu-card-img');
                const cardCategory = document.getElementById('menu-card-category');
                const cardName = document.getElementById('menu-card-name');
                const cardDesc = document.getElementById('menu-card-desc');
                const cardPrice = document.getElementById('menu-card-price');
                const cardVariants = document.getElementById('menu-card-variants');
                const cardVariantsList = document.getElementById('menu-card-variants-list');
                const cardProductId = document.getElementById('menu-card-product-id');
                const cardVariantId = document.getElementById('menu-card-variant-id');
                const cardQty = document.getElementById('menu-card-qty');
                const cardViewLink = document.getElementById('menu-card-view-link');
                const cartForm = document.getElementById('menu-card-cart-form');

                function openMenuCard(item) {
                    const productId = item.dataset.productId;
                    const name = item.dataset.name;
                    const slug = item.dataset.slug;
                    const desc = item.dataset.desc;
                    const price = parseFloat(item.dataset.price);
                    const originalPrice = parseFloat(item.dataset.originalPrice);
                    const salePrice = item.dataset.salePrice;
                    const catName = item.dataset.categoryName;
                    const image = item.dataset.image;
                    let variants = [];
                    try { variants = JSON.parse(item.dataset.variants || '[]'); } catch(e) {}

                    cardProductId.value = productId;

                    // Populate card
                    cardImg.innerHTML = `<img src="${image}" alt="${name}" />`;
                    cardCategory.textContent = catName;
                    cardName.textContent = name;
                    cardDesc.textContent = desc || 'A delicious handcrafted treat from BonBons PH.';

                    // Price
                    if (salePrice && parseFloat(salePrice) > 0 && parseFloat(salePrice) < originalPrice) {
                        cardPrice.innerHTML = `₱${Number(salePrice).toLocaleString()} <span class="original-price">₱${Number(originalPrice).toLocaleString()}</span>`;
                    } else {
                        cardPrice.textContent = `₱${Number(price).toLocaleString()}`;
                    }

                    // Variants
                    if (variants.length > 0) {
                        cardVariants.style.display = '';
                        cardVariantsList.innerHTML = '';
                        variants.forEach((v, i) => {
                            const chip = document.createElement('button');
                            chip.type = 'button';
                            chip.className = 'variant-chip' + (i === 0 ? ' active' : '') + (v.stock <= 0 ? ' out-of-stock' : '');
                            chip.textContent = v.name + (v.price ? ` (₱${Number(v.price).toLocaleString()})` : '');
                            chip.dataset.variantId = v.id;
                            chip.addEventListener('click', () => {
                                cardVariantsList.querySelectorAll('.variant-chip').forEach(c => c.classList.remove('active'));
                                chip.classList.add('active');
                                cardVariantId.value = v.id;
                            });
                            cardVariantsList.appendChild(chip);
                        });
                        cardVariantId.value = variants[0].id;
                    } else {
                        cardVariants.style.display = 'none';
                        cardVariantId.value = '';
                    }

                    cardQty.value = 1;
                    cardViewLink.href = `/product/${slug}`;

                    // Highlight selected item
                    items.forEach(i => i.classList.remove('shelf-item-active'));
                    item.classList.add('shelf-item-active');

                    card.style.display = '';
                }

                function closeMenuCard() {
                    card.style.display = 'none';
                    items.forEach(i => i.classList.remove('shelf-item-active'));
                }

                // Click on cake → open card
                items.forEach(item => {
                    item.addEventListener('click', () => openMenuCard(item));
                });

                // Close button
                if (cardClose) cardClose.addEventListener('click', closeMenuCard);

                // Qty +/-
                document.getElementById('menu-card-qty-minus')?.addEventListener('click', () => {
                    const v = parseInt(cardQty.value) || 1;
                    if (v > 1) cardQty.value = v - 1;
                });
                document.getElementById('menu-card-qty-plus')?.addEventListener('click', () => {
                    const v = parseInt(cardQty.value) || 1;
                    if (v < 99) cardQty.value = v + 1;
                });

                // Add to cart via AJAX → show cart panel
                const cartPanel = document.getElementById('cart-panel');
                const cartPanelToggle = document.getElementById('cart-panel-toggle');
                const cartPanelBody = document.getElementById('cart-panel-body');
                const cartBadge = document.getElementById('cart-badge');
                const cakeBoxItems = document.getElementById('cake-box-items');
                const cartItemsList = document.getElementById('cart-items-list');
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                // Toggle expand/collapse
                function toggleCartPanel() {
                    const expanded = cartPanelToggle.getAttribute('aria-expanded') === 'true';
                    cartPanelToggle.setAttribute('aria-expanded', !expanded);
                    cartPanelBody.classList.toggle('expanded', !expanded);
                }
                function expandCartPanel() {
                    cartPanelToggle.setAttribute('aria-expanded', 'true');
                    cartPanelBody.classList.add('expanded');
                }
                function collapseCartPanel() {
                    cartPanelToggle.setAttribute('aria-expanded', 'false');
                    cartPanelBody.classList.remove('expanded');
                }

                if (cartPanelToggle) cartPanelToggle.addEventListener('click', toggleCartPanel);

                function fmt(n) { return '₱' + Number(n).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}); }

                function renderCartPanel(data, autoExpand) {
                    // Hide cart completely if 0 items
                    if (data.count === 0) {
                        cartPanel.style.display = 'none';
                        collapseCartPanel();
                        return;
                    }

                    cartPanel.style.display = '';
                    cartBadge.textContent = data.count;

                    // Cake box images
                    if (data.items.length === 0) {
                        cakeBoxItems.innerHTML = '<div class="cake-box-empty">Your box is empty</div>';
                    } else {
                        cakeBoxItems.innerHTML = data.items.map(i =>
                            `<img src="${i.image || '/images/cakes/chocolate_3layer.png'}" alt="${i.name}" title="${i.name} x${i.quantity}" />`
                        ).join('');
                    }

                    // Item list
                    cartItemsList.innerHTML = data.items.map(i => `
                        <div class="cart-list-item" data-item-id="${i.id}">
                            <img src="${i.image || '/images/cakes/chocolate_3layer.png'}" alt="${i.name}" />
                            <div class="cart-list-info">
                                <div class="cart-list-name">${i.name}</div>
                                <div class="cart-list-variant">${i.variant}</div>
                            </div>
                            <div class="cart-list-qty">
                                <button onclick="cartQtyChange(${i.id},'dec')">−</button>
                                <span>${i.quantity}</span>
                                <button onclick="cartQtyChange(${i.id},'inc')">+</button>
                            </div>
                            <div class="cart-list-price">${fmt(i.subtotal)}</div>
                            <button class="cart-list-remove" onclick="cartRemove(${i.id})" title="Remove">×</button>
                        </div>
                    `).join('');

                    // Summary
                    document.getElementById('cart-subtotal').textContent = fmt(data.subtotal);
                    document.getElementById('cart-delivery').textContent = fmt(data.delivery);
                    document.getElementById('cart-tax').textContent = fmt(data.tax);
                    document.getElementById('cart-total').textContent = fmt(data.total);

                    // Auto-expand on add-to-cart, stay collapsed on load
                    if (autoExpand) expandCartPanel();
                }

                // Load initial cart (collapsed)
                fetch('/cart/json', { headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(d => { if (d.count > 0) renderCartPanel(d, false); })
                    .catch(() => {});

                // AJAX add to cart
                if (cartForm) {
                    cartForm.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        if (!cardVariantId.value) {
                            if (window.BonbonNotify) { window.BonbonNotify('warning', 'Please select a variant first.'); } else { alert('Please select a variant first.'); }
                            return;
                        }

                        // Show loader on menu card
                        if (window.BonBonLoader) BonBonLoader.show('#menu-card', 'Adding to cart...');

                        try {
                            const fd = new FormData(cartForm);
                            const res = await fetch(cartForm.action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: fd,
                            });
                            const data = await res.json();
                            renderCartPanel(data, true);
                            closeMenuCard();
                        } catch (err) {
                            console.error('Add to cart failed:', err);
                        } finally {
                            if (window.BonBonLoader) BonBonLoader.hide('#menu-card');
                        }
                    });
                }

                // Cart qty +/- and remove (with loading)
                window.cartQtyChange = async function(itemId, dir) {
                    if (window.BonBonLoader) BonBonLoader.show('#cart-panel', 'Updating...');
                    const url = `/cart/${itemId}/${dir === 'inc' ? 'increment' : 'decrement'}`;
                    try {
                        await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } });
                        const res = await fetch('/cart/json', { headers: { 'Accept': 'application/json' } });
                        const data = await res.json();
                        renderCartPanel(data, false);
                    } catch(e) { console.error(e); }
                    finally { if (window.BonBonLoader) BonBonLoader.hide('#cart-panel'); }
                };

                window.cartRemove = async function(itemId) {
                    if (window.BonBonLoader) BonBonLoader.show('#cart-panel', 'Removing...');
                    try {
                        await fetch(`/cart/${itemId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' } });
                        const res = await fetch('/cart/json', { headers: { 'Accept': 'application/json' } });
                        const data = await res.json();
                        renderCartPanel(data, false);
                    } catch(e) { console.error(e); }
                    finally { if (window.BonBonLoader) BonBonLoader.hide('#cart-panel'); }
                };

            })();
        </script>
    @endpush
@endsection

