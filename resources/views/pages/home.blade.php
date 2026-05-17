@extends('layouts.app')

@section('hideGlobalLoader', true)
@section('hideChatbot', true)

@php
    $cakesCategory = $featuredCategories->first(fn ($category) => str_contains(strtolower($category->name), 'cake'));
    $cakesUrl = url('/#shop');
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
                    <p class="story-sub">Three layers. One unforgettable moment.</p>
                    <a id="story-cta-btn" href="#cake-shelf" class="story-cta" style="opacity: 0;">
                        Explore Our Cakes
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    </a>
                </div>
            </div>

            {{-- Signboard Overlay (top-left) --}}
            <div id="signboard-overlay" class="side-overlay side-overlay-left" style="opacity: 0;">
                <a href="{{ url('/#shop') }}" class="side-overlay-btn side-overlay-btn-sign">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 L15.09 8.26 L22 9.27 L17 14.14 L18.18 21.02 L12 17.77 L5.82 21.02 L7 14.14 L2 9.27 L8.91 8.26Z"/></svg>
                    Recommendations
                </a>
            </div>

            {{-- Cupcake Overlay (top-right) --}}
            <div id="cupcake-overlay" class="side-overlay side-overlay-right" style="opacity: 0;">
                <a href="{{ url('/#shop') }}" class="side-overlay-btn side-overlay-btn-cupcake">
                    Explore Our Pastries
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </a>
            </div>

            {{-- Scroll indicator --}}
            <div class="scroll-indicator" id="scroll-indicator">
                <div class="scroll-mouse">
                    <div class="scroll-wheel"></div>
                </div>
                <span>Scroll to discover</span>
            </div>
        </div>
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
        /* ═══════════════════════════════════════════════════
           CAKE SCROLLYTELLING — PROFESSIONAL DARK THEME
           ═══════════════════════════════════════════════════ */

        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap');

        .cake-scroll-section {
            position: relative;
            width: 100vw;
            margin-left: calc(-50vw + 50%);
            height: 500vh;
            background: linear-gradient(
                180deg,
                #0d0705 0%,
                #1a0e0a 12%,
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
            bottom: 2rem;
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
            font-size: 0.6rem;
            font-weight: 500;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: rgba(245, 235, 224, 0.25);
        }

        .scroll-mouse {
            width: 20px;
            height: 32px;
            border: 1.5px solid rgba(245, 235, 224, 0.18);
            border-radius: 11px;
            display: flex;
            justify-content: center;
            padding-top: 6px;
        }

        .scroll-wheel {
            width: 2px;
            height: 7px;
            background: rgba(201, 168, 76, 0.45);
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
    </style>
    <link rel="stylesheet" href="/css/shelf.css">

    @push('scripts')
        @vite('resources/js/cake-entry.js')

        <script>
            // Hide scroll indicator after first scroll
            (() => {
                const indicator = document.getElementById('scroll-indicator');
                if (!indicator) return;

                let hidden = false;
                window.addEventListener('scroll', () => {
                    if (!hidden && window.scrollY > 100) {
                        indicator.style.transition = 'opacity 0.5s ease';
                        indicator.style.opacity = '0';
                        hidden = true;
                    }
                }, { passive: true });
            })();
        </script>

        <script>
            // ═══ Shelf: Reveal, Search, Filter, Sort + Menu Card ═══
            (() => {
                const shelf = document.getElementById('cake-shelf');
                if (!shelf) return;

                // --- Reveal shelf ---
                function revealShelf(e) {
                    if (e) e.preventDefault();
                    shelf.style.display = '';
                    shelf.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }

                const ctaBtn = document.getElementById('story-cta-btn');
                if (ctaBtn) ctaBtn.addEventListener('click', revealShelf);

                document.querySelectorAll('a').forEach(a => {
                    if (a.textContent.trim() === 'Shop' && a.closest('header')) {
                        a.addEventListener('click', (e) => {
                            e.preventDefault();
                            revealShelf(e);
                        });
                    }
                });

                // Auto-reveal shelf if URL has #shop
                if (window.location.hash === '#shop') {
                    setTimeout(() => revealShelf(), 500);
                }

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
                            alert('Please select a variant first.');
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
