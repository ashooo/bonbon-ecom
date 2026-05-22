@extends('layouts.app')

@section('hideChatbot')
@endsection

@section('content')
<style>
    body {
        background: #fff5f8;
    }
    .android-customize {
        min-height: 100svh;
        margin: -2rem -1rem;
        padding: 0 0 6rem;
        background:
            radial-gradient(circle at 85% 6%, rgba(255,255,255,.95), transparent 18rem),
            radial-gradient(circle at 5% 38%, rgba(236,90,97,.16), transparent 16rem),
            linear-gradient(180deg, #fff9fb 0%, #ffe8f0 46%, #fff7f4 100%);
        color: #4f3338;
        overflow-x: hidden;
    }
    .android-hero {
        min-height: calc(100svh - 5rem);
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: .85rem;
    }
    .android-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding: .3rem .2rem 0;
    }
    .android-brand-pill,
    .android-hint-pill {
        border: 1px solid rgba(255,255,255,.76);
        background: rgba(255,255,255,.72);
        box-shadow: 0 12px 28px rgba(90,58,58,.08);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
    }
    .android-preview-card {
        position: relative;
        flex: 1;
        min-height: 520px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,.86);
        border-radius: 2rem;
        background: linear-gradient(160deg, rgba(255,255,255,.72), rgba(255,228,237,.7));
        box-shadow: 0 28px 60px rgba(90,58,58,.16);
    }
    #cake-3d-canvas {
        height: 100%;
        width: 100%;
        cursor: grab;
        touch-action: none;
    }
    #spin-hint,
    #filling-badge {
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
    }
    .android-view-tabs {
        position: sticky;
        bottom: .8rem;
        z-index: 30;
        display: flex;
        gap: .5rem;
        overflow-x: auto;
        margin: 0 1rem;
        padding: .5rem;
        border: 1px solid rgba(243,215,221,.94);
        border-radius: 1.35rem;
        background: rgba(255,255,255,.84);
        box-shadow: 0 16px 40px rgba(90,58,58,.15);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        scrollbar-width: none;
    }
    .android-view-tabs::-webkit-scrollbar,
    .android-chip-row::-webkit-scrollbar {
        display: none;
    }
    .android-view-tabs button {
        flex: 0 0 auto;
        min-width: max-content;
        border-radius: 1rem;
        padding: .8rem .95rem;
        font-size: .8rem;
        font-weight: 800;
        transition: transform .18s ease, background-color .18s ease, color .18s ease;
        touch-action: manipulation;
    }
    .android-view-tabs button:active,
    .android-card button:active {
        transform: scale(.97);
    }
    .android-panel {
        padding: 1rem;
    }
    .android-card {
        border: 1px solid rgba(243,215,221,.9);
        border-radius: 1.55rem;
        background: rgba(255,255,255,.88);
        box-shadow: 0 16px 36px rgba(90,58,58,.1);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
    .android-field {
        display: grid;
        gap: .45rem;
    }
    .android-field label {
        font-size: .72rem;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #7a5258;
    }
    .android-field select,
    .android-field textarea,
    .android-field input[type='number'],
    .android-field input[type='color'] {
        width: 100%;
        border: 1px solid #f3d7db;
        border-radius: 1rem;
        background: #fff7f7;
        padding: .9rem .95rem;
        font-size: .95rem;
        font-weight: 750;
        color: #57393f;
        outline: none;
        transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
    }
    .android-field select:focus,
    .android-field textarea:focus,
    .android-field input:focus {
        border-color: #ec5a61;
        box-shadow: 0 0 0 4px rgba(236,90,97,.13);
        background: #fff;
    }
    .android-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .75rem;
    }
    .android-chip-row {
        display: flex;
        gap: .55rem;
        overflow-x: auto;
        padding-bottom: .15rem;
        scrollbar-width: none;
    }
    .android-swatch {
        flex: 0 0 auto;
        width: 2.8rem;
        height: 2.8rem;
        border-radius: 999px;
        border: 2px solid rgba(90,58,58,.12);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.5), 0 7px 16px rgba(90,58,58,.08);
    }
    .android-swatch.is-active {
        border-color: #ec5a61;
        box-shadow: 0 0 0 4px rgba(236,90,97,.14), 0 8px 18px rgba(90,58,58,.12);
    }
    .android-preset-btn,
    .android-shape-btn {
        border: 1px solid #f3d7db;
        border-radius: 1rem;
        background: #fff7f7;
        padding: .78rem .7rem;
        font-size: .8rem;
        font-weight: 800;
        color: #7a5252;
    }
    .android-preset-btn.is-active {
        border-color: #ec5a61;
        background: #fdecef;
        color: #5a3a3a;
    }
    .android-sticky-submit {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 45;
        padding: .75rem 1rem max(.75rem, env(safe-area-inset-bottom));
        background: linear-gradient(180deg, rgba(255,245,248,0), rgba(255,245,248,.94) 24%, rgba(255,245,248,.98));
    }
    .android-submit-btn {
        width: 100%;
        border-radius: 1.15rem;
        background: linear-gradient(135deg, #ec5a61, #f06292);
        padding: 1rem 1.1rem;
        font-size: 1rem;
        font-weight: 950;
        color: #fff;
        box-shadow: 0 16px 34px rgba(236,90,97,.28);
    }
    .color-pill {
        appearance: none;
        -webkit-appearance: none;
        overflow: hidden;
        padding: 0 !important;
    }
    .color-pill::-webkit-color-swatch-wrapper { padding: 0; }
    .color-pill::-webkit-color-swatch { border: none; border-radius: 1rem; }
    .color-pill::-moz-color-swatch { border: none; border-radius: 1rem; }
    @media (max-width: 380px) {
        .android-preview-card { min-height: 460px; }
        .android-grid-2 { grid-template-columns: 1fr; }
    }
</style>

<div id="test-customize-page" class="android-customize">
    <section class="android-hero" aria-label="Cake preview">
        <div class="android-topbar">
            <div class="android-brand-pill rounded-full px-4 py-2">
                <p class="text-[10px] font-black uppercase tracking-[.22em] text-pink-600">Bonbon Cake Studio</p>
            </div>
            <a href="{{ route('customize.index') }}?desktop=1" class="android-hint-pill rounded-full px-3 py-2 text-[11px] font-bold text-[#7A5252]">Classic UI</a>
        </div>

        <div id="cake-3d-wrap" class="android-preview-card">
            <div id="cake-3d-canvas"></div>
            <div id="spin-hint" class="absolute right-4 top-4 rounded-full border border-white/70 bg-white/82 px-3 py-2 text-[11px] font-bold text-[#7A5252] shadow-md">✦ Swipe to rotate</div>
            <div id="filling-badge" class="absolute bottom-4 right-4 hidden max-w-[12rem] rounded-2xl border border-white/70 bg-white/92 px-4 py-3 shadow-lg">
                <div class="mb-1 text-[10px] font-bold uppercase tracking-wider text-[#5A3A3A]">Inside Filling</div>
                <div id="filling-badge-name" class="truncate text-sm font-bold text-[#7A5252]"></div>
                <div id="filling-badge-swatch" class="mt-2 h-3 w-full rounded-full" style="background:#6a3d2d"></div>
            </div>
        </div>

        <div class="px-1 pb-1 text-center">
            <h1 class="text-3xl font-black leading-tight text-[#4f3338]">Design your dream cake</h1>
            <p class="mx-auto mt-2 max-w-[22rem] text-sm leading-relaxed text-[#86646a]">Preview first, then scroll down to customize every detail smoothly.</p>
        </div>
    </section>

    <div id="customize-view-tabs" class="android-view-tabs" aria-label="Cake view controls">
        <button id="tab-view-front" data-cake-view="auto" type="button" class="border border-[#ec5a61] bg-pink-600 text-white">360 View</button>
        <button id="tab-view-top" data-cake-view="top" type="button" class="border border-[#F3D7DB] bg-white/90 text-[#6E4D53]">Top View</button>
        <button id="tab-view-side" data-cake-view="side" type="button" class="border border-[#F3D7DB] bg-white/90 text-[#6E4D53]">Side View</button>
        <button id="tab-view-inside" data-cake-view="inside" type="button" class="border border-pink-300 bg-pink-50 text-pink-600">🍰 Inside</button>
    </div>

    <section class="android-panel" aria-label="Cake customization form">
        <form method="POST" action="{{ route('cart.add') }}" id="cake-builder-form" class="space-y-4">
            @csrf
            <input type="hidden" id="builder-toppings-hidden" name="customization[toppings]" value="[]">
            <input type="hidden" id="builder-preview-svg-hidden" name="customization[preview_svg]" value="">
            <input type="hidden" id="builder-frosting" name="customization[frosting]" value="ivory">
            <input type="hidden" id="builder-frosting-custom-hidden" name="customization[frosting_custom]" value="">
            <input type="hidden" id="builder-rush-hidden" name="customization[rush]" value="no">

            <div class="android-card p-5">
                <p class="text-xs font-black uppercase tracking-[.2em] text-pink-600">Step 1</p>
                <h2 class="mt-1 text-2xl font-black text-[#4f3338]">Cake foundation</h2>
                <p class="mt-1 text-sm text-[#86646a]">Pick the base, size, tiers, and inside flavors.</p>

                <div class="mt-5 space-y-4">
                    <div class="android-grid-2">
                        <div class="android-field">
                            <label for="builder-shape">Shape</label>
                            <select id="builder-shape" name="customization[shape]">
                                <option value="Round">Round</option>
                                <option value="Square">Square</option>
                                <option value="Heart">Heart</option>
                            </select>
                        </div>
                        <div class="android-field">
                            <label for="builder-size">Base size</label>
                            <select id="builder-size" name="customization[size]">
                                <option value="6">6 inches</option>
                                <option value="8">8 inches</option>
                                <option value="10">10 inches</option>
                                <option value="12">12 inches</option>
                            </select>
                        </div>
                    </div>

                    <div class="android-grid-2">
                        <div class="android-field">
                            <label for="builder-layers">Tiers</label>
                            <select id="builder-layers" name="customization[layers]">
                                <option value="1">1 tier</option>
                                <option value="2">2 tiers</option>
                                <option value="3">3 tiers</option>
                                <option value="4">4 tiers</option>
                            </select>
                        </div>
                        <div class="android-field">
                            <label for="builder-drip">Drip</label>
                            <select id="builder-drip" name="customization[drip]">
                                <option value="none">No drip</option>
                                <option value="chocolate">Chocolate drip</option>
                                <option value="white_chocolate">White chocolate</option>
                                <option value="pink">Pink drip</option>
                                <option value="caramel">Caramel drip</option>
                            </select>
                        </div>
                    </div>

                    <div class="android-field">
                        <label for="builder-sponge">Cake flavor</label>
                        <select id="builder-sponge" name="customization[sponge]">
                            <option value="Vanilla">Vanilla</option>
                            <option value="Chocolate">Chocolate</option>
                            <option value="Red Velvet">Red Velvet</option>
                            <option value="Lemon">Lemon</option>
                            <option value="Strawberry">Strawberry</option>
                            <option value="Funfetti">Funfetti</option>
                        </select>
                    </div>

                    <div class="android-field">
                        <label for="builder-filling">Filling</label>
                        <select id="builder-filling" name="customization[filling]">
                            <option value="Chocolate Mousse">Chocolate mousse</option>
                            <option value="Strawberry Jam">Strawberry jam</option>
                            <option value="Vanilla Cream">Vanilla cream</option>
                            <option value="Nutella">Nutella</option>
                            <option value="Cookies & Cream">Cookies & cream</option>
                            <option value="Buttercream">Buttercream</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="android-card p-5">
                <p class="text-xs font-black uppercase tracking-[.2em] text-pink-600">Step 2</p>
                <h2 class="mt-1 text-2xl font-black text-[#4f3338]">Color & style</h2>

                <div class="mt-5 space-y-4">
                    <div>
                        <div class="mb-2 text-xs font-black uppercase tracking-[.08em] text-[#7a5258]">Frosting color</div>
                        <div id="builder-frosting-swatches" class="android-chip-row">
                            <button type="button" data-frosting="white" class="android-swatch frosting-swatch" style="background:#f2f2f2" aria-label="White frosting"></button>
                            <button type="button" data-frosting="ivory" class="android-swatch frosting-swatch is-active" style="background:#e9e2cf" aria-label="Ivory frosting"></button>
                            <button type="button" data-frosting="blush" class="android-swatch frosting-swatch" style="background:#edd3d6" aria-label="Blush frosting"></button>
                            <button type="button" data-frosting="sage" class="android-swatch frosting-swatch" style="background:#d2e1d8" aria-label="Sage frosting"></button>
                            <button type="button" data-frosting="powder_blue" class="android-swatch frosting-swatch" style="background:#d6e1ea" aria-label="Powder blue frosting"></button>
                            <button type="button" data-frosting="chocolate" class="android-swatch frosting-swatch" style="background:#4a2f1f" aria-label="Chocolate frosting"></button>
                            <button type="button" data-frosting="mocha" class="android-swatch frosting-swatch" style="background:#6a4638" aria-label="Mocha frosting"></button>
                            <button type="button" data-frosting="lavender" class="android-swatch frosting-swatch" style="background:#d7b2ef" aria-label="Lavender frosting"></button>
                            <button id="builder-frosting-custom-btn" type="button" data-frosting="custom" class="android-swatch frosting-swatch bg-white text-xl font-black text-[#7A5252]" aria-label="Custom frosting color">+</button>
                        </div>
                        <input id="builder-frosting-custom" type="color" value="#6a4638" class="sr-only" tabindex="-1" aria-hidden="true">
                    </div>

                    <div class="android-field">
                        <label for="builder-topper">Topper</label>
                        <select id="builder-topper" name="customization[topper]">
                            <option value="none">No topper</option>
                            <option value="name">Name topper</option>
                            <option value="acrylic">Acrylic topper</option>
                            <option value="edible_print">Edible print topper</option>
                        </select>
                    </div>

                    <div class="android-field">
                        <label for="builder-message">Message on cake</label>
                        <textarea id="builder-message" name="customization[message]" maxlength="50" rows="3" placeholder="Happy Birthday, Mia!"></textarea>
                    </div>

                    <div class="android-grid-2">
                        <div class="android-field">
                            <label for="builder-text-color">Text color</label>
                            <input id="builder-text-color" type="color" value="#7a3444" class="color-pill h-12">
                        </div>
                        <div class="android-field">
                            <label for="builder-topping-color">Topping color</label>
                            <input id="builder-topping-color" type="color" value="#ff7eac" class="color-pill h-12">
                        </div>
                    </div>
                </div>
            </div>

            <div class="android-card p-5">
                <p class="text-xs font-black uppercase tracking-[.2em] text-pink-600">Step 3</p>
                <h2 class="mt-1 text-2xl font-black text-[#4f3338]">Toppings</h2>
                <p class="mt-1 text-sm text-[#86646a]">Tap presets or add individual decorations.</p>

                <div class="mt-5 space-y-4">
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" class="quick-topping android-preset-btn" data-preset="sprinkles">Sprinkles</button>
                        <button type="button" class="quick-topping android-preset-btn" data-preset="chips">Choco chips</button>
                        <button type="button" class="quick-topping android-preset-btn" data-preset="pearls">Pearls</button>
                        <button type="button" class="quick-topping android-preset-btn" data-preset="nuts">Nuts</button>
                    </div>

                    <div class="grid grid-cols-4 gap-2">
                        <button type="button" class="shape-adjust android-shape-btn" data-shape="dot" data-action="add">● +</button>
                        <button type="button" class="shape-adjust android-shape-btn" data-shape="heart" data-action="add">♥ +</button>
                        <button type="button" class="shape-adjust android-shape-btn" data-shape="flower" data-action="add">✿ +</button>
                        <button type="button" class="shape-adjust android-shape-btn" data-shape="star" data-action="add">★ +</button>
                    </div>

                    <button id="builder-clear-toppings" type="button" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-sm font-bold text-[#7A5252]">Clear toppings</button>
                </div>
            </div>

            <div class="android-card p-5">
                <p class="text-xs font-black uppercase tracking-[.2em] text-pink-600">Finish</p>
                <h2 class="mt-1 text-2xl font-black text-[#4f3338]">Order details</h2>

                <div class="mt-5 space-y-4">
                    <label class="flex items-center gap-3 rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 font-bold text-[#5A3A3A]">
                        <input id="builder-rush" type="checkbox" value="yes" class="h-5 w-5">
                        Rush order (+₱350)
                    </label>

                    <div class="android-field">
                        <label for="builder-quantity">Quantity</label>
                        <input id="builder-quantity" name="quantity" type="number" min="1" value="1">
                    </div>

                    <div class="android-field">
                        <label for="builder-special-instructions">Special instructions</label>
                        <textarea id="builder-special-instructions" name="special_instructions" rows="3" placeholder="Allergies, delivery notes, design requests..."></textarea>
                    </div>
                </div>
            </div>

            <div class="android-sticky-submit">
                <button id="builder-submit" class="android-submit-btn" type="submit">Add Custom Cake to Cart</button>
            </div>
        </form>
    </section>
</div>

<script>
(() => {
    const pricing = @json($customizationPricing ?? []);
    const $ = (selector) => document.querySelector(selector);
    const $$ = (selector) => [...document.querySelectorAll(selector)];

    const shapeSelect = $('#builder-shape');
    const sizeSelect = $('#builder-size');
    const layersSelect = $('#builder-layers');
    const spongeSelect = $('#builder-sponge');
    const fillingSelect = $('#builder-filling');
    const frostingInput = $('#builder-frosting');
    const frostingCustomInput = $('#builder-frosting-custom');
    const frostingCustomHidden = $('#builder-frosting-custom-hidden');
    const dripSelect = $('#builder-drip');
    const topperSelect = $('#builder-topper');
    const messageInput = $('#builder-message');
    const textColorInput = $('#builder-text-color');
    const toppingColorInput = $('#builder-topping-color');
    const rushCheckbox = $('#builder-rush');
    const rushHidden = $('#builder-rush-hidden');
    const toppingsHidden = $('#builder-toppings-hidden');
    const fillingBadge = $('#filling-badge');
    const fillingBadgeName = $('#filling-badge-name');
    const fillingBadgeSwatch = $('#filling-badge-swatch');

    const toppings = [];
    const activePresets = new Set();

    const frostingTone = {
        white: ['#f2f2f2', '#d9d9d9'],
        ivory: ['#e9e2cf', '#d2c7ad'],
        blush: ['#edd3d6', '#d8b5bb'],
        sage: ['#d2e1d8', '#b4c8bd'],
        powder_blue: ['#d6e1ea', '#b7c6d3'],
        chocolate: ['#4a2f1f', '#311f14'],
        mocha: ['#6a4638', '#4b3329'],
        lavender: ['#d7b2ef', '#bb8fdd'],
    };
    const spongeTone = {
        Vanilla: '#f5d7a5',
        Chocolate: '#7b4a38',
        'Red Velvet': '#a43b4a',
        Lemon: '#f3e38a',
        Strawberry: '#f3a6b8',
        Funfetti: '#f7e3b8',
    };
    const fillingTone = {
        'Chocolate Mousse': '#6a3d2d',
        'Strawberry Jam': '#cf4f6a',
        'Vanilla Cream': '#f6f0dc',
        Nutella: '#5a3528',
        'Cookies & Cream': '#d5d2dd',
        Buttercream: '#f6dfb2',
    };

    const darkenHex = (hex, factor = 0.72) => {
        const value = String(hex || '').replace('#', '');
        if (value.length !== 6) return hex;
        const r = Math.max(0, Math.min(255, Math.round(parseInt(value.slice(0, 2), 16) * factor)));
        const g = Math.max(0, Math.min(255, Math.round(parseInt(value.slice(2, 4), 16) * factor)));
        const b = Math.max(0, Math.min(255, Math.round(parseInt(value.slice(4, 6), 16) * factor)));
        return `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`;
    };

    const getFrostingTone = () => {
        if (frostingInput.value === 'custom') {
            const hex = frostingCustomInput.value || '#6a4638';
            return [hex, darkenHex(hex)];
        }
        return frostingTone[frostingInput.value] || frostingTone.ivory;
    };

    const randomTopPoint = () => ({
        x: 70 + Math.random() * 180,
        y: 70 + Math.random() * 180,
    });

    const addTopping = (shape, extra = {}) => {
        const point = randomTopPoint();
        toppings.push({
            shape,
            color: toppingColorInput.value || '#ff7eac',
            x: point.x,
            y: point.y,
            ...extra,
        });
    };

    const setPreset = (preset) => {
        if (activePresets.has(preset)) {
            for (let i = toppings.length - 1; i >= 0; i--) {
                if (toppings[i].preset === preset) toppings.splice(i, 1);
            }
            activePresets.delete(preset);
        } else {
            const config = {
                sprinkles: { count: 22, shape: 'sprinkle', palette: ['#e772aa', '#6ac39a', '#2fa8df', '#f9df00', '#f06f4f'] },
                chips: { count: 18, shape: 'chip', palette: ['#5a331a', '#6a3f1f', '#4e2d17'] },
                pearls: { count: 14, shape: 'dot', palette: ['#f8efe0', '#f3e7cf', '#efe2d2'] },
                nuts: { count: 16, shape: 'nut', palette: ['#b8742f', '#c98b45', '#e9dbc1'] },
            }[preset];
            if (!config) return;
            for (let i = 0; i < config.count; i++) {
                addTopping(config.shape, {
                    preset,
                    color: config.palette[i % config.palette.length],
                    rotation: (i * 37) % 180,
                    length: 10 + (i % 8),
                    thickness: 4 + ((i % 3) * .7),
                    scale: .7 + ((i % 5) * .08),
                });
            }
            activePresets.add(preset);
        }
        $$('.quick-topping').forEach((button) => button.classList.toggle('is-active', activePresets.has(button.dataset.preset)));
        sync();
    };

    const syncSwatches = () => {
        $$('.frosting-swatch').forEach((button) => {
            button.classList.toggle('is-active', button.dataset.frosting === frostingInput.value);
        });
    };

    const sync = () => {
        const [frostingTop, frostingBottom] = getFrostingTone();
        toppingsHidden.value = JSON.stringify(toppings);
        rushHidden.value = rushCheckbox.checked ? 'yes' : 'no';
        frostingCustomHidden.value = frostingInput.value === 'custom' ? (frostingCustomInput.value || '') : '';

        const detail = {
            shape: shapeSelect.value,
            size: sizeSelect.value,
            layers: layersSelect.value,
            sponge: spongeSelect.value,
            filling: fillingSelect.value,
            spongeColor: spongeTone[spongeSelect.value] || '#f5d7a5',
            fillingColor: fillingTone[fillingSelect.value] || '#f6f0dc',
            frostingTop,
            frostingBottom,
            drip: dripSelect.value,
            topper: topperSelect.value,
            message: messageInput.value || '',
            textColor: textColorInput.value || '#7a3444',
            toppings: toppings.map((item) => ({ ...item })),
        };

        if (fillingBadge && fillingBadgeName && fillingBadgeSwatch) {
            fillingBadge.classList.remove('hidden');
            fillingBadgeName.textContent = fillingSelect.value;
            fillingBadgeSwatch.style.background = fillingTone[fillingSelect.value] || '#f6f0dc';
        }

        window.__bonbonCustomize3DState = detail;
        window.dispatchEvent(new CustomEvent('bonbon-customize-3d:update', { detail }));
        window.BonbonCustomize3D?.update?.(detail);
    };

    $$('.frosting-swatch').forEach((button) => {
        button.addEventListener('click', () => {
            frostingInput.value = button.dataset.frosting;
            if (button.dataset.frosting === 'custom') {
                if (typeof frostingCustomInput.showPicker === 'function') frostingCustomInput.showPicker();
                else frostingCustomInput.click();
            }
            syncSwatches();
            sync();
        });
    });

    frostingCustomInput.addEventListener('input', () => {
        frostingInput.value = 'custom';
        syncSwatches();
        sync();
    });

    $$('.quick-topping').forEach((button) => button.addEventListener('click', () => setPreset(button.dataset.preset)));
    $$('.shape-adjust').forEach((button) => button.addEventListener('click', () => {
        if (button.dataset.action === 'add') addTopping(button.dataset.shape);
        sync();
    }));
    $('#builder-clear-toppings')?.addEventListener('click', () => {
        toppings.length = 0;
        activePresets.clear();
        $$('.quick-topping').forEach((button) => button.classList.remove('is-active'));
        sync();
    });

    [shapeSelect, sizeSelect, layersSelect, spongeSelect, fillingSelect, dripSelect, topperSelect, rushCheckbox].forEach((field) => field.addEventListener('change', sync));
    [messageInput, textColorInput, toppingColorInput].forEach((field) => field.addEventListener('input', sync));

    syncSwatches();
    sync();
})();
</script>
@endsection
