@extends('layouts.app')

@section('content')
<style>
    .color-pill {
        appearance: none;
        -webkit-appearance: none;
        border-radius: 1rem;
        overflow: hidden;
        padding: 0;
    }
    .color-pill::-webkit-color-swatch-wrapper {
        padding: 0;
        border-radius: inherit;
    }
    .color-pill::-webkit-color-swatch {
        border: none;
        border-radius: inherit;
    }
    .color-pill::-moz-color-swatch {
        border: none;
        border-radius: inherit;
    }
    .custom-frosting-swatch {
        --custom-color: #6a4638;
        background: radial-gradient(
            circle at center,
            #f7f7f7 0 42%,
            var(--custom-color) 43% 66%,
            #f7f7f7 67% 100%
        );
    }
    .no-scrollbar {
        scrollbar-width: none;
    }
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
</style>
<div id="test-customize-page" class="fixed inset-0 z-50 w-full h-[100dvh] overflow-hidden bg-gradient-to-b from-[#fff6f8] to-[#ffe7ef]">

    {{-- 3D Canvas Background (Fullscreen Turntable) --}}
    <div id="cake-3d-wrap" class="absolute inset-0">
        <div id="cake-3d-canvas" class="block h-full w-full cursor-grab active:cursor-grabbing"></div>

        {{-- Auto-rotating hint --}}
        <div id="spin-hint" class="absolute top-10 right-10 z-10 rounded-full bg-white/80 px-4 py-2 text-xs font-semibold text-[#7A5252] shadow-md" style="backdrop-filter:blur(4px);transition:opacity .6s">✦ Auto-rotating</div>

        {{-- Filling badge --}}
        <div id="filling-badge" class="absolute bottom-24 right-10 z-10 max-w-[160px] rounded-2xl bg-white/92 px-4 py-3 shadow-lg hidden" style="backdrop-filter:blur(6px)">
            <div class="mb-1 text-[10px] font-bold uppercase tracking-wider text-[#5A3A3A]">Inside Filling</div>
            <div id="filling-badge-name" class="truncate text-sm font-bold text-[#7A5252]"></div>
            <div id="filling-badge-swatch" class="mt-2 h-3 w-full rounded-full" style="background:#6a3d2d"></div>
        </div>
    </div>

    {{-- Bottom View Tabs Overlay --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 flex gap-3 rounded-full bg-white/80 p-2 shadow-2xl backdrop-blur-md border border-[#F3D7DD]">
        <button id="tab-view-front" data-cake-view="auto" type="button" class="rounded-full border border-[#ec5a61] bg-pink-600 px-8 py-3 text-sm font-bold text-white shadow-sm transition-all hover:bg-pink-500">360 View</button>
        <button id="tab-view-top" data-cake-view="top" type="button" class="rounded-full border border-transparent px-8 py-3 text-sm font-bold text-[#7A5252] transition-all hover:bg-pink-100 hover:text-pink-700">Top View</button>
        <button id="tab-view-side" data-cake-view="side" type="button" class="rounded-full border border-transparent px-8 py-3 text-sm font-bold text-[#7A5252] transition-all hover:bg-pink-100 hover:text-pink-700">Side View</button>
        <button id="tab-view-inside" data-cake-view="inside" type="button" class="rounded-full border border-pink-300 bg-pink-50 px-8 py-3 text-sm font-black text-pink-600 transition-all hover:bg-pink-100 shadow-inner">🍰 Inside View</button>
    </div>

    {{-- Cellphone Mockup Control Panel (Left Side) --}}
    <div class="absolute left-4 top-4 bottom-24 z-20 flex items-start pointer-events-none sm:left-6 lg:left-10">
        <div class="relative flex h-full max-h-[830px] w-[385px] max-w-[calc(100vw-2rem)] flex-col overflow-hidden rounded-[2.75rem] border-[10px] border-[#202024] bg-[#202024] p-2 shadow-[0_26px_70px_rgba(55,28,35,0.28)] pointer-events-auto ring-1 ring-white/35">
            {{-- iPhone Notch --}}
            <div class="absolute left-1/2 top-2 z-50 h-6 w-[42%] -translate-x-1/2 rounded-b-3xl bg-[#202024]"></div>

            {{-- Scrollable Form Area --}}
            <div class="flex-1 overflow-y-auto rounded-[2.05rem] bg-[#fffaf8] px-4 pb-7 pt-10 no-scrollbar">
                <div class="mb-4">
                    <p class="text-[10px] font-black uppercase tracking-[0.22em] text-pink-600">Cake Studio</p>
                    <h1 class="mt-1 text-2xl font-black leading-tight text-[#4f3338]">Build Your Dream Cake</h1>
                    <p class="mt-2 text-xs leading-relaxed text-[#86646a]">Choose the structure first, then decorate the cake in 3D.</p>
                </div>

                <div class="mt-4">
                    <form method="POST" action="{{ route('cart.add') }}" class="space-y-4" id="cake-builder-form">
                @csrf
                <input type="hidden" id="builder-toppings-hidden" name="customization[toppings]" value="[]">
                <input type="hidden" id="builder-preview-svg-hidden" name="customization[preview_svg]" value="">

                <section class="rounded-[1.45rem] border border-[#f0d7dc] bg-white/90 p-4 shadow-[0_14px_28px_rgba(90,58,58,0.08)]">
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="text-sm font-black uppercase tracking-[0.14em] text-[#5A3A3A]">Build Steps</h2>
                        <span id="builder-step-label" class="rounded-full bg-pink-50 px-2.5 py-1 text-[11px] font-bold text-pink-600">Step 1 of 2</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                        <div id="step-pill-1" class="rounded-xl bg-pink-600 px-3 py-2 text-center text-white">Structure</div>
                        <div id="step-pill-2" class="rounded-xl bg-[#F7E7EB] px-3 py-2 text-center text-[#7A5252]">Toppings & Text</div>
                    </div>
                </section>

                <section data-step="1" class="rounded-[1.45rem] border border-[#f0d7dc] bg-white/92 p-4 shadow-[0_14px_28px_rgba(90,58,58,0.08)]">
                    <h2 class="mb-3 text-lg font-black text-[#4f3338]">Foundation</h2>
                    <div class="grid gap-3">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-[0.08em] text-[#7a5258]">Shape</label>
                            <select id="builder-shape" name="customization[shape]" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-3 py-3 text-sm font-semibold text-[#57393f]">
                                <option value="Round">Round</option><option value="Square">Square</option><option value="Heart">Heart</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-[0.08em] text-[#7a5258]">Base Size</label>
                            <select id="builder-size" name="customization[size]" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-3 py-3 text-sm font-semibold text-[#57393f]">
                                <option value="6">6 inches</option><option value="8">8 inches</option><option value="10">10 inches</option><option value="12">12 inches</option>
                            </select>
                        </div>
                    </div>
                </section>

                <section data-step="1" class="rounded-[1.45rem] border border-[#f0d7dc] bg-white/92 p-4 shadow-[0_14px_28px_rgba(90,58,58,0.08)]">
                    <h2 class="mb-3 text-lg font-black text-[#4f3338]">Design</h2>
                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-[0.08em] text-[#7a5258]">Cake Flavor</label>
                            <select name="customization[sponge]" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-3 py-3 text-sm font-semibold text-[#57393f]">
                                <option value="Vanilla">Vanilla</option>
                                <option value="Chocolate">Chocolate</option>
                                <option value="Red Velvet">Red Velvet</option>
                                <option value="Lemon">Lemon</option>
                                <option value="Strawberry">Strawberry</option>
                                <option value="Funfetti">Funfetti</option>
                            </select>
                            <p class="mt-1 text-[11px] leading-relaxed text-[#8f6a73]">Cake base flavor inside each tier.</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-[0.08em] text-[#7a5258]">Filling</label>
                            <select name="customization[filling]" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-3 py-3 text-sm font-semibold text-[#57393f]">
                                <option value="Chocolate Mousse">Chocolate mousse</option>
                                <option value="Strawberry Jam">Strawberry jam</option>
                                <option value="Vanilla Cream">Vanilla cream</option>
                                <option value="Nutella">Nutella</option>
                                <option value="Cookies & Cream">Cookies & cream</option>
                                <option value="Buttercream">Buttercream</option>
                            </select>
                            <p class="mt-1 text-[11px] leading-relaxed text-[#8f6a73]">Flavor layer between sponge tiers.</p>
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.08em] text-[#7a5258]">Frosting Color (+PHP 10)</label>
                            <input id="builder-frosting" type="hidden" name="customization[frosting]" value="ivory">
                            <input id="builder-frosting-custom-hidden" type="hidden" name="customization[frosting_custom]" value="">
                            <div class="grid grid-cols-6 gap-2" id="builder-frosting-swatches">
                                <button type="button" data-frosting="white" class="frosting-swatch h-10 w-10 rounded-full border border-[#dddddd] bg-[#f2f2f2]" aria-label="White frosting"></button>
                                <button type="button" data-frosting="ivory" class="frosting-swatch h-10 w-10 rounded-full border border-[#dddddd] bg-[#e9e2cf]" aria-label="Ivory frosting"></button>
                                <button type="button" data-frosting="blush" class="frosting-swatch h-10 w-10 rounded-full border border-[#dddddd] bg-[#edd3d6]" aria-label="Blush frosting"></button>
                                <button type="button" data-frosting="sage" class="frosting-swatch h-10 w-10 rounded-full border border-[#dddddd] bg-[#d2e1d8]" aria-label="Sage frosting"></button>
                                <button type="button" data-frosting="powder_blue" class="frosting-swatch h-10 w-10 rounded-full border border-[#dddddd] bg-[#d6e1ea]" aria-label="Powder blue frosting"></button>
                                <button type="button" data-frosting="chocolate" class="frosting-swatch h-10 w-10 rounded-full border border-[#dddddd] bg-[#4a2f1f]" aria-label="Chocolate frosting"></button>
                                <button type="button" data-frosting="mocha" class="frosting-swatch h-11 w-11 rounded-full border-2 border-[#ec5a61] bg-[#6a4638] text-white" aria-label="Mocha frosting">✓</button>
                                <button type="button" data-frosting="lavender" class="frosting-swatch h-11 w-11 rounded-full border border-[#dddddd] bg-[#d7b2ef]" aria-label="Lavender frosting"></button>
                                <button id="builder-frosting-custom-btn" type="button" data-frosting="custom" class="custom-frosting-swatch frosting-swatch h-11 w-11 rounded-full border border-[#dddddd] text-xl font-bold leading-none text-[#7A5252]" aria-label="Custom frosting color">+</button>
                            </div>
                            <input id="builder-frosting-custom" type="color" value="#6a4638" class="sr-only" tabindex="-1" aria-hidden="true">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-[0.08em] text-[#7a5258]">Drip</label>
                            <select id="builder-drip" name="customization[drip]" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-3 py-3 text-sm font-semibold text-[#57393f]">
                                <option value="none">No drip</option>
                                <option value="chocolate">Chocolate drip</option>
                                <option value="white_chocolate">White chocolate drip</option>
                                <option value="pink">Pink drip</option>
                                <option value="caramel">Caramel drip</option>
                            </select>
                            <p class="mt-1 text-[11px] leading-relaxed text-[#8f6a73]">Turn drip overlay on or off.</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-[0.08em] text-[#7a5258]">Tiers</label>
                            <select id="builder-layers" name="customization[layers]" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-3 py-3 text-sm font-semibold text-[#57393f]">
                                <option value="1">1 tier</option><option value="2">2 tiers</option><option value="3">3 tiers</option><option value="4">4 tiers</option>
                            </select>
                            <p class="mt-1 text-[11px] leading-relaxed text-[#8f6a73]">Adds vertical cake levels for larger designs.</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-[0.08em] text-[#7a5258]">Topper</label>
                            <select id="builder-topper" name="customization[topper]" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-3 py-3 text-sm font-semibold text-[#57393f]">
                                <option value="none">No topper</option><option value="name">Name topper</option><option value="acrylic">Acrylic topper</option><option value="edible_print">Edible print topper</option>
                            </select>
                            <p class="mt-1 text-[11px] leading-relaxed text-[#8f6a73]">Decorative sign placed at the top of the cake.</p>
                        </div>
                    </div>
                </section>

                <section data-step="2" class="rounded-[1.45rem] border border-[#f0d7dc] bg-white/92 p-4 shadow-[0_14px_28px_rgba(90,58,58,0.08)] hidden">
                    <h2 class="mb-2 text-lg font-black text-[#4f3338]">Toppings & Text</h2>
                    <p class="mb-4 text-xs leading-relaxed text-[#7A5252]">Add flat toppings and personalize text placement, then check it in the 3D view.</p>

                    <div class="space-y-4">
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-[0.08em] text-[#7a5258]">Message on cake</label>
                                <textarea id="builder-message" name="customization[message]" maxlength="50" rows="3" placeholder="Message on cake" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-3 py-3 text-sm font-semibold text-[#57393f]"></textarea>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-[0.08em] text-[#7a5258]">Text color</label>
                                <input id="builder-text-color" type="color" value="#7a3444" class="color-pill h-11 w-full cursor-pointer rounded-2xl border border-[#F3D7DB] bg-white p-0">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-[0.08em] text-[#7a5258]">Topping shapes</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="rounded-xl border border-[#F3D7DB] bg-[#FFF7F7] p-2">
                                        <div class="mb-2 text-center text-lg">●</div>
                                        <div class="flex gap-2">
                                            <button type="button" class="shape-adjust flex-1 rounded-lg border border-[#F3D7DB] bg-white py-1 text-sm font-bold text-[#7A5252]" data-shape="dot" data-action="remove">-</button>
                                            <button type="button" class="shape-adjust flex-1 rounded-lg bg-[#F06292] py-1 text-sm font-bold text-white" data-shape="dot" data-action="add">+</button>
                                        </div>
                                    </div>
                                    <div class="rounded-xl border border-[#F3D7DB] bg-[#FFF7F7] p-2">
                                        <div class="mb-2 text-center text-lg">♥</div>
                                        <div class="flex gap-2">
                                            <button type="button" class="shape-adjust flex-1 rounded-lg border border-[#F3D7DB] bg-white py-1 text-sm font-bold text-[#7A5252]" data-shape="heart" data-action="remove">-</button>
                                            <button type="button" class="shape-adjust flex-1 rounded-lg bg-[#F06292] py-1 text-sm font-bold text-white" data-shape="heart" data-action="add">+</button>
                                        </div>
                                    </div>
                                    <div class="rounded-xl border border-[#F3D7DB] bg-[#FFF7F7] p-2">
                                        <div class="mb-2 text-center text-lg">✿</div>
                                        <div class="flex gap-2">
                                            <button type="button" class="shape-adjust flex-1 rounded-lg border border-[#F3D7DB] bg-white py-1 text-sm font-bold text-[#7A5252]" data-shape="flower" data-action="remove">-</button>
                                            <button type="button" class="shape-adjust flex-1 rounded-lg bg-[#F06292] py-1 text-sm font-bold text-white" data-shape="flower" data-action="add">+</button>
                                        </div>
                                    </div>
                                    <div class="rounded-xl border border-[#F3D7DB] bg-[#FFF7F7] p-2">
                                        <div class="mb-2 text-center text-lg">★</div>
                                        <div class="flex gap-2">
                                            <button type="button" class="shape-adjust flex-1 rounded-lg border border-[#F3D7DB] bg-white py-1 text-sm font-bold text-[#7A5252]" data-shape="star" data-action="remove">-</button>
                                            <button type="button" class="shape-adjust flex-1 rounded-lg bg-[#F06292] py-1 text-sm font-bold text-white" data-shape="star" data-action="add">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-[0.08em] text-[#7a5258]">Topping color</label>
                                <input id="builder-topping-color" type="color" value="#ff7eac" class="color-pill h-11 w-full cursor-pointer rounded-2xl border border-[#F3D7DB] bg-white p-0">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-[0.08em] text-[#7a5258]">Quick toppings</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" class="quick-topping rounded-xl border border-[#F3D7DB] bg-[#FFF7F7] px-3 py-2 text-xs font-semibold text-[#7A5252]" data-preset="sprinkles">Sprinkles</button>
                                    <button type="button" class="quick-topping rounded-xl border border-[#F3D7DB] bg-[#FFF7F7] px-3 py-2 text-xs font-semibold text-[#7A5252]" data-preset="sprinkles_choco">Chocolate Sprinkles</button>
                                    <button type="button" class="quick-topping rounded-xl border border-[#F3D7DB] bg-[#FFF7F7] px-3 py-2 text-xs font-semibold text-[#7A5252]" data-preset="sprinkles_white">White Sprinkles</button>
                                    <button type="button" class="quick-topping rounded-xl border border-[#F3D7DB] bg-[#FFF7F7] px-3 py-2 text-xs font-semibold text-[#7A5252]" data-preset="chips">Chocolate Chips</button>
                                    <button type="button" class="quick-topping rounded-xl border border-[#F3D7DB] bg-[#FFF7F7] px-3 py-2 text-xs font-semibold text-[#7A5252]" data-preset="nuts">Nuts</button>
                                    <button type="button" class="quick-topping rounded-xl border border-[#F3D7DB] bg-[#FFF7F7] px-3 py-2 text-xs font-semibold text-[#7A5252]" data-preset="pearls">Pearl Candy</button>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button id="builder-clear-toppings" type="button" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3 text-sm font-semibold text-[#7A5252]">Clear</button>
                            </div>
                            <p class="text-xs text-[#8f6a73]">Use + / - per shape to add or remove toppings.</p>
                    </div>
                </section>

                <section data-step="2" class="rounded-[1.45rem] border border-[#f0d7dc] bg-white/92 p-4 shadow-[0_14px_28px_rgba(90,58,58,0.08)] hidden">
                    <h2 class="mb-3 text-lg font-black text-[#4f3338]">Finish</h2>
                    <div class="grid gap-3">
                        <label class="flex items-center gap-3 rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3">
                            <input id="builder-rush" type="checkbox" value="yes" class="h-4 w-4">
                            <input id="builder-rush-hidden" type="hidden" name="customization[rush]" value="no">
                            <span>Rush order (+&#8369;350)</span>
                        </label>
                        <input name="quantity" type="number" min="1" value="1" class="rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-3 py-3 text-sm font-semibold text-[#57393f]">
                    </div>
                    <textarea name="special_instructions" rows="3" placeholder="Special instructions" class="mt-3 w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-3 py-3 text-sm font-semibold text-[#57393f]"></textarea>
                </section>

                <div class="flex gap-3">
                    <button id="builder-prev-step" type="button" class="rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-6 py-4 text-base font-bold text-[#7A5252] disabled:cursor-not-allowed disabled:opacity-50">Back</button>
                    <button id="builder-next-step" type="button" class="w-full rounded-2xl bg-pink-500 px-6 py-4 text-lg font-bold text-white hover:bg-pink-600">Next: Toppings</button>
                    <button id="builder-submit" class="hidden w-full rounded-2xl bg-pink-600 px-6 py-4 text-lg font-bold text-white hover:bg-pink-700">Add Custom Cake to Cart</button>
                </div>
            </form>
                </div>
            </div>

            {{-- Home Indicator --}}
            <div class="absolute bottom-2 left-1/2 -translate-x-1/2 w-1/3 h-1.5 bg-gray-300 rounded-full z-50"></div>
        </div>
    </div>

    <div id="slice-modal" class="fixed inset-0 z-[9999] hidden">
                    <div id="slice-modal-backdrop" class="absolute inset-0 cursor-pointer bg-[#14070d]/86" style="backdrop-filter:blur(18px) saturate(1.12)"></div>
                    <div class="absolute inset-0 flex items-center justify-center p-4 md:p-8">
                        <div class="relative overflow-hidden rounded-[2rem] shadow-[0_34px_90px_rgba(0,0,0,0.46)] ring-1 ring-white/10" style="width:min(94vw,900px);height:min(84vh,660px);background:radial-gradient(circle at 50% 28%,#49323a 0%,#241018 45%,#12060b 100%)">
                            <canvas id="slice-3d-canvas" style="display:block;width:100%;height:100%"></canvas>
                            <button id="slice-modal-close" class="absolute right-4 top-4 z-20 flex h-10 w-10 items-center justify-center rounded-full bg-white/16 text-xl font-bold text-white shadow-lg transition hover:bg-white/30" style="backdrop-filter:blur(10px)">×</button>
                            <div class="absolute bottom-5 left-5 max-w-[78%] rounded-2xl border border-white/12 bg-[#17070f]/66 px-5 py-4 shadow-2xl" style="pointer-events:none;backdrop-filter:blur(16px)">
                                <div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-[#ffd7df]">Inside Your Cake</div>
                                <div id="slice-info" class="text-lg font-bold leading-tight text-white"></div>
                                <div id="slice-filling-info" class="mt-1 text-sm text-white/74"></div>
                            </div>
                        </div>
                    </div>
                </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>
        (() => {
            const sizeSelect = document.getElementById('builder-size');
            const layersSelect = document.getElementById('builder-layers');
            const spongeSelect = document.querySelector('select[name="customization[sponge]"]');
            const fillingSelect = document.querySelector('select[name="customization[filling]"]');
            const frostingSelect = document.getElementById('builder-frosting');
            const frostingCustomInput = document.getElementById('builder-frosting-custom');
            const frostingCustomHidden = document.getElementById('builder-frosting-custom-hidden');
            const frostingCustomBtn = document.getElementById('builder-frosting-custom-btn');
            const frostingSwatches = [...document.querySelectorAll('.frosting-swatch')];
            const dripSelect = document.getElementById('builder-drip');
            const topperSelect = document.getElementById('builder-topper');
            const rushCheckbox = document.getElementById('builder-rush');
            const rushHidden = document.getElementById('builder-rush-hidden');
            const shapeSelect = document.getElementById('builder-shape');
            const messageInput = document.getElementById('builder-message');
            const textColorInput = document.getElementById('builder-text-color');
            const topViewBaseEl = document.getElementById('cake-top-base');
            const topViewToppingsEl = document.getElementById('cake-top-toppings');
            const topViewMessageEl = document.getElementById('cake-top-message-preview');
            const toppingsHiddenInput = document.getElementById('builder-toppings-hidden');
            const previewSvgHiddenInput = document.getElementById('builder-preview-svg-hidden');
            const shapeAdjustBtns = [...document.querySelectorAll('.shape-adjust')];
            const toppingColorInput = document.getElementById('builder-topping-color');
            const clearToppingsBtn = document.getElementById('builder-clear-toppings');
            const quickToppingBtns = [...document.querySelectorAll('.quick-topping')];
            const previewTabFrontBtn = document.getElementById('preview-tab-front');
            const previewTabTopBtn = document.getElementById('preview-tab-top');
            const previewPanelFront = document.getElementById('preview-panel-front');
            const previewPanelTop = document.getElementById('preview-panel-top');
            const stepLabelEl = document.getElementById('builder-step-label');
            const prevStepBtn = document.getElementById('builder-prev-step');
            const nextStepBtn = document.getElementById('builder-next-step');
            const submitBtn = document.getElementById('builder-submit');
            const stepSections = [...document.querySelectorAll('[data-step]')];
            const stepPills = [1, 2].map((n) => document.getElementById(`step-pill-${n}`));

            const addonEl = document.getElementById('estimate-addon');
            const totalEl = document.getElementById('estimate-total');
            const cakeShadowEl = document.getElementById('cake-shadow');
            const cakeLayersEl = document.getElementById('cake-layers');
            const cakeInsideLayersEl = document.getElementById('cake-inside-layers');
            const cakeInsideLabelEl = document.getElementById('cake-inside-label');
            const messagePreviewEl = document.getElementById('cake-message-preview');
            const topperWrapEl = document.getElementById('cake-topper');
            const topperTextEl = document.getElementById('cake-topper-text');
            const cakeSvgEl = document.getElementById('cake-svg');

            const pricing = @json($customizationPricing ?? []);

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
            const getFrostingTone = () => {
                if (frostingSelect.value === 'custom') {
                    const customHex = frostingCustomInput.value || '#6a4638';
                    return [customHex, darkenHex(customHex, 0.72)];
                }
                return frostingTone[frostingSelect.value] || frostingTone.ivory;
            };
            const syncCustomFrostingSwatch = () => {
                frostingCustomBtn.style.setProperty('--custom-color', frostingCustomInput.value || '#6a4638');
            };

            const spongeTone = {
                Vanilla: '#f5d7a5',
                Chocolate: '#7b4a38',
                'Red Velvet': '#a43b4a',
                Lemon: '#f3e38a',
                Strawberry: '#f3a6b8',
                Funfetti: '#f7e3b8',
                Ube: '#b9a0da'
            };

            const fillingTone = {
                'Chocolate Mousse': '#6a3d2d',
                'Strawberry Jam': '#cf4f6a',
                'Vanilla Cream': '#f6f0dc',
                Nutella: '#5a3528',
                'Cookies & Cream': '#d5d2dd',
                Buttercream: '#f6dfb2',
                Ganache: '#5a3528',
                Custard: '#f4d986',
                'Fruit Jam': '#cf4f6a',
                'Cream Cheese': '#f0e8dc'
            };

            const fillingStyle = {
                'Chocolate Mousse': { accent: '#7c4e3c', chunks: 8, drip: 0.7, rough: 0.55, metal: 0.08 },
                'Strawberry Jam': { accent: '#f08aa4', chunks: 14, drip: 1.25, rough: 0.24, metal: 0.02 },
                'Vanilla Cream': { accent: '#fff8ea', chunks: 7, drip: 0.65, rough: 0.62, metal: 0.01 },
                Nutella: { accent: '#7b4a37', chunks: 9, drip: 0.8, rough: 0.42, metal: 0.06 },
                'Cookies & Cream': { accent: '#6a6470', chunks: 16, drip: 0.6, rough: 0.72, metal: 0.02 },
                Buttercream: { accent: '#ffe9c6', chunks: 6, drip: 0.55, rough: 0.68, metal: 0.01 },
            };

            const dripTone = {
                none: null,
                chocolate: '#3f2219',
                white_chocolate: '#fff6ea',
                pink: '#f26ca1',
                caramel: '#b66a3d'
            };

            const tierGapByShape = {
                Round: 4,
                Square: 0,
                Heart: 6
            };
            const overallCakeScale = 1.5;
            const dripOverscale = 1.03;
            const circleTopDripExtraScale = 1.005;
            const toppingItems = [];
            const activeToppingPresets = new Set();
            let currentStep = 1;

            const php = (amount) => `PHP ${Number(amount).toFixed(2)}`;
            const darkenHex = (hex, factor = 0.75) => {
                const value = hex.replace('#', '');
                if (value.length !== 6) return hex;
                const r = Math.max(0, Math.min(255, Math.round(parseInt(value.slice(0, 2), 16) * factor)));
                const g = Math.max(0, Math.min(255, Math.round(parseInt(value.slice(2, 4), 16) * factor)));
                const b = Math.max(0, Math.min(255, Math.round(parseInt(value.slice(4, 6), 16) * factor)));
                return `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`;
            };

            const drawLayer = (shape, x, y, width, height, topColor, bottomColor, idx, dripMode) => {
                const ns = 'http://www.w3.org/2000/svg';
                const group = document.createElementNS(ns, 'g');
                const shouldFlipDrip = idx % 2 === 1;
                const topGradientId = `cakeTopGrad${idx}`;
                const sideGradientId = `cakeSideGrad${idx}`;
                const glossGradientId = `cakeGlossGrad${idx}`;
                const sideBaseColor = darkenHex(bottomColor, 0.92);
                const sideShadowColor = darkenHex(bottomColor, 0.62);
                const defs = document.createElementNS(ns, 'defs');
                const topGradient = document.createElementNS(ns, 'linearGradient');
                topGradient.setAttribute('id', topGradientId);
                topGradient.setAttribute('x1', '0%');
                topGradient.setAttribute('x2', '0%');
                topGradient.setAttribute('y1', '0%');
                topGradient.setAttribute('y2', '100%');
                const topShadeColor = darkenHex(topColor, 0.9);
                const topStop1 = document.createElementNS(ns, 'stop');
                topStop1.setAttribute('offset', '0%');
                topStop1.setAttribute('stop-color', topColor);
                topStop1.setAttribute('stop-opacity', '1');
                const topStop2 = document.createElementNS(ns, 'stop');
                topStop2.setAttribute('offset', '100%');
                topStop2.setAttribute('stop-color', topShadeColor);
                topStop2.setAttribute('stop-opacity', '1');
                topGradient.append(topStop1, topStop2);

                const sideGradient = document.createElementNS(ns, 'linearGradient');
                sideGradient.setAttribute('id', sideGradientId);
                sideGradient.setAttribute('x1', '0%');
                sideGradient.setAttribute('x2', '0%');
                sideGradient.setAttribute('y1', '0%');
                sideGradient.setAttribute('y2', '100%');
                const sideStop1 = document.createElementNS(ns, 'stop');
                sideStop1.setAttribute('offset', '0%');
                sideStop1.setAttribute('stop-color', sideBaseColor);
                sideStop1.setAttribute('stop-opacity', '1');
                const sideStop2 = document.createElementNS(ns, 'stop');
                sideStop2.setAttribute('offset', '100%');
                sideStop2.setAttribute('stop-color', sideShadowColor);
                sideStop2.setAttribute('stop-opacity', '1');
                sideGradient.append(sideStop1, sideStop2);

                const glossGradient = document.createElementNS(ns, 'linearGradient');
                glossGradient.setAttribute('id', glossGradientId);
                glossGradient.setAttribute('x1', '0%');
                glossGradient.setAttribute('x2', '100%');
                glossGradient.setAttribute('y1', '0%');
                glossGradient.setAttribute('y2', '0%');
                const glossStop1 = document.createElementNS(ns, 'stop');
                glossStop1.setAttribute('offset', '0%');
                glossStop1.setAttribute('stop-color', '#ffffff');
                glossStop1.setAttribute('stop-opacity', '0.35');
                const glossStop2 = document.createElementNS(ns, 'stop');
                glossStop2.setAttribute('offset', '100%');
                glossStop2.setAttribute('stop-color', '#ffffff');
                glossStop2.setAttribute('stop-opacity', '0');
                glossGradient.append(glossStop1, glossStop2);

                defs.append(topGradient, sideGradient, glossGradient);

                const tierDepth = Math.max(24, Math.round(height * 0.72));
                let sideEl;
                let topEl;
                let glossEl;
                let frostingEl = null;
                let frostingEl2 = null;
                if (shape === 'Square') {
                    // Rectangle geometry mapped from provided square.svg.
                    const squareSideD = 'M126.5,48.46H3.58C1.42,48.46,0,46.72,0,44.39v33A4.56,4.56,0,0,0,4.56,82h121a4.56,4.56,0,0,0,4.56-4.56v-33C130.08,46.71,128.65,48.46,126.5,48.46Z';
                    const squareTopD = 'M130.08,44.38c0,2.33-1.43,4.08-3.58,4.08H3.58C1.42,48.46,0,46.72,0,44.39A6.49,6.49,0,0,1,.15,43l2.49-11.3L8.75,3.94A5.13,5.13,0,0,1,13.5,0H116.58a5.15,5.15,0,0,1,4.75,3.94l6.11,27.8L129.93,43A6.32,6.32,0,0,1,130.08,44.38Z';
                    const topAnchorY = y - Math.max(8, Math.round(height * 0.2));
                    const squareScale = width / 130.08;
                    const squareTransform = `translate(${x} ${topAnchorY}) scale(${squareScale})`;

                    sideEl = document.createElementNS(ns, 'path');
                    sideEl.setAttribute('d', squareSideD);
                    sideEl.setAttribute('transform', squareTransform);

                    topEl = document.createElementNS(ns, 'path');
                    topEl.setAttribute('d', squareTopD);
                    topEl.setAttribute('transform', squareTransform);

                    glossEl = document.createElementNS(ns, 'path');
                    const gx = x + width * 0.12;
                    const gy = topAnchorY + width * 0.06;
                    const gh = Math.max(10, height * 0.72);
                    glossEl.setAttribute('d', `M ${gx} ${gy} L ${gx + width * 0.24} ${gy} L ${gx + width * 0.16} ${gy + gh} L ${gx} ${gy + gh} Z`);

                    if (dripMode !== 'none') {
                        const squareDripTopD = 'M131.13,44.93a5.55,5.55,0,0,1-.2,1.5,4,4,0,0,1-3.88,3.08H4.13A3.91,3.91,0,0,1,.29,46.59a5.44,5.44,0,0,1-.24-1.65,6.77,6.77,0,0,1,.16-1.45L2.7,32.18,8.81,4.38A5.6,5.6,0,0,1,14.05.05H117.13a5.63,5.63,0,0,1,5.24,4.33l6.11,27.8,2.45,11.12,0,.18A6.71,6.71,0,0,1,131.13,44.93Z';
                        const squareDripD = 'M130.93,46.43V79.12l-.08.08a1.1,1.1,0,0,1-1.22.35,2,2,0,0,1-1-.84,7,7,0,0,1-1-3.48c-.24-2.87-.22-5.76-.52-8.62a20.26,20.26,0,0,0-2.4-8.23c-.23-.39-.53-.8-1-.85a1.27,1.27,0,0,0-.71.17,3.69,3.69,0,0,0-2,3.6c.07.64.31,1.25.33,1.89a1.53,1.53,0,0,1-.87,1.56A1.4,1.4,0,0,1,119,64a4.07,4.07,0,0,1-.32-1.84c0-1,0-2,0-2.95a2.89,2.89,0,0,0-.85-2.47,1.91,1.91,0,0,0-2,0,5,5,0,0,0-1.46,1.43,8.7,8.7,0,0,0-1.29,2.2c-1,2.63.28,5.72-.82,8.3a1,1,0,0,1-1.9.24,7.61,7.61,0,0,1-1.24-4.65,47.17,47.17,0,0,0,0-4.9,3.93,3.93,0,0,0-.57-1.81,1.81,1.81,0,0,0-1.62-.84,11.33,11.33,0,0,0-.87,4.22,1,1,0,0,1-1-.42c0,1.47,0,2.94,0,4.41,0,.86-.13,1.95-.95,2.22a1.39,1.39,0,0,1-1.53-.72,3.79,3.79,0,0,1-.39-1.77c0-.76,0-1.52,0-2.28a35.29,35.29,0,0,1-.82-4.9,1,1,0,0,0-.82,0,1.55,1.55,0,0,0-.51.37,5.91,5.91,0,0,0-1.34,2.14,15.16,15.16,0,0,0-.68,5.18c0,1.48,0,3,0,4.43,0,1.17,0,2.35,0,3.52a1.5,1.5,0,0,1-.25,1,1,1,0,0,1-1.12.15,1.79,1.79,0,0,1-.78-.89c-.68-1.46-.28-3.21-.68-4.77-.19-.73-.55-1.41-.78-2.13a7.19,7.19,0,0,1-.29-1.3c-1,.73-2.59.66-3.4,1.66s-.41,2.37-.27,3.61c.18,1.74-.41,3.79-2,4.43a3.42,3.42,0,0,1-3.56-1,6.89,6.89,0,0,1-1.6-3.53,69.81,69.81,0,0,1-.54-7.09,25.49,25.49,0,0,0-1.58-6.84c-.36-.94-.91-2-1.89-2.16a2.17,2.17,0,0,0-2.24,1.52,6.38,6.38,0,0,0-.12,2.92,2.48,2.48,0,0,1-2.25-1.8,7.32,7.32,0,0,1-.15-3.06c-2.07.91-4.39,2.1-4.9,4.3a9.63,9.63,0,0,0,0,2.46,17.3,17.3,0,0,1-1.82,8.51,3.26,3.26,0,0,1-1.75,1.85c-1.23.38-2.47-.66-3.08-1.79a9.73,9.73,0,0,1-.73-6.3c.35-2,1-4.35-.38-5.87l-.09-.09a.85.85,0,0,0-.55-.29,1,1,0,0,0-.5.14A3.09,3.09,0,0,0,59.35,58a4.56,4.56,0,0,0-.45,1.3,15.46,15.46,0,0,0-.07,3.19,1.71,1.71,0,0,1-.14.9c-.39.72-1.51.57-2.14.05a5,5,0,0,1-1.37-3.93c0-.58,0-1.17,0-1.76A8.27,8.27,0,0,0,55,55.24c-.39-1.38-1.66-2.67-3.08-2.46a2.81,2.81,0,0,0-2,1.82,4.65,4.65,0,0,0,0,2.81c-.49.56-1.52.24-1.88-.42A3.28,3.28,0,0,1,48,54.78c.07-.34.16-.69.2-1a2.66,2.66,0,0,0,0-1.19c-.12-.41-.44-.81-.86-.79a1.08,1.08,0,0,0-.65.35A5.55,5.55,0,0,0,45.19,56c0,.87,0,1.77,0,2.62a41.76,41.76,0,0,1-.54,8.05c0,.35-.15.75-.48.89-.56.24-1-.52-1.13-1.12-.58-3.08,0-6.23.22-9.37a1.68,1.68,0,0,1-1.34-.3c-.15-.65-.31-1.3-.46-1.94-.73-.37-1.64.29-1.88,1.08a6.52,6.52,0,0,0,0,2.45,4.23,4.23,0,0,1-1,3.44,2.19,2.19,0,0,1-3.24-.17c-.6-.95-.11-2.21.51-3.15a5.78,5.78,0,0,0,1.41-3c0-1.13-1.41-2.21-2.24-1.45-.55.51-.45,1.47-1,2a1.2,1.2,0,0,1-1.9-.37c-.21-.57,0-1.25-.2-1.8a1.48,1.48,0,0,0-2.18-.33,3.83,3.83,0,0,0-1.17,2.18,26.08,26.08,0,0,0-.84,6.57,14.14,14.14,0,0,1-.35,4.46,3.55,3.55,0,0,1-3.22,2.65,3.31,3.31,0,0,1-2.7-2A8,8,0,0,1,20.89,64a29.9,29.9,0,0,1,1.19-8.36,5,5,0,0,0,.33-2.06,1.56,1.56,0,0,0-1.31-1.41,1.64,1.64,0,0,0-1.46,1.17,10.82,10.82,0,0,0-.34,2c-.26,1.46-1.72,3-3,2.3a7.34,7.34,0,0,0,.1-5.67A3.93,3.93,0,0,0,13.57,55c-.35,1.43-.25,2.94-.47,4.4a4,4,0,0,1-.95,2.23A1.81,1.81,0,0,1,9.92,62c.13-1.14.25-2.29.37-3.43-1.47-.23-2.71,1.06-3.52,2.31a20.7,20.7,0,0,0-3.22,9.54,5.17,5.17,0,0,1-.83,3.09c-.72.82-2.24.82-2.43-.26V46.59a3.91,3.91,0,0,0,3.84,2.92H127.05A4,4,0,0,0,130.93,46.43Z';
                        const dripScale = (width / 131.18) * dripOverscale;
                        const dripTx = x - ((130.08 * dripScale) - width) / 2;
                        const dripTy = topAnchorY - (0.6 * dripScale);
                        const dripColor = dripTone[dripMode] || darkenHex(topColor, 0.52);
                        const dripShadowColor = darkenHex(dripColor, 0.72);
                        frostingEl = document.createElementNS(ns, 'path');
                        frostingEl.setAttribute('d', squareDripTopD);
                        frostingEl.setAttribute('transform', `translate(${dripTx} ${dripTy}) scale(${dripScale})`);
                        frostingEl.setAttribute('fill', dripColor);
                        frostingEl.setAttribute('fill-opacity', '1');
                        frostingEl2 = document.createElementNS(ns, 'path');
                        frostingEl2.setAttribute('d', squareDripD);
                        frostingEl2.setAttribute('transform', `translate(${dripTx} ${dripTy}) scale(${dripScale})`);
                        frostingEl2.setAttribute('fill', dripShadowColor);
                        frostingEl2.setAttribute('fill-opacity', '1');
                    }
                } else if (shape === 'Heart') {
                    // Heart geometry mapped from provided heart1.svg source paths.
                    const heartTopD = 'M200.13,275.76c-1.96,18.34-61.61,32.62-64.65,33.34c-0.04-0.02-0.09-0.03-0.14-0.04c-0.06-0.02-0.14-0.03-0.22-0.05c-5.81-1.38-62.86-15.39-64.77-33.25c0.38-4.53,6.21-14.64,30.93-14.64c23.98,0,32.89,7.35,33.95,8.3c1.06-0.95,9.98-8.3,33.96-8.3C193.92,261.12,199.75,271.23,200.13,275.76z';
                    const heartSideD = 'M200.4,309.12c0,10.64-28.4,24.88-65.06,33.55c-0.04,0.01-0.07,0.02-0.11,0.02c-0.04,0-0.07-0.01-0.11-0.02c-36.66-8.67-65.04-22.91-65.04-33.55c0-4.25,0.03-18.05,0.24-33.6c0.01,0.08,0.01,0.16,0.03,0.24c1.91,17.86,58.96,31.87,64.77,33.25c0.08,0.02,0.16,0.03,0.22,0.05c0.05,0.01,0.1,0.02,0.14,0.04c3.04-0.72,62.69-15,64.65-33.34c0.02-0.08,0.02-0.16,0.03-0.24C200.37,291.07,200.4,304.87,200.4,309.12z';
                    const heartDripBodyD = 'M131,30a2.86,2.86,0,0,1-.05,1.51.94.94,0,0,1-1.16.64c-.51-.22-.6-.93-1.06-1.23a6,6,0,0,0-1.17,3.44c.06.65.33,1.25.45,1.89a4.41,4.41,0,0,1-.16,2.25,1,1,0,0,1-.54.68.91.91,0,0,1-.94-.5c-1-1.43-.25-3.4-.87-5-.13-.35-.43-.72-.79-.64a.94.94,0,0,0-.35.2,8.61,8.61,0,0,0-3.21,5c-.35,2.08.32,4.43-.87,6.17a1.84,1.84,0,0,1-1.71.91,2.07,2.07,0,0,1-1.3-1,12.62,12.62,0,0,1-1.41-4.4c-.2-1-.52-2.07-1.43-2.45a2.05,2.05,0,0,0-2.2.65,5.19,5.19,0,0,0-1,2.2c-.09.34-.22.74-.56.84s-.58-.07-.87-.1a1.26,1.26,0,0,0-1.17.8,3,3,0,0,0-.14,1.48,11.19,11.19,0,0,1,.16,3.73c-.12.46-.42,1-.89,1a1,1,0,0,1-.82-.57c-.71-1.16-.4-2.63-.48-4,0-.21-.07-.46-.26-.52s-.43.2-.51.43a8.48,8.48,0,0,0-.1,3.14.63.63,0,0,1,0,.36c-.2.38-.79.11-1.07-.22a6.22,6.22,0,0,1-.7-1,9.54,9.54,0,0,0-.47,3.09,14.08,14.08,0,0,1,.08,3.72c-.28,1.22-1.55,2.34-2.7,1.86-1.33-.56-1.18-2.45-1-3.87a21.66,21.66,0,0,0-.28-8.26c-.8-.53-1.74.48-2.07,1.38s-.77,2.05-1.73,2.05-1.46-1.27-2.32-1.78c-1.18-.68-2.69.28-3.37,1.46S89,47.9,88.12,49s-1.89,1.47-2.64,2.38c-2.26,2.74-.14,7-1.38,10.35-.55,1.49-2.67,2.68-3.62,1.41a3.06,3.06,0,0,1-.3-2.14,20.74,20.74,0,0,0-.36-6.14c-.37-1.76-1.48-3.83-3.27-3.66-1.24.12-2.09,1.34-2.47,2.53A18.93,18.93,0,0,1,73,57.26a1.66,1.66,0,0,1-.86.85c-1.08.33-1.79-1.14-1.83-2.26S70.19,53.26,69.1,53a5.49,5.49,0,0,0-2.66,5.48c-.26.57-1.2.19-1.4-.4s0-1.28-.25-1.86a1.89,1.89,0,0,0-2.5-.83,3.65,3.65,0,0,0-1.78,2.25c-1.17,3.4.35,7.15-.08,10.73a2.85,2.85,0,0,1-.75,1.82,1.24,1.24,0,0,1-1.78,0,1.8,1.8,0,0,1-.24-.55q-.93-3-1.87-6c-.63-2-1.26-4.17-.78-6.24.37-1.6,1.39-3,1.59-4.64s-1.11-3.63-2.68-3.17c-.66.19-1.24.78-1.91.66-.88-.15-1.15-1.35-1.93-1.77-1.22-.66-2.69.91-4,.51-1.51-.45-1.88-3.11-3.45-2.91-1.23.15-1.34,1.84-1.48,3.07a6.63,6.63,0,0,1-6.67,5.68c-.36-1.31.09-2.68.24-4s-.12-3-1.33-3.56c-1.54-.76-3.32.7-3.92,2.3s-.57,3.41-1.34,4.93c-2.1-.71-2.94-3.29-2.83-5.51s.87-4.4.7-6.61a2.65,2.65,0,0,0-.3-1.15c-.67-1.18-2.57-1-3.51,0a6.6,6.6,0,0,0-1.37,3.76c-.8.27-1.44-.76-1.43-1.59s.3-1.74-.1-2.48a2,2,0,0,0-2.84-.34,4.13,4.13,0,0,0-1.3,2.89c-.33,3.09.76,6.29-.13,9.27A2.12,2.12,0,0,1,14.09,54c-1,.54-2.22-.57-2.44-1.7s.12-2.31-.05-3.45a22.77,22.77,0,0,0-1.37-3.6c-1.05-3.31.88-6.91.32-10.33-.2-1.2-1.2-2.59-2.34-2.15a2.31,2.31,0,0,0-1.14,1.58,18.1,18.1,0,0,0-.6,8.49c-1.75.08-2.84-1.94-2.95-3.69s.28-3.59-.44-5.18a7.51,7.51,0,0,0-2.16,3A.88.88,0,0,1,.05,36V17.44a15.51,15.51,0,0,1,.11-1.83c2.67,18,58.68,31.79,65.1,33.31l.27.07.15.07.18,0c6.45-1.53,63.07-15.46,65-33.87,0-.08,0-.17,0-.25.54,3,.13,6.36.13,9.3Z';
                    const heartDripTopD = 'M130.89,15.15c-2,18.41-58.58,32.34-65,33.87l-.18,0L65.53,49l-.27-.07C58.84,47.4,2.83,33.59.16,15.61A12.63,12.63,0,0,1,3.92,8a5.66,5.66,0,0,1,.43-.42l.53-.47A20.07,20.07,0,0,1,7.53,5.22c.29-.18.6-.35.91-.53C13.16,2.11,20.2.18,30.44.06h1.1C54,.05,63.48,6.57,65.49,8.2,67.5,6.57,77,.05,99.45.05h1.1l1.08,0,1.06,0,1.43.07a53,53,0,0,1,13,2.21l.75.25.64.23.63.24.6.24.59.25a26.67,26.67,0,0,1,3.21,1.65l.39.25.87.58c.27.19.54.39.79.59a17.8,17.8,0,0,1,2,1.86c.2.21.38.42.56.63s.34.43.5.64.3.42.44.63.18.27.26.41.19.32.28.49.23.45.34.68.22.5.32.76a11.76,11.76,0,0,1,.44,1.54c0,.11,0,.22.06.33s0,.17,0,.26S130.88,15.07,130.89,15.15Z M.11,15.24A11.68,11.68,0,0,1,3.92,8,12.63,12.63,0,0,0,.16,15.61C.14,15.49.12,15.36.11,15.24Z';

                    const baseMinX = 70.08;
                    const baseMinY = 261.12;
                    const baseW = 130.32;
                    const s = width / baseW;
                    const topAnchorY = y - Math.max(10, Math.round(height * 0.2));
                    const tx = x - (baseMinX * s);
                    const ty = topAnchorY - (baseMinY * s);
                    const transformValue = `translate(${tx} ${ty}) scale(${s})`;

                    sideEl = document.createElementNS(ns, 'path');
                    sideEl.setAttribute('d', heartSideD);
                    sideEl.setAttribute('transform', transformValue);

                    topEl = document.createElementNS(ns, 'path');
                    topEl.setAttribute('d', heartTopD);
                    topEl.setAttribute('transform', transformValue);

                    if (dripMode !== 'none') {
                        const frostingScale = (width / 131.2) * dripOverscale;
                        const frostingTx = x - ((131.2 * frostingScale) - width) / 2;
                        const frostingTy = topAnchorY - (0.6 * frostingScale);
                        const dripColor = dripTone[dripMode] || darkenHex(topColor, 0.52);
                        const dripShadowColor = darkenHex(dripColor, 0.74);
                        frostingEl = document.createElementNS(ns, 'path');
                        frostingEl.setAttribute('d', heartDripTopD);
                        frostingEl.setAttribute('transform', `translate(${frostingTx} ${frostingTy}) scale(${frostingScale})`);
                        frostingEl.setAttribute('fill', dripColor);
                        frostingEl.setAttribute('fill-rule', 'evenodd');
                        frostingEl.setAttribute('clip-rule', 'evenodd');
                        frostingEl.setAttribute('fill-opacity', '1');
                        frostingEl2 = document.createElementNS(ns, 'path');
                        frostingEl2.setAttribute('d', heartDripBodyD);
                        frostingEl2.setAttribute('transform', `translate(${frostingTx} ${frostingTy}) scale(${frostingScale})`);
                        frostingEl2.setAttribute('fill', dripShadowColor);
                        frostingEl2.setAttribute('fill-rule', 'evenodd');
                        frostingEl2.setAttribute('clip-rule', 'evenodd');
                        frostingEl2.setAttribute('fill-opacity', '1');
                    }

                    const cx = x + width / 2;
                    const glossY = topAnchorY + (26 * s);
                    glossEl = document.createElementNS(ns, 'path');
                    glossEl.setAttribute('d', `M ${cx - width * 0.22} ${glossY} C ${cx - width * 0.30} ${glossY + 8}, ${cx - width * 0.24} ${glossY + 20}, ${cx - width * 0.08} ${glossY + 31}`);
                    glossEl.setAttribute('fill', 'none');
                    glossEl.setAttribute('stroke', 'url(#' + glossGradientId + ')');
                    glossEl.setAttribute('stroke-width', String(Math.max(3, width * 0.04)));
                    glossEl.setAttribute('stroke-linecap', 'round');
                } else {
                    // Round geometry mapped from provided circle1.svg source paths.
                    const roundTopD = 'M895.6,1021.12c-35.81,0-64.86,10.69-65,23.88c0.18,6.42,14.42,23.88,65,23.88c44.1,0,64.76-14.25,65-24C960.23,1031.72,931.27,1021.12,895.6,1021.12Z';
                    const roundSideD = 'M960.64,1045.12v33.6c0,13.25-29.12,24-65,24s-65-10.75-65-24V1045c0.18,6.42,14.42,23.88,65,23.88c44.1,0,64.76-14.25,65-24A2.62,2.62,0,0,1,960.64,1045.12Z';
                    const baseMinX = 830.56;
                    const baseMinY = 1021.12;
                    const baseW = 130.08;
                    const s = width / baseW;
                    const topAnchorY = y - Math.max(9, Math.round(height * 0.22));
                    const tx = x - (baseMinX * s);
                    const ty = topAnchorY - (baseMinY * s);
                    const transformValue = `translate(${tx} ${ty}) scale(${s})`;
                    const cx = x + width / 2;

                    sideEl = document.createElementNS(ns, 'path');
                    sideEl.setAttribute('d', roundSideD);
                    sideEl.setAttribute('transform', transformValue);

                    topEl = document.createElementNS(ns, 'path');
                    topEl.setAttribute('d', roundTopD);
                    topEl.setAttribute('transform', transformValue);

                    glossEl = document.createElementNS(ns, 'ellipse');
                    glossEl.setAttribute('cx', String(cx - width * 0.16));
                    glossEl.setAttribute('cy', String(y + Math.max(10, height * 0.36)));
                    glossEl.setAttribute('rx', String(Math.max(10, width * 0.16)));
                    glossEl.setAttribute('ry', String(Math.max(5, height * 0.18)));

                    if (dripMode !== 'none') {
                        const roundDripTopD = 'M201.1,285.1c0,0.2,0,0.5,0,0.7c-1,13.2-30,23.8-65.5,23.8c-35.5,0-64.5-10.6-65.5-23.8c0-0.2,0-0.5,0-0.7V285c0.2-13.5,29.6-24.4,65.5-24.4c36.3,0,65.1,10.6,65.5,24.2C201.1,284.9,201.1,285,201.1,285.1z';
                        const roundDripD = 'M201.2,289c-0.3,6.5-0.7,13-1,19.5c-3.4-2.6,0.6-8.8-2-12.1c-0.6,2.8-1.2,5.5-1.9,8.3c-0.2,0.8-0.9,1.9-1.6,1.4c-0.3-0.2-0.4-0.7-0.4-1c-0.2-1.7-0.4-3.4-0.6-5.1c-3.1,1.9-4.5,5.6-5.3,9.1c-0.8,3.5-1,7.2-2.7,10.5c-1.7,0.4-2.6-1.9-2.8-3.6c-0.3-3-0.6-6-0.9-9c-2.2-0.1-3.4,2.7-3.5,4.9c-0.2,2.2-0.3,4.9-2.3,6c-0.3-2.2-0.6-4.3-0.8-6.5c-0.1-0.5-0.1-1-0.4-1.4c-0.7-1.1-2.5-0.7-3.3,0.4c-0.8,1.1-0.8,2.4-0.8,3.7c-0.1,4.4-0.5,8.8-1.2,13.2c-0.1,0.5-0.2,1.1-0.6,1.5c-0.9,1-2.8,0.3-3.3-0.9c-0.6-1.2-0.3-2.7,0-4c0.5-2.8,0.8-5.8-0.4-8.4c-1.3-2.5-4.6-4.2-7.1-2.7c-2.2,1.4-2.5,4.5-2.5,7.1c-1.4-1.2-1.8-3.4-1-5c-2-0.5-3.8,1.6-3.9,3.6c-0.1,2,0.8,4,1.3,5.9c0.5,2,0.6,4.3-0.9,5.7c-2.9-2-4.2-5.8-3.9-9.2c0.2-1.8,0.9-3.7,0.6-5.5c-0.3-1.8-2-3.7-3.8-3.1c-1.6,0.5-2.1,2.5-3.3,3.7c-0.4,0.5-1.1,0.8-1.7,0.6c-1.2-0.5-0.3-2.6-1.3-3.4c-1.6,0.7-2.8,2.1-3.2,3.8c-0.9-1.1-1.5-2.5-1.6-3.9c-1.6,0-3.2,0.3-4.7,0.9c0.3,1.7,0.3,3.6-0.6,5.1c-1,1.5-3.1,2.2-4.5,1.2c-2-1.5-1.3-5.2-3.6-6.2c-0.7-0.3-1.5-0.2-2.2-0.1c-3.1,0.7-5.7,3.2-6.6,6.2c-0.3,1.1-0.4,2.3-1.1,3.3c-0.7,1-2.2,1.5-3,0.6c0-3.2,0-6.5,0-9.8c0-1.3-0.5-3.1-1.8-2.9c-1.1,0.1-1.4,1.6-1.5,2.7c-0.1,1.1-0.9,2.5-1.9,2.2c-1.6-0.5,0.2-3.4-0.8-4.8c-1.6,0.7-1.7,2.9-1.6,4.7c0.1,3.5-0.3,6.9-1.3,10.3c-0.2,0.8-0.6,1.6-1.4,1.9c-1.4,0.4-2.2-1.5-2.3-2.9c-0.5-5.5-0.7-11-0.4-16.5c-1.9-1.4-4,4-5.8,2.4c-1.2-1.1,0.8-3.5-0.3-4.6c-2.4-0.5-4.9,1.2-6,3.4c-1.1,2.2-1.2,4.8-1,7.3c0,0.8,0.1,1.7-0.2,2.5s-1.2,1.4-2,1.1c-0.7-0.2-1.1-1.1-1.2-1.8c-1.1-4.5-0.1-9.1,0.5-13.7c0.1-0.9,0.2-1.9-0.2-2.8c-0.4-0.9-1.4-1.5-2.3-1.1c-0.8,0.4-1,1.3-1.2,2.1c-0.6,3.2-1.2,6.3-1.9,9.5c-0.8,0.2-1.5-0.2-2.1-0.8v-26.4c1,13.2,30,23.8,65.5,23.8c35.5,0,64.5-10.6,65.5-23.8C201.2,286.9,201.2,287.9,201.2,289z';
                        const roundDripMinX = 70.1;
                        const roundDripMinY = 260.6;
                        const roundDripW = 131.1;
                        const dripScale = (width / roundDripW) * dripOverscale;
                        const dripTx = x - ((roundDripW * dripScale) - width) / 2 - (roundDripMinX * dripScale);
                        const dripTy = topAnchorY + (1.1 * dripScale) - (roundDripMinY * dripScale);
                        const dripColor = dripTone[dripMode] || darkenHex(topColor, 0.52);
                        const dripShadowColor = darkenHex(dripColor, 0.72);
                        const topDripScale = dripScale * circleTopDripExtraScale;
                        const topDripTx = x - ((roundDripW * topDripScale) - width) / 2 - (roundDripMinX * topDripScale);
                        const topDripTy = topAnchorY + (1.1 * topDripScale) - (roundDripMinY * topDripScale);
                        frostingEl = document.createElementNS(ns, 'path');
                        frostingEl.setAttribute('d', roundDripTopD);
                        frostingEl.setAttribute('transform', `translate(${topDripTx} ${topDripTy}) scale(${topDripScale})`);
                        frostingEl.setAttribute('fill', dripColor);
                        frostingEl.setAttribute('fill-opacity', '1');
                        frostingEl2 = document.createElementNS(ns, 'path');
                        frostingEl2.setAttribute('d', roundDripD);
                        frostingEl2.setAttribute('transform', `translate(${dripTx} ${dripTy}) scale(${dripScale})`);
                        frostingEl2.setAttribute('fill', dripShadowColor);
                        frostingEl2.setAttribute('fill-opacity', '1');
                    }
                }

                if (shape !== 'Square') {
                    sideEl.setAttribute('fill', `url(#${sideGradientId})`);
                    sideEl.setAttribute('stroke', 'rgba(122,66,82,0.24)');
                    sideEl.setAttribute('stroke-width', '1');
                } else {
                    sideEl.setAttribute('fill', `url(#${sideGradientId})`);
                    sideEl.setAttribute('stroke', 'rgba(122,66,82,0.24)');
                    sideEl.setAttribute('stroke-width', '1');
                }

                topEl.setAttribute('fill', `url(#${topGradientId})`);
                topEl.setAttribute('stroke', 'rgba(122,66,82,0.3)');
                topEl.setAttribute('stroke-width', '1');

                if (shape !== 'Heart') {
                    glossEl.setAttribute('fill', `url(#${glossGradientId})`);
                    glossEl.setAttribute('opacity', '0.6');
                }

                if (shouldFlipDrip) {
                    const mirrorX = x + (width / 2);
                    if (frostingEl) {
                        const t1 = frostingEl.getAttribute('transform') || '';
                        frostingEl.setAttribute('transform', `translate(${(2 * mirrorX).toFixed(3)} 0) scale(-1 1) ${t1}`);
                    }
                    if (frostingEl2) {
                        const t2 = frostingEl2.getAttribute('transform') || '';
                        frostingEl2.setAttribute('transform', `translate(${(2 * mirrorX).toFixed(3)} 0) scale(-1 1) ${t2}`);
                    }
                }

                if (frostingEl && frostingEl2) {
                    group.append(defs, sideEl, topEl, glossEl, frostingEl, frostingEl2);
                } else if (frostingEl) {
                    group.append(defs, sideEl, topEl, glossEl, frostingEl);
                } else {
                    group.append(defs, sideEl, glossEl, topEl);
                }
                return group;
            };

            const renderInsidePreview = () => {
                // 3D inside view handled by Three.js preview system
            };

            const shapeTopPath = {
                Round: '<circle cx="160" cy="160" r="108"></circle>',
                Square: '<rect x="52" y="52" width="216" height="216" rx="20"></rect>',
                Heart: '<path d="M160 268 C 98 228, 52 188, 52 134 C 52 102, 78 76, 110 76 C 132 76, 150 88, 160 108 C 170 88, 188 76, 210 76 C 242 76, 268 102, 268 134 C 268 188, 222 228, 160 268 Z"></path>'
            };

            const pointInTopShape = (shape, x, y) => {
                if (shape === 'Round') {
                    const dx = x - 160;
                    const dy = y - 160;
                    return ((dx * dx) + (dy * dy)) <= (108 * 108);
                }
                if (shape === 'Square') {
                    return x >= 52 && x <= 268 && y >= 52 && y <= 268;
                }
                // Heart implicit equation, with an inner edible area so toppings do not collect on the point.
                const nx = (x - 160) / 88;
                const ny = (y - 154) / 76;
                const v = Math.pow((nx * nx) + (ny * ny) - 1, 3) - (nx * nx * Math.pow(ny, 3));
                const lowerPointInset = y > 198 ? (y - 198) * 0.95 : 0;
                return v <= -0.012
                    && y >= 92
                    && y <= 222
                    && x >= 72 + lowerPointInset
                    && x <= 248 - lowerPointInset;
            };

            const randomPointInTopShape = (shape) => {
                const textSafeZone = (() => {
                    const msg = (messageInput.value || '').trim();
                    if (!msg) return null;
                    const hardLines = msg.split(/\r?\n/).map((s) => s.trim()).filter(Boolean);
                    const wrappedLines = [];
                    const approxCharsPerLine = 16;
                    hardLines.forEach((line) => {
                        if (line.length <= approxCharsPerLine) {
                            wrappedLines.push(line);
                            return;
                        }
                        let start = 0;
                        while (start < line.length) {
                            wrappedLines.push(line.slice(start, start + approxCharsPerLine));
                            start += approxCharsPerLine;
                        }
                    });
                    const effectiveLines = Math.max(1, wrappedLines.length || 1);
                    const longestLine = wrappedLines.reduce((m, l) => Math.max(m, l.length), 0) || msg.length;
                    const len = Math.max(10, Math.min(34, longestLine));
                    return {
                        cx: 160,
                        cy: 160,
                        rx: 52 + (len * 2.8),
                        ry: 26 + (effectiveLines * 12) + (len * 0.35)
                    };
                })();
                const insideTextSafeZone = (x, y) => {
                    if (!textSafeZone) return false;
                    const nx = (x - textSafeZone.cx) / textSafeZone.rx;
                    const ny = (y - textSafeZone.cy) / textSafeZone.ry;
                    return ((nx * nx) + (ny * ny)) <= 1;
                };
                const bounds = shape === 'Square'
                    ? { minX: 52, maxX: 268, minY: 52, maxY: 268 }
                    : shape === 'Heart'
                        ? { minX: 72, maxX: 248, minY: 92, maxY: 222 }
                        : { minX: 52, maxX: 268, minY: 52, maxY: 268 };
                for (let tries = 0; tries < 300; tries++) {
                    const x = bounds.minX + Math.random() * (bounds.maxX - bounds.minX);
                    const y = bounds.minY + Math.random() * (bounds.maxY - bounds.minY);
                    if (pointInTopShape(shape, x, y) && !insideTextSafeZone(x, y)) return { x, y };
                }
                return { x: 160, y: 160 };
            };

            const toppingSvg = (shape, x, y, color, idx) => {
                if (shape === 'sprinkle') {
                    const item = toppingItems[idx] || {};
                    const length = Number(item.length || 16);
                    const rotation = Number(item.rotation || 0);
                    const thickness = Number(item.thickness || 5);
                    return `<g transform="translate(${x} ${y}) rotate(${rotation})"><line x1="${(-length / 2).toFixed(2)}" y1="0" x2="${(length / 2).toFixed(2)}" y2="0" stroke="${color}" stroke-width="${thickness.toFixed(2)}" stroke-linecap="round"/></g>`;
                }
                if (shape === 'chip') {
                    const item = toppingItems[idx] || {};
                    const rotation = Number(item.rotation || 0);
                    const scale = Number(item.scale || 1);
                    // Irregular chocolate-chip silhouette
                    return `<g transform="translate(${x} ${y}) rotate(${rotation}) scale(${scale})"><path d="M -6 -1.5 C -5 -6.2, 1.2 -8.1, 5.5 -4.8 C 8.9 -2.4, 8.2 2.8, 4.2 6 C 0.4 8.8, -4.4 7.2, -6.8 3.8 C -8.3 1.9, -7.9 0, -6 -1.5 Z" fill="${color}" stroke="#2f1c12" stroke-width="0.7"/></g>`;
                }
                if (shape === 'nut') {
                    const item = toppingItems[idx] || {};
                    const rotation = Number(item.rotation || 0);
                    const scale = Number(item.scale || 1);
                    return `<g transform="translate(${x} ${y}) rotate(${rotation}) scale(${scale})"><path d="M -5 -2 C -3.5 -6, 2.8 -6.8, 5.2 -3.2 C 6.8 -0.9, 5.8 2.2, 3.4 4.1 C 0.6 6.2, -3.6 5.7, -5.4 2.5 C -6.2 1, -6.1 -0.7, -5 -2 Z" fill="${color}" stroke="#8b5a2b" stroke-width="0.5"/></g>`;
                }
                if (shape === 'heart') {
                    return `<path d="M ${x} ${y + 8} c -8 -6 -14 -11 -14 -17 c 0 -5 4 -9 9 -9 c 3 0 6 2 7 5 c 1 -3 4 -5 7 -5 c 5 0 9 4 9 9 c 0 6 -6 11 -14 17 z" fill="${color}" stroke="#b84f74" stroke-width="1"/>`;
                }
                if (shape === 'flower') {
                    return `<g><circle cx="${x}" cy="${y}" r="6" fill="${color}"/><circle cx="${x - 9}" cy="${y}" r="5" fill="${color}"/><circle cx="${x + 9}" cy="${y}" r="5" fill="${color}"/><circle cx="${x}" cy="${y - 9}" r="5" fill="${color}"/><circle cx="${x}" cy="${y + 9}" r="5" fill="${color}"/><circle cx="${x}" cy="${y}" r="3" fill="#fff6f8"/></g>`;
                }
                if (shape === 'star') {
                    return `<path d="M ${x} ${y - 10} L ${x + 4} ${y - 2} L ${x + 13} ${y - 1} L ${x + 6} ${y + 5} L ${x + 8} ${y + 13} L ${x} ${y + 8} L ${x - 8} ${y + 13} L ${x - 6} ${y + 5} L ${x - 13} ${y - 1} L ${x - 4} ${y - 2} Z" fill="${color}" stroke="#b84f74" stroke-width="1"/>`;
                }
                return `<circle cx="${x}" cy="${y}" r="8" fill="${color}" stroke="#b84f74" stroke-width="1"/>`;
            };

            const sync3DPreview = () => {
                toppingsHiddenInput.value = JSON.stringify(toppingItems);
                const [frostingTop, frostingBottom] = getFrostingTone();
                const detail = {
                    shape: shapeSelect.value || 'Round',
                    size: sizeSelect.value || '6',
                    layers: layersSelect.value || '1',
                    sponge: spongeSelect.value || 'Vanilla',
                    filling: fillingSelect.value || 'Vanilla Cream',
                    spongeColor: spongeTone[spongeSelect.value] || '#f5d7a5',
                    fillingColor: fillingTone[fillingSelect.value] || '#f6f0dc',
                    frostingTop,
                    frostingBottom,
                    drip: dripSelect.value || 'none',
                    topper: topperSelect.value || 'none',
                    message: messageInput.value || '',
                    textColor: textColorInput.value || '#7A3444',
                    toppings: toppingItems.map((item) => ({ ...item })),
                };

                window.__bonbonCustomize3DState = detail;
                window.dispatchEvent(new CustomEvent('bonbon-customize-3d:update', { detail }));
                window.BonbonCustomize3D?.update?.(detail);
            };

            const renderTopView = () => {
                sync3DPreview();
            };



            const updateStepView = () => {
                stepSections.forEach((section) => {
                    const isVisible = Number(section.dataset.step) === currentStep;
                    section.classList.toggle('hidden', !isVisible);
                });
                stepPills.forEach((pill, idx) => {
                    const active = (idx + 1) === currentStep;
                    pill.classList.toggle('bg-pink-600', active);
                    pill.classList.toggle('text-white', active);
                    pill.classList.toggle('bg-[#F7E7EB]', !active);
                    pill.classList.toggle('text-[#7A5252]', !active);
                });
                stepLabelEl.textContent = `Step ${currentStep} of 2`;
                prevStepBtn.disabled = currentStep === 1;
                nextStepBtn.classList.toggle('hidden', currentStep === 2);
                submitBtn.classList.toggle('hidden', currentStep !== 2);
                if (currentStep === 1) nextStepBtn.textContent = 'Next: Toppings';
                if (currentStep === 2) nextStepBtn.textContent = 'Review & Submit';
                if (currentStep === 2 && typeof setView === 'function') setView('top');
                renderTopView();
            };

            const renderCake = () => {
                sync3DPreview();
            };

            const compute = () => {
                const subtotal =
                    Number(pricing.size[sizeSelect.value] || 0) +
                    Number(pricing.layers[layersSelect.value] || 0) +
                    Number(pricing.sponge[spongeSelect.value] || 0) +
                    Number(pricing.filling[fillingSelect.value] || 0) +
                    Number(pricing.frosting[frostingSelect.value] || 0) +
                    Number(pricing.drip[dripSelect.value] || 0) +
                    Number(pricing.topper[topperSelect.value] || 0) +
                    Number(pricing.rush[rushCheckbox.checked ? 'yes' : 'no'] || 0) +
                    (Number(pricing.toppings?.per_piece || 0) * toppingItems.length);

                rushHidden.value = rushCheckbox.checked ? 'yes' : 'no';
                frostingCustomHidden.value = frostingSelect.value === 'custom' ? (frostingCustomInput.value || '') : '';
                if (addonEl) addonEl.textContent = php(subtotal);
                if (totalEl) totalEl.textContent = php(subtotal);
                renderCake();
            };

            const syncFrostingSwatchUI = () => {
                frostingSwatches.forEach((btn) => {
                    const active = btn.dataset.frosting === frostingSelect.value;
                    btn.classList.toggle('border-2', active);
                    btn.classList.toggle('border-[#ec5a61]', active);
                    btn.classList.toggle('ring-4', active);
                    btn.classList.toggle('ring-[#f3d7dd]', active);
                    if (btn.dataset.frosting === 'custom') {
                        btn.textContent = '+';
                        return;
                    }
                    if (btn.dataset.frosting === 'mocha') {
                        btn.textContent = active ? '\u2713' : '';
                    }
                });
            };

            [sizeSelect, layersSelect, spongeSelect, fillingSelect, frostingSelect, dripSelect, topperSelect, rushCheckbox, shapeSelect, messageInput, textColorInput].forEach((el) => {
                el.addEventListener('change', compute);
            });
            [messageInput, textColorInput].forEach((el) => el.addEventListener('input', compute));
            frostingSwatches.forEach((btn) => {
                btn.addEventListener('click', () => {
                    frostingSelect.value = btn.dataset.frosting;
                    syncFrostingSwatchUI();
                    compute();
                });
            });
            frostingCustomBtn.addEventListener('click', () => {
                frostingSelect.value = 'custom';
                syncFrostingSwatchUI();
                if (typeof frostingCustomInput.showPicker === 'function') {
                    frostingCustomInput.showPicker();
                } else {
                    frostingCustomInput.click();
                }
                compute();
            });
            frostingCustomInput.addEventListener('input', () => {
                frostingSelect.value = 'custom';
                syncCustomFrostingSwatch();
                syncFrostingSwatchUI();
                compute();
            });

            const addSingleShapeTopping = (shapeType) => {
                const shape = shapeSelect.value || 'Round';
                const pt = randomPointInTopShape(shape);
                toppingItems.push({
                    shape: shapeType,
                    color: toppingColorInput.value,
                    x: pt.x,
                    y: pt.y
                });
                compute();
            };

            const removeSingleShapeTopping = (shapeType) => {
                for (let i = toppingItems.length - 1; i >= 0; i--) {
                    if (toppingItems[i].shape === shapeType && !toppingItems[i].preset) {
                        toppingItems.splice(i, 1);
                        break;
                    }
                }
                compute();
            };

            const addPresetToppings = (preset) => {
                const setConfig = {
                    sprinkles: { count: 24, palette: ['#e772aa', '#6ac39a', '#2fa8df', '#f9df00', '#f06f4f', '#887fc2'], shape: 'sprinkle', rMin: 24, rVar: 78 },
                    sprinkles_choco: { count: 24, palette: ['#2f1b14', '#45291d', '#5a3526', '#3b2219', '#6a3f2d'], shape: 'sprinkle', rMin: 24, rVar: 78 },
                    sprinkles_white: { count: 24, palette: ['#fffaf0', '#f7f1e3', '#f2ede2', '#efe7d8', '#faf6ee'], shape: 'sprinkle', rMin: 24, rVar: 78 },
                    chips: { count: 26, palette: ['#5a331a', '#6a3f1f', '#70431f', '#4e2d17'], shape: 'chip', rMin: 22, rVar: 82 },
                    nuts: { count: 22, palette: ['#b8742f', '#c98b45', '#f3e7cc', '#e9dbc1', '#9f6328'], shape: 'nut', rMin: 20, rVar: 86 },
                    pearls: { count: 14, palette: ['#f8efe0', '#f3e7cf', '#efe2d2'], shape: 'dot', rMin: 20, rVar: 72 }
                };
                const cfg = setConfig[preset];
                if (!cfg) return;
                const isActive = activeToppingPresets.has(preset);
                if (isActive) {
                    for (let i = toppingItems.length - 1; i >= 0; i--) {
                        if (toppingItems[i].preset === preset) toppingItems.splice(i, 1);
                    }
                    activeToppingPresets.delete(preset);
                    quickToppingBtns.forEach((b) => {
                        if (b.dataset.preset === preset) {
                            b.classList.remove('bg-[#FDECEF]', 'border-[#ec5a61]', 'text-[#5A3A3A]');
                            b.classList.add('bg-[#FFF7F7]', 'border-[#F3D7DB]', 'text-[#7A5252]');
                        }
                    });
                    compute();
                    return;
                }

                // Replace all existing pieces from the same preset, then add fresh randomized set.
                for (let i = toppingItems.length - 1; i >= 0; i--) {
                    if (toppingItems[i].preset === preset) toppingItems.splice(i, 1);
                }
                const shape = shapeSelect.value || 'Round';
                for (let i = 0; i < cfg.count; i++) {
                    const idx = toppingItems.length;
                    const pt = randomPointInTopShape(shape);
                    const color = cfg.palette[(idx + i) % cfg.palette.length];
                    const rotation = ((idx * 31) + (i * 23)) % 180;
                    const length = 10 + ((idx + i) % 10); // 10..19
                    const thickness = 4 + (((idx + i) % 3) * 0.9); // 4..5.8
                    const chipScale = 0.7 + (((idx + i) % 6) * 0.08); // 0.7..1.1
                    const nutScale = 0.45 + (((idx + i) % 8) * 0.07); // 0.45..0.94
                    toppingItems.push({
                        shape: cfg.shape,
                        color,
                        x: pt.x,
                        y: pt.y,
                        rotation,
                        length,
                        thickness,
                        scale: cfg.shape === 'nut' ? nutScale : chipScale,
                        preset
                    });
                }
                activeToppingPresets.add(preset);
                quickToppingBtns.forEach((b) => {
                    if (b.dataset.preset === preset) {
                        b.classList.remove('bg-[#FFF7F7]', 'border-[#F3D7DB]', 'text-[#7A5252]');
                        b.classList.add('bg-[#FDECEF]', 'border-[#ec5a61]', 'text-[#5A3A3A]');
                    }
                });
                compute();
            };

            clearToppingsBtn.addEventListener('click', () => {
                toppingItems.length = 0;
                activeToppingPresets.clear();
                quickToppingBtns.forEach((b) => {
                    b.classList.remove('bg-[#FDECEF]', 'border-[#ec5a61]', 'text-[#5A3A3A]');
                    b.classList.add('bg-[#FFF7F7]', 'border-[#F3D7DB]', 'text-[#7A5252]');
                });
                compute();
            });

            quickToppingBtns.forEach((btn) => {
                btn.addEventListener('click', () => addPresetToppings(btn.dataset.preset));
            });
            shapeAdjustBtns.forEach((btn) => {
                btn.addEventListener('click', () => {
                    const shapeType = btn.dataset.shape;
                    const action = btn.dataset.action;
                    if (!shapeType || !action) return;
                    if (action === 'add') addSingleShapeTopping(shapeType);
                    if (action === 'remove') removeSingleShapeTopping(shapeType);
                });
            });
            // Tab switching is handled by the 3D preview system below

            prevStepBtn.addEventListener('click', () => {
                currentStep = Math.max(1, currentStep - 1);
                updateStepView();
            });

            nextStepBtn.addEventListener('click', () => {
                currentStep = Math.min(2, currentStep + 1);
                updateStepView();
            });

            compute();
            syncCustomFrostingSwatch();
            syncFrostingSwatchUI();
            updateStepView();
        })();
    </script>

    {{-- ═══ THREE.JS 3D CAKE PREVIEW ═══ --}}
    <script>
    function initCake3DPreview() {
        const wrap = document.getElementById('cake-3d-wrap');
        const canvas = document.getElementById('cake-3d-canvas');
        if (!wrap || !canvas || typeof THREE === 'undefined') {
            console.warn('cake-3d: container or Three.js missing');
            return;
        }

        // Wait until the wrap actually has real pixel dimensions
        if (!wrap.clientWidth || !wrap.clientHeight) {
            requestAnimationFrame(initCake3DPreview);
            return;
        }

        // ── Renderer / Scene / Camera ──────────────────────────────────────
        const scene = new THREE.Scene();
        let W = wrap.clientWidth, H = wrap.clientHeight;
        const camera = new THREE.PerspectiveCamera(38, W / H, 0.1, 100);
        camera.position.set(0, 1.2, 6.4);
        camera.lookAt(0, 0.2, 0);

        const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true, powerPreference: 'high-performance' });
        renderer.setSize(W, H);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.15;
        renderer.shadowMap.enabled = true;
        renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        renderer.localClippingEnabled = true;

        // ── Lighting ──────────────────────────────────────────────────────
        scene.add(new THREE.AmbientLight(0xffe8f2, 0.95));
        const key = new THREE.DirectionalLight(0xfff4ec, 2.2);
        key.position.set(4, 8, 6); key.castShadow = true;
        key.shadow.mapSize.set(1024, 1024); key.shadow.bias = -0.002;
        scene.add(key);
        const fill = new THREE.DirectionalLight(0xffcce8, 0.7); fill.position.set(-4, 3, 3); scene.add(fill);
        const rim  = new THREE.DirectionalLight(0xffd4b8, 0.4); rim.position.set(0, 2, -5);  scene.add(rim);
        const front= new THREE.PointLight(0xfff0f6, 0.55, 14); front.position.set(0, 2, 7);  scene.add(front);

        // Ground reflection disc
        const groundM = new THREE.MeshStandardMaterial({ color: 0xffb8d0, transparent: true, opacity: 0.18, roughness: 1 });
        const groundMesh = new THREE.Mesh(new THREE.CircleGeometry(3, 48), groundM);
        groundMesh.rotation.x = -Math.PI / 2; groundMesh.position.y = -0.01; groundMesh.receiveShadow = true;
        scene.add(groundMesh);

        // ── Groups ────────────────────────────────────────────────────────
        const cakeGroup = new THREE.Group(); scene.add(cakeGroup);
        const cutGroup  = new THREE.Group(); scene.add(cutGroup);  cutGroup.visible = false;

        // Clipping plane: reveal only x > 0 (right half of cake)
        const HALF_PLANE = new THREE.Plane(new THREE.Vector3(-1, 0, 0), 0.02);

        // ── Color Maps ────────────────────────────────────────────────────
        const FROST = {
            white:'#f2f2f2', ivory:'#e9e2cf', blush:'#edd3d6',
            sage:'#d2e1d8', powder_blue:'#d6e1ea', chocolate:'#4a2f1f',
            mocha:'#6a4638', lavender:'#d7b2ef'
        };
        const SPONGE = {
            'Vanilla':'#f5d7a5','Chocolate':'#7b4a38','Red Velvet':'#a43b4a',
            'Lemon':'#f3e38a','Strawberry':'#f3a6b8','Funfetti':'#f7e3b8','Ube':'#b9a0da'
        };
        const FILL = {
            'Chocolate Mousse':'#6a3d2d','Strawberry Jam':'#cf4f6a',
            'Vanilla Cream':'#f0e8d4','Nutella':'#5a3528',
            'Cookies & Cream':'#d5d2dd','Buttercream':'#f6dfb2'
        };
        const fillingStyle = {
            'Chocolate Mousse': { accent: '#7c4e3c', chunks: 8, drip: 0.7, rough: 0.55, metal: 0.08 },
            'Strawberry Jam': { accent: '#f08aa4', chunks: 14, drip: 1.25, rough: 0.24, metal: 0.02 },
            'Vanilla Cream': { accent: '#fff8ea', chunks: 7, drip: 0.65, rough: 0.62, metal: 0.01 },
            Nutella: { accent: '#7b4a37', chunks: 9, drip: 0.8, rough: 0.42, metal: 0.06 },
            'Cookies & Cream': { accent: '#6a6470', chunks: 16, drip: 0.6, rough: 0.72, metal: 0.02 },
            Buttercream: { accent: '#ffe9c6', chunks: 6, drip: 0.55, rough: 0.68, metal: 0.01 },
        };
        const DRIP = { none:null, chocolate:'#3f2219', white_chocolate:'#fff6ea', pink:'#f26ca1', caramel:'#b66a3d' };

        // ── Helpers ───────────────────────────────────────────────────────
        const tc = hex => new THREE.Color(hex.startsWith('#') ? hex : '#' + hex);
        const darken3dHex = (hex, factor = 0.75) => {
            const value = String(hex || '').replace('#', '');
            if (value.length !== 6) return hex;
            const r = Math.max(0, Math.min(255, Math.round(parseInt(value.slice(0, 2), 16) * factor)));
            const g = Math.max(0, Math.min(255, Math.round(parseInt(value.slice(2, 4), 16) * factor)));
            const b = Math.max(0, Math.min(255, Math.round(parseInt(value.slice(4, 6), 16) * factor)));
            return `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`;
        };
        const mkMat = (hex, o = {}) => new THREE.MeshStandardMaterial({
            color: tc(hex),
            roughness: o.r ?? 0.62,
            metalness: o.m ?? 0.02,
            transparent: !!o.t,
            opacity: o.o ?? 1,
            side: o.side ?? THREE.FrontSide,
            clippingPlanes: o.clip ?? [],
        });

        const mkDrips = (grp, radius, height, dripHex, clip) => {
            if (!dripHex) return;
            const dm = mkMat(dripHex, { r: 0.1, m: 0.04, clip });
            const n = 24;
            for (let i = 0; i < n; i++) {
                const a = (i / n) * Math.PI * 2;
                const len = 0.036 + Math.sin(i * 1.9 + 0.4) * 0.026 + (i % 4 === 0 ? 0.058 : 0);
                const dc = new THREE.Mesh(new THREE.CylinderGeometry(0.019, 0.011, len, 7), dm);
                dc.position.set(Math.cos(a) * radius, height / 2 - len / 2 + 0.009, Math.sin(a) * radius);
                dc.castShadow = true; grp.add(dc);
                const bb = new THREE.Mesh(new THREE.SphereGeometry(0.024, 7, 7), dm);
                bb.position.set(Math.cos(a) * radius, height / 2 - len + 0.007, Math.sin(a) * radius);
                bb.scale.y = 1.38; grp.add(bb);
            }
        };

        // ── Tier Builders ─────────────────────────────────────────────────
        const buildRound = (r, h, fHex, dHex, clip) => {
            const g = new THREE.Group(); const cp = clip ? [HALF_PLANE] : [];
            const sm = mkMat(fHex, { r: 0.55, clip: cp });
            g.add(Object.assign(new THREE.Mesh(new THREE.CylinderGeometry(r, r, h, 80, 1, true), sm), { castShadow: true }));
            const tm = mkMat(fHex, { r: 0.46, clip: cp });
            const top = new THREE.Mesh(new THREE.CircleGeometry(r, 80), tm);
            top.rotation.x = -Math.PI / 2; top.position.y = h / 2; g.add(top);
            const bot = new THREE.Mesh(new THREE.CircleGeometry(r, 80), mkMat(fHex, { r: 0.72, clip: cp }));
            bot.rotation.x = Math.PI / 2; bot.position.y = -h / 2; g.add(bot);
            mkDrips(g, r, h, dHex, cp); return g;
        };

        const buildSquare = (s, h, fHex, dHex, clip) => {
            const g = new THREE.Group(); const cp = clip ? [HALF_PLANE] : [];
            g.add(Object.assign(new THREE.Mesh(new THREE.BoxGeometry(s * 2, h, s * 2), mkMat(fHex, { r: 0.55, clip: cp })), { castShadow: true }));
            if (dHex) {
                const dm = mkMat(dHex, { r: 0.1, clip: cp });
                [0, 1, 2, 3].forEach(side => {
                    for (let i = 0; i < 11; i++) {
                        const t = (i + 0.5) / 11;
                        const len = 0.028 + Math.sin(i * 2.3) * 0.018 + (i % 3 === 0 ? 0.042 : 0);
                        let x = 0, z = 0;
                        if (side === 0) { x = -s + t * s * 2; z = s; }
                        else if (side === 1) { x = s; z = s - t * s * 2; }
                        else if (side === 2) { x = s - t * s * 2; z = -s; }
                        else { x = -s; z = -s + t * s * 2; }
                        const dc = new THREE.Mesh(new THREE.CylinderGeometry(0.015, 0.009, len, 6), dm);
                        dc.position.set(x, h / 2 - len / 2, z); g.add(dc);
                    }
                });
            }
            return g;
        };

        const buildHeart = (r, h, fHex, clip) => {
            const g = new THREE.Group(); const cp = clip ? [HALF_PLANE] : [];
            const shape = new THREE.Shape();
            const s = r * 0.88;
            shape.moveTo(0, -s * 0.72);
            shape.bezierCurveTo(-s * 1.22, -s * 0.82, -s * 1.32, s * 0.18, -s * 0.92, s * 0.56);
            shape.bezierCurveTo(-s * 0.66, s * 0.97, -s * 0.1, s * 0.72, 0, s * 1.12);
            shape.bezierCurveTo(s * 0.1, s * 0.72, s * 0.66, s * 0.97, s * 0.92, s * 0.56);
            shape.bezierCurveTo(s * 1.32, s * 0.18, s * 1.22, -s * 0.82, 0, -s * 0.72);
            const geo = new THREE.ExtrudeGeometry(shape, { depth: h, bevelEnabled: false });
            geo.rotateX(-Math.PI / 2); geo.translate(0, h / 2, 0);
            g.add(Object.assign(new THREE.Mesh(geo, mkMat(fHex, { r: 0.55, clip: cp, side: THREE.DoubleSide })), { castShadow: true }));
            return g;
        };

        const buildCutFace = (p, tiers) => {
            const g = new THREE.Group();
            const TH = 0.62, GAP = 0.04;
            // Tier heights - tall filling (35%) like real cake
            const sH  = TH * 0.32;  // bottom sponge
            const fH  = TH * 0.30;  // filling (thick, like photo)
            const s2H = TH * 0.32;  // top sponge
            const frH = TH * 0.06;  // frosting cap layer
            const fillFx = fillingStyle[p.filling] || fillingStyle['Vanilla Cream'];

            let y = 0;
            for (let t = 0; t < tiers; t++) {
                const r = Math.max(0.55, 1.1 * (1 + (p.size - 6) * 0.045) - t * 0.22);
                const W = r * 2; // full width of cross-section
                const FROST_COAT = 0.06; // frosting thickness on outside

                // ── CROSS-SECTION FACE (flat cut plane at x=0) ──────────────
                // Each layer is a PlaneGeometry facing +X direction

                // Frosting top cap face
                const frCapFace = new THREE.Mesh(
                    new THREE.PlaneGeometry(W, frH),
                    mkMat(p.frostHex, { r: 0.45, side: THREE.DoubleSide })
                );
                frCapFace.rotation.y = -Math.PI / 2;
                frCapFace.position.set(0, y + TH - frH / 2, 0);
                g.add(frCapFace);

                // Top sponge face
                const sp2Face = new THREE.Mesh(
                    new THREE.PlaneGeometry(W, s2H),
                    mkMat(p.spongeHex, { r: 0.9, side: THREE.DoubleSide })
                );
                sp2Face.rotation.y = -Math.PI / 2;
                sp2Face.position.set(0, y + sH + fH + s2H / 2, 0);
                g.add(sp2Face);

                // Filling face – thick, vibrant band
                const fillFace = new THREE.Mesh(
                    new THREE.PlaneGeometry(W, fH),
                    mkMat(p.fillHex, { r: 0.28, side: THREE.DoubleSide })
                );
                fillFace.rotation.y = -Math.PI / 2;
                fillFace.position.set(0, y + sH + fH / 2, 0);
                if (fillFace.material) {
                    fillFace.material.roughness = fillFx.rough;
                    fillFace.material.metalness = fillFx.metal;
                    fillFace.material.polygonOffset = true;
                    fillFace.material.polygonOffsetFactor = 1;
                    fillFace.material.polygonOffsetUnits = 1;
                }
                g.add(fillFace);

                for (let i = 0; i < fillFx.chunks; i++) {
                    const zJitter = -r * 0.78 + (i / Math.max(1, fillFx.chunks - 1)) * (r * 1.56);
                    const yJitter = y + sH + fH * (0.2 + ((i * 37) % 55) / 100);
                    const dot = new THREE.Mesh(
                        new THREE.SphereGeometry(0.018 + (i % 3) * 0.005, 8, 8),
                        mkMat(fillFx.accent, { r: 0.35, side: THREE.DoubleSide })
                    );
                    dot.position.set(0.01 + (i % 2) * 0.004, yJitter, zJitter);
                    dot.scale.set(1.25, 0.8, 1.1);
                    dot.castShadow = false;
                    g.add(dot);
                }

                // Bottom sponge face
                const sp1Face = new THREE.Mesh(
                    new THREE.PlaneGeometry(W, sH),
                    mkMat(p.spongeHex, { r: 0.9, side: THREE.DoubleSide })
                );
                sp1Face.rotation.y = -Math.PI / 2;
                sp1Face.position.set(0, y + sH / 2, 0);
                g.add(sp1Face);

                // ── FROSTING COAT borders on the cut face edges ──────────────
                // Left strip
                const frLeft = new THREE.Mesh(
                    new THREE.PlaneGeometry(FROST_COAT, TH),
                    mkMat(p.frostHex, { r: 0.45, side: THREE.DoubleSide })
                );
                frLeft.rotation.y = -Math.PI / 2;
                frLeft.position.set(0, y + TH / 2, -r + FROST_COAT / 2);
                g.add(frLeft);

                // Right strip
                const frRight = new THREE.Mesh(
                    new THREE.PlaneGeometry(FROST_COAT, TH),
                    mkMat(p.frostHex, { r: 0.45, side: THREE.DoubleSide })
                );
                frRight.rotation.y = -Math.PI / 2;
                frRight.position.set(0, y + TH / 2, r - FROST_COAT / 2);
                g.add(frRight);

                // ── FILLING OOZE / DRIPS from cut edges ──────────────────────
                const drpM = mkMat(p.fillHex, { r: 0.14, t: true, o: 0.92, side: THREE.DoubleSide });
                const fillCenterY = y + sH + fH * 0.5;
                const numDrips = Math.max(3, Math.round((6 + Math.floor(r * 4)) * fillFx.drip));
                for (let d = 0; d < numDrips; d++) {
                    const zPos = -r * 0.82 + (d / (numDrips - 1)) * r * 1.64;
                    const dLen = (0.03 + (d % 4 === 0 ? 0.09 : 0.025) + Math.abs(Math.sin(d * 2.3)) * 0.04) * fillFx.drip;
                    const dc = new THREE.Mesh(new THREE.CylinderGeometry(0.016, 0.009, dLen, 6), drpM);
                    dc.position.set(0, fillCenterY - fH * 0.3 - dLen / 2, zPos);
                    g.add(dc);
                    const bb = new THREE.Mesh(new THREE.SphereGeometry(0.021, 7, 7), drpM);
                    bb.position.set(0, fillCenterY - fH * 0.3 - dLen, zPos);
                    bb.scale.set(1.2, 1.5, 1.2);
                    g.add(bb);
                }

                // ── TOP FROSTING DOLLOPS (piped cream on top, like photo) ────
                if (t === tiers - 1) {
                    const pipeMat = mkMat(p.frostHex, { r: 0.35 });
                    const nPipes = Math.round(r * 3.5);
                    for (let i = 0; i < nPipes; i++) {
                        const zPos = -r * 0.85 + (i / Math.max(1, nPipes - 1)) * r * 1.7;
                        // Sphere base of pipe
                        const s = new THREE.Mesh(new THREE.SphereGeometry(0.09, 10, 8), pipeMat);
                        s.position.set(0, y + TH + frH + 0.07, zPos);
                        s.scale.set(0.7, 1.1, 0.7);
                        g.add(s);
                        // Small peak
                        const tip = new THREE.Mesh(new THREE.ConeGeometry(0.045, 0.1, 8), pipeMat);
                        tip.position.set(0, y + TH + frH + 0.14, zPos);
                        g.add(tip);
                    }
                }

                y += TH + GAP;
            }

            const totalH = y - GAP;
            g.position.y = -totalH / 2;
            return g;
        };

        // ── Read Form ─────────────────────────────────────────────────────
        const getP = () => {
            const fKey = document.getElementById('builder-frosting')?.value || 'ivory';
            const cHex = document.getElementById('builder-frosting-custom')?.value || '#e9e2cf';
            const frostHex = fKey === 'custom' ? cHex : (FROST[fKey] || '#e9e2cf');
            const dMode = document.getElementById('builder-drip')?.value || 'none';
            const sponge  = document.querySelector('select[name="customization[sponge]"]')?.value || 'Vanilla';
            const filling = document.querySelector('select[name="customization[filling]"]')?.value || 'Vanilla Cream';
            return {
                tiers: Math.max(1, Math.min(4, parseInt(document.getElementById('builder-layers')?.value || '1'))),
                shape: document.getElementById('builder-shape')?.value || 'Round',
                size: parseInt(document.getElementById('builder-size')?.value || '6'),
                frostHex, dMode, dripHex: DRIP[dMode] || null,
                sponge, filling,
                spongeHex: SPONGE[sponge] || '#f5d7a5',
                fillHex: FILL[filling] || '#f0e8d4',
                fillStyle: fillingStyle[filling] || fillingStyle['Vanilla Cream'],
                topper: document.getElementById('builder-topper')?.value || 'none',
            };
        };

        // ── Build Cake ────────────────────────────────────────────────────
        const build = () => {
            while (cakeGroup.children.length) cakeGroup.remove(cakeGroup.children[0]);
            while (cutGroup.children.length)  cutGroup.remove(cutGroup.children[0]);

            const p = getP();
            const isInside = currentView === 'inside';
            const TH = 0.62, GAP = 0.04;
            const ss = { 6: 1.0, 8: 1.1, 10: 1.2, 12: 1.3 };
            const baseR = 1.1 * (ss[p.size] || 1.0);

            let yPos = 0;
            const radii = [];
            for (let i = 0; i < p.tiers; i++) {
                const r = Math.max(0.55, baseR - i * 0.22);
                radii.push(r);
                let tier;
                if (p.shape === 'Square')     tier = buildSquare(r * 0.88, TH, p.frostHex, p.dripHex, isInside);
                else if (p.shape === 'Heart') tier = buildHeart(r * 0.75, TH, p.frostHex, isInside);
                else                          tier = buildRound(r, TH, p.frostHex, p.dripHex, isInside);
                tier.position.y = yPos + TH / 2;
                tier.castShadow = true; tier.receiveShadow = true;
                cakeGroup.add(tier);

                // Filling ring between tiers (visible band at tier boundary)
                if (p.shape === 'Round' && i < p.tiers - 1) {
                    const fr = new THREE.Mesh(
                        new THREE.CylinderGeometry(r + 0.006, r + 0.006, 0.017, 72),
                        mkMat(p.fillHex, { r: 0.72, clip: isInside ? [HALF_PLANE] : [] })
                    );
                    fr.position.y = yPos + TH - 0.009;
                    cakeGroup.add(fr);
                }
                yPos += TH + GAP;
            }

            const totalH = yPos - GAP;
            cakeGroup.position.y = -totalH / 2;

            // Topper
            if (p.topper !== 'none') {
                const stk = new THREE.Mesh(new THREE.CylinderGeometry(0.013, 0.013, 0.3, 8),
                    mkMat('#c9a84c', { r: 0.28, m: 0.82 }));
                stk.position.y = cakeGroup.position.y + totalH + 0.15;
                const bnr = new THREE.Mesh(new THREE.BoxGeometry(0.54, 0.1, 0.018), mkMat('#fff8fa', { r: 0.8 }));
                bnr.position.y = 0.18; stk.add(bnr);
                cakeGroup.add(stk);
            }

            // Inside view cut-face
            if (isInside) {
                const cf = buildCutFace(p, p.tiers);
                cf.position.y = cakeGroup.position.y;
                cutGroup.add(cf);
                cutGroup.visible = true;
            } else {
                cutGroup.visible = false;
            }

               const syncViewButtons = (v) => {
            ['front', 'top', 'side', 'inside'].forEach(name => {
                const btn = document.getElementById('tab-view-' + name);
                if (!btn) return;
                const active = name === v;

                if (name === 'inside') {
                    btn.className = 'rounded-full px-8 py-3 text-sm font-black transition-all shadow-inner border ' +
                        (active ? 'border-[#ec5a61] bg-pink-600 text-white' : 'border-pink-300 bg-pink-50 text-pink-600 hover:bg-pink-100');
                    return;
                }

                btn.className = 'rounded-full px-8 py-3 text-sm font-bold transition-all shadow-sm border ' +
                    (active ? 'border-[#ec5a61] bg-pink-600 text-white hover:bg-pink-500' : 'border-transparent bg-white/50 text-[#7A5252] hover:bg-pink-100 hover:text-pink-700');
            });
        };   let currentView = 'front', autoRot = true, rotY = 0;
        const CAM = {
            front:  { pos: [0, 1.2, 6.4],  look: [0, 0.1, 0], fov: 38 },
            top:    { pos: [0, 7.8, 0.01], look: [0, 0,   0], fov: 36 },
            side:   { pos: [6.4, 1.2, 0],  look: [0, 0.1, 0], fov: 38 },
            inside: { pos: [5.2, 0.5, 0.0], look: [0, 0, 0], fov: 38 },
        };
        let camFrom = null, camTo = null, camT = 0;
        const CAM_DUR = 42;

        const setView = v => {
            currentView = v;
            autoRot = v === 'front';
            if (v === 'side') rotY = Math.PI / 2;
            else if (v === 'top') rotY = 0;
            else rotY = 0;
            if (v !== 'front') { cakeGroup.rotation.y = 0; cutGroup.rotation.y = 0; rotY = 0; }

            // Update tab styling
            ['front', 'top', 'side', 'inside'].forEach(name => {
                const btn = document.getElementById('tab-view-' + name);
                if (!btn) return;
                const active = name === v;
                btn.className = 'rounded-xl px-2 py-2 text-xs font-semibold text-center transition-all border ' +
                    (active ? 'border-[#ec5a61] bg-pink-600 text-white' : 'border-[#F3D7DB] bg-[#FFF7F7] text-[#7A5252]');
            });

            const lbl = document.getElementById('cam-view-label');
            if (lbl) lbl.textContent = v === 'front' ? '3D View' : v === 'top' ? 'Top View' : 'Inside View';

            const hint = document.getElementById('inside-click-hint');
            if (hint) hint.style.display = (v === 'inside') ? 'flex' : 'none';

            const spinHint = document.getElementById('spin-hint');
            if (spinHint) spinHint.style.opacity = (v === 'front') ? '1' : '0';

            build();

            const cv = CAM[v];
            if (cv) {
                camFrom = { pos: camera.position.clone(), fov: camera.fov };
                camTo = { pos: new THREE.Vector3(...cv.pos), look: new THREE.Vector3(...cv.look), fov: cv.fov };
                camT = 0;
            }
        };

        // ── Slice Modal ───────────────────────────────────────────────────
        let sliceR, sliceSc, sliceCam, sliceRAF;

        const openSlice = () => {
            const modal = document.getElementById('slice-modal');
            if (!modal) return;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            const sc = document.getElementById('slice-3d-canvas');
            if (!sc) return;
            const mw = sc.closest('.relative') || sc.parentElement;
            // Force layout so dimensions are correct
            const SW = Math.max(320, (mw ? mw.clientWidth : 0) || sc.clientWidth || 700);
            const SH = Math.max(240, (mw ? mw.clientHeight : 0) || sc.clientHeight || 500);

            if (!sliceSc) {
                sliceSc = new THREE.Scene();
                sliceSc.background = new THREE.Color(0x1f0d13);
                sliceSc.fog = new THREE.Fog(0x1f0d13, 4.5, 8.4);
                sliceCam = new THREE.PerspectiveCamera(30, SW / SH, 0.1, 100);
                sliceCam.position.set(0.28, 0.42, 4.35);
                sliceCam.lookAt(0.05, 0.02, 0);
                sliceR = new THREE.WebGLRenderer({ canvas: sc, antialias: true, powerPreference: 'high-performance' });
                sliceR.setSize(SW, SH, false);
                sliceR.setPixelRatio(Math.min(window.devicePixelRatio, 2));
                sliceR.toneMapping = THREE.ACESFilmicToneMapping;
                sliceR.toneMappingExposure = 1.38;
                sliceR.outputEncoding = THREE.sRGBEncoding;
                sliceR.shadowMap.enabled = true;
                sliceR.shadowMap.type = THREE.PCFSoftShadowMap;

                sliceSc.add(new THREE.HemisphereLight(0xffefe5, 0x2a0f17, 1.1));
                sliceSc.add(new THREE.AmbientLight(0xffe7dc, 0.42));
                const sl1 = new THREE.DirectionalLight(0xfff3de, 2.65);
                sl1.position.set(-2.6, 4.8, 4.2);
                sl1.castShadow = true;
                sl1.shadow.mapSize.set(2048, 2048);
                sl1.shadow.bias = -0.0008;
                sl1.shadow.camera.left = -3;
                sl1.shadow.camera.right = 3;
                sl1.shadow.camera.top = 3;
                sl1.shadow.camera.bottom = -3;
                sliceSc.add(sl1);
                const sl2 = new THREE.DirectionalLight(0xffb8c8, 1.0);
                sl2.position.set(3.8, 1.6, 2.6);
                sliceSc.add(sl2);
                const sl3 = new THREE.DirectionalLight(0xffffff, 1.15);
                sl3.position.set(0.8, 2.4, -3.8);
                sliceSc.add(sl3);
                const productGlow = new THREE.PointLight(0xffe0b8, 0.9, 5);
                productGlow.position.set(-1.6, 1.1, 2.4);
                sliceSc.add(productGlow);

                const bgMat = new THREE.MeshBasicMaterial({
                    color: 0x3a242a,
                    transparent: true,
                    opacity: 0.92,
                    side: THREE.DoubleSide,
                });
                const bg = new THREE.Mesh(new THREE.PlaneGeometry(7, 4.8), bgMat);
                bg.position.set(0, 0.28, -1.25);
                bg.userData.isStudioBackdrop = true;
                sliceSc.add(bg);

                const floorMat = new THREE.ShadowMaterial({ opacity: 0.22 });
                const floor = new THREE.Mesh(new THREE.PlaneGeometry(6.5, 4.2), floorMat);
                floor.rotation.x = -Math.PI / 2;
                floor.position.set(0, -1.02, 0.18);
                floor.receiveShadow = true;
                floor.userData.isStudioFloor = true;
                sliceSc.add(floor);
            } else {
                // Clear previous cake
                sliceSc.children.filter(c => c.isGroup).forEach(c => sliceSc.remove(c));
                sliceR.setSize(SW, SH, false);
            }
            sliceCam.aspect = SW / SH;
            sliceCam.updateProjectionMatrix();

            const p = getP();
            let sliceGroup;
            try {
                sliceGroup = buildSliceScene(p);
            } catch (e) {
                sliceGroup = new THREE.Group();
                const fallback = new THREE.Mesh(
                    new THREE.CylinderGeometry(1.1, 1.1, 1.2, 24, 1, false, -Math.PI * 0.25, Math.PI * 0.5),
                    mkMat(p.frostHex, { r: 0.45, side: THREE.DoubleSide })
                );
                fallback.castShadow = true;
                sliceGroup.add(fallback);
                console.error('Slice scene fallback due to render error:', e);
            }
            sliceSc.add(sliceGroup);

            const si = document.getElementById('slice-info');
            const sfi = document.getElementById('slice-filling-info');
            if (si) si.textContent = p.sponge + ' sponge · ' + p.tiers + (p.tiers === 1 ? ' tier' : ' tiers');
            if (sfi) sfi.textContent = 'Filled with ' + p.filling + (p.dMode !== 'none' ? ' · ' + p.dMode.replace('_', ' ') + ' drip' : '');

            if (sliceRAF) cancelAnimationFrame(sliceRAF);
            // Keep the slice still by default, then let shoppers rotate it by dragging.
            sliceGroup.rotation.y = -Math.PI * 0.08;
            if (!sc.dataset.dragRotateReady) {
                sc.dataset.dragRotateReady = 'true';
                sc.style.cursor = 'grab';

                let dragging = false;
                let lastX = 0;
                let lastY = 0;
                let pinchDistance = 0;
                let sliceZoom = 4.35;
                const minZoom = 2.7;
                const maxZoom = 6.2;
                const setSliceZoom = (nextZoom) => {
                    sliceZoom = Math.max(minZoom, Math.min(maxZoom, nextZoom));
                    if (!sliceCam) return;
                    sliceCam.position.z = sliceZoom;
                    sliceCam.lookAt(0.05, 0.02, 0);
                };
                const getPinchDistance = (touches) => {
                    if (!touches || touches.length < 2) return 0;
                    const dx = touches[0].clientX - touches[1].clientX;
                    const dy = touches[0].clientY - touches[1].clientY;
                    return Math.hypot(dx, dy);
                };

                const activeSlice = () => sliceSc?.children?.find(c => c.userData?.isCakeSlice);

                sc.addEventListener('wheel', (event) => {
                    event.preventDefault();
                    setSliceZoom(sliceZoom + event.deltaY * 0.0028);
                }, { passive: false });

                sc.addEventListener('pointerdown', (event) => {
                    dragging = true;
                    lastX = event.clientX;
                    lastY = event.clientY;
                    sc.style.cursor = 'grabbing';
                    sc.setPointerCapture?.(event.pointerId);
                });

                sc.addEventListener('pointermove', (event) => {
                    if (!dragging) return;
                    const group = activeSlice();
                    if (!group) return;

                    const dx = event.clientX - lastX;
                    const dy = event.clientY - lastY;
                    group.rotation.y += dx * 0.008;
                    group.rotation.x = Math.max(-0.45, Math.min(0.35, group.rotation.x + dy * 0.005));
                    lastX = event.clientX;
                    lastY = event.clientY;
                });

                const stopDrag = (event) => {
                    dragging = false;
                    sc.style.cursor = 'grab';
                    sc.releasePointerCapture?.(event.pointerId);
                };
                sc.addEventListener('pointerup', stopDrag);
                sc.addEventListener('pointercancel', stopDrag);
                sc.addEventListener('pointerleave', () => {
                    dragging = false;
                    sc.style.cursor = 'grab';
                });

                sc.addEventListener('touchstart', (event) => {
                    pinchDistance = getPinchDistance(event.touches);
                }, { passive: true });

                sc.addEventListener('touchmove', (event) => {
                    if (event.touches.length < 2) return;
                    event.preventDefault();
                    const nextDistance = getPinchDistance(event.touches);
                    if (pinchDistance > 0 && nextDistance > 0) {
                        setSliceZoom(sliceZoom - (nextDistance - pinchDistance) * 0.01);
                    }
                    pinchDistance = nextDistance;
                }, { passive: false });

                sc.addEventListener('touchend', () => {
                    pinchDistance = 0;
                }, { passive: true });
            }
            sliceGroup.userData.isCakeSlice = true;
            const sLoop = () => {
                sliceRAF = requestAnimationFrame(sLoop);
                sliceR.render(sliceSc, sliceCam);
            };
            sLoop();
        };

        const buildSliceScene = p => {
            const g = new THREE.Group();
            const fillFx = p.fillStyle || (fillingStyle[p.filling] || fillingStyle['Vanilla Cream']);

            const makeMat = (hex, opts = {}) => new THREE.MeshPhysicalMaterial({
                color: tc(hex),
                map: opts.map ?? null,
                bumpMap: opts.bumpMap ?? null,
                bumpScale: opts.bumpScale ?? 0,
                roughnessMap: opts.roughnessMap ?? null,
                roughness: opts.r ?? 0.58,
                metalness: opts.m ?? 0.02,
                transparent: !!opts.t,
                opacity: opts.o ?? 1,
                side: opts.side ?? THREE.DoubleSide,
                clearcoat: opts.clearcoat ?? 0,
                clearcoatRoughness: opts.clearcoatRoughness ?? 0.08,
                reflectivity: opts.reflectivity ?? 0.28,
                transmission: opts.transmission ?? 0,
                thickness: opts.thickness ?? 0,
                ior: opts.ior ?? 1.45,
                envMapIntensity: opts.env ?? 0.55,
                emissive: opts.emissive ? tc(opts.emissive) : new THREE.Color(0x000000),
                emissiveIntensity: opts.emissiveIntensity ?? 0,
            });

            const textureCanvas = (size, draw, repeatX = 1, repeatY = 1) => {
                const c = document.createElement('canvas');
                c.width = size;
                c.height = size;
                const ctx = c.getContext('2d');
                draw(ctx, size);
                const tex = new THREE.CanvasTexture(c);
                tex.wrapS = THREE.RepeatWrapping;
                tex.wrapT = THREE.RepeatWrapping;
                tex.repeat.set(repeatX, repeatY);
                tex.encoding = THREE.sRGBEncoding;
                tex.needsUpdate = true;
                return tex;
            };

            const spongeTex = textureCanvas(512, (ctx, s) => {
                ctx.fillStyle = p.spongeHex;
                ctx.fillRect(0, 0, s, s);
                for (let i = 0; i < 950; i++) {
                    const x = Math.random() * s;
                    const y = Math.random() * s;
                    const r = 1 + Math.random() * 5;
                    ctx.fillStyle = i % 4 === 0 ? 'rgba(95,55,18,0.22)' : 'rgba(255,245,199,0.28)';
                    ctx.beginPath();
                    ctx.ellipse(x, y, r * 1.45, r, Math.random() * Math.PI, 0, Math.PI * 2);
                    ctx.fill();
                }
                for (let i = 0; i < 190; i++) {
                    ctx.fillStyle = 'rgba(112,68,22,0.22)';
                    ctx.fillRect(Math.random() * s, Math.random() * s, 1 + Math.random() * 5, 1 + Math.random() * 2);
                }
            }, 2.2, 1.2);

            const creamTex = textureCanvas(512, (ctx, s) => {
                const grad = ctx.createLinearGradient(0, 0, s, s);
                grad.addColorStop(0, '#fff9ea');
                grad.addColorStop(0.55, p.frostHex);
                grad.addColorStop(1, '#d8c5a7');
                ctx.fillStyle = grad;
                ctx.fillRect(0, 0, s, s);
                for (let i = 0; i < 95; i++) {
                    ctx.strokeStyle = i % 2 ? 'rgba(255,255,255,0.28)' : 'rgba(170,132,94,0.16)';
                    ctx.lineWidth = 1 + Math.random() * 5;
                    ctx.beginPath();
                    const y = Math.random() * s;
                    ctx.moveTo(-20, y);
                    ctx.bezierCurveTo(s * 0.25, y + Math.sin(i) * 22, s * 0.65, y - Math.cos(i) * 18, s + 20, y + Math.sin(i * 1.7) * 18);
                    ctx.stroke();
                }
            }, 1.7, 1.1);

            const mousseTex = textureCanvas(512, (ctx, s) => {
                const base = p.filling === 'Chocolate Mousse' ? '#4a281e' : p.fillHex;
                const hi = p.filling === 'Chocolate Mousse' ? '#9a5b43' : fillFx.accent;
                ctx.fillStyle = base;
                ctx.fillRect(0, 0, s, s);
                for (let i = 0; i < 260; i++) {
                    const x = Math.random() * s;
                    const y = Math.random() * s;
                    const r = 4 + Math.random() * 22;
                    const g2 = ctx.createRadialGradient(x, y, 0, x, y, r);
                    g2.addColorStop(0, i % 3 === 0 ? 'rgba(255,235,210,0.42)' : 'rgba(255,255,255,0.18)');
                    g2.addColorStop(0.45, hi + '88');
                    g2.addColorStop(1, 'rgba(38,12,8,0)');
                    ctx.fillStyle = g2;
                    ctx.beginPath();
                    ctx.ellipse(x, y, r * 1.5, r * 0.75, Math.random() * Math.PI, 0, Math.PI * 2);
                    ctx.fill();
                }
            }, 1.7, 0.8);

            const width = 2.55;
            const depth = 0.88;
            const faceZ = depth / 2 + 0.018;
            const spongeH = 0.34;
            const fillH = 0.20;
            const creamH = 0.075;
            const topFrostH = 0.12;
            const totalH = spongeH * 3 + fillH * 2 + creamH * 4 + topFrostH;
            const left = -width / 2;

            const spongeMat = makeMat(p.spongeHex, { r: 0.94, reflectivity: 0.08, env: 0.2, map: spongeTex, bumpMap: spongeTex, bumpScale: 0.045, emissive: p.spongeHex, emissiveIntensity: 0.018 });
            const spongeDarkMat = makeMat(darken3dHex(p.spongeHex, 0.68), { r: 0.96, reflectivity: 0.04, env: 0.12 });
            const crustMat = makeMat(darken3dHex(p.spongeHex, 0.55), { r: 0.86, reflectivity: 0.05, env: 0.14 });
            const fillMat = makeMat(p.filling === 'Chocolate Mousse' ? '#4a281e' : p.fillHex, { r: Math.min(fillFx.rough, 0.2), m: fillFx.metal, clearcoat: 1, clearcoatRoughness: 0.018, transmission: 0.1, thickness: 0.14, ior: 1.52, reflectivity: 0.68, env: 1.2, map: mousseTex, bumpMap: mousseTex, bumpScale: 0.028 });
            const fillAccentMat = makeMat(p.filling === 'Chocolate Mousse' ? '#8e4d38' : fillFx.accent, { r: 0.2, clearcoat: 0.95, clearcoatRoughness: 0.035, transmission: 0.08, thickness: 0.08, reflectivity: 0.58, env: 1.0, map: mousseTex, bumpMap: mousseTex, bumpScale: 0.018 });
            const frostingMat = makeMat(p.frostHex, { r: 0.52, clearcoat: 0.18, clearcoatRoughness: 0.28, reflectivity: 0.14, env: 0.32, map: creamTex, bumpMap: creamTex, bumpScale: 0.032, emissive: '#fff2dc', emissiveIntensity: 0.012 });
            const shadowMat = new THREE.ShadowMaterial({ opacity: 0.24 });
            const creviceMat = new THREE.MeshBasicMaterial({
                color: 0x2b120b,
                transparent: true,
                opacity: 0.22,
                depthWrite: false,
                side: THREE.DoubleSide,
            });

            const addBlock = (w, h, d, x, y, z, mat) => {
                const mesh = new THREE.Mesh(new THREE.BoxGeometry(w, h, d, 10, 2, 4), mat);
                mesh.position.set(x, y + h / 2, z);
                mesh.castShadow = true;
                mesh.receiveShadow = true;
                g.add(mesh);
                return mesh;
            };

            const addFaceBand = (h, y, mat, inset = 0.02) => {
                const shape = new THREE.Shape();
                const waves = 18;
                shape.moveTo(left + inset, y);
                for (let i = 0; i <= waves; i++) {
                    const x = left + inset + (i / waves) * (width - inset * 2);
                    const wobble = Math.sin(i * 1.7 + y * 5.1) * 0.012 + Math.cos(i * 0.9) * 0.006;
                    shape.lineTo(x, y + wobble);
                }
                for (let i = waves; i >= 0; i--) {
                    const x = left + inset + (i / waves) * (width - inset * 2);
                    const wobble = Math.sin(i * 1.5 + y * 4.4) * 0.012 + Math.cos(i * 1.1) * 0.006;
                    shape.lineTo(x, y + h + wobble);
                }
                shape.closePath();
                const mesh = new THREE.Mesh(new THREE.ShapeGeometry(shape), mat);
                mesh.position.z = faceZ;
                g.add(mesh);
                return mesh;
            };

            const addCreamRibbon = (y, h = creamH) => {
                const ribbon = addFaceBand(h, y, frostingMat, 0.015);
                ribbon.position.z = faceZ + 0.006;
                for (let i = 0; i < 18; i++) {
                    const x = left + 0.1 + (i / 17) * (width - 0.2);
                    const puff = new THREE.Mesh(new THREE.SphereGeometry(0.035 + (i % 3) * 0.006, 10, 8), frostingMat);
                    puff.position.set(x, y + h * (0.48 + Math.sin(i * 1.8) * 0.12), faceZ + 0.02);
                    puff.scale.set(1.25, 0.55, 0.45);
                    g.add(puff);
                }
            };

            const addCreviceShadow = (y) => {
                const shadow = new THREE.Mesh(new THREE.PlaneGeometry(width * 0.94, 0.028), creviceMat);
                shadow.position.set(0, y, faceZ + 0.052);
                g.add(shadow);
            };

            const addSpongeTexture = (y, h) => {
                for (let i = 0; i < 105; i++) {
                    const x = left + 0.06 + ((i * 37) % 100) / 100 * (width - 0.12);
                    const yy = y + 0.035 + ((i * 61) % 100) / 100 * (h - 0.07);
                    const pore = new THREE.Mesh(new THREE.SphereGeometry(0.007 + (i % 4) * 0.003, 6, 5), i % 5 === 0 ? crustMat : spongeDarkMat);
                    pore.position.set(x, yy, faceZ + 0.024 + (i % 3) * 0.003);
                    pore.scale.set(1.25, 0.75, 0.45);
                    g.add(pore);
                }
                for (let i = 0; i < 24; i++) {
                    const x = left + 0.04 + (i / 23) * (width - 0.08);
                    const crumb = new THREE.Mesh(new THREE.SphereGeometry(0.018 + (i % 3) * 0.006, 8, 6), spongeMat);
                    crumb.position.set(x, y + h + Math.sin(i * 1.9) * 0.018, faceZ + 0.022);
                    crumb.scale.set(1.15, 0.55, 0.5);
                    g.add(crumb);
                }
            };

            const addFillingLayer = (y) => {
                const xSegs = 90;
                const ySegs = 16;
                const planeGeo = new THREE.PlaneGeometry(width - 0.04, fillH, xSegs, ySegs);
                const pos = planeGeo.attributes.position;

                for(let i = 0; i < pos.count; i++) {
                    const vx = pos.getX(i);
                    const vy = pos.getY(i);
                    // nx is normalized x from 0 to 1 across the width of the face
                    const nx = (vx - (left + 0.02)) / (width - 0.04);

                    // z_factor is 0 at the top of the layer, 1 at the bottom
                    const z_factor = (-vy + fillH/2) / fillH;

                    let edge_fade = 1.0;
                    const dist_from_edge = Math.min(nx, 1.0 - nx);
                    if (dist_from_edge < 0.08) {
                        const f = dist_from_edge / 0.08;
                        edge_fade = f * f * (3 - 2 * f);
                    }

                    // Sine waves for deep, heavy drips based on the python script
                    const drip_pattern = Math.max(0, 0.1 + 0.18 * Math.sin(nx * 22.0) + 0.1 * Math.cos(nx * 48.0));

                    const outward = drip_pattern * z_factor * edge_fade * fillFx.drip * 0.28;
                    const downward = (drip_pattern * 3.2) * Math.pow(z_factor, 1.8) * edge_fade * fillFx.drip * 0.18;

                    pos.setY(i, vy - downward);
                    pos.setZ(i, pos.getZ(i) + outward);
                }
                planeGeo.computeVertexNormals();

                const mesh = new THREE.Mesh(planeGeo, fillMat);
                mesh.position.set(0, y + fillH / 2, faceZ + 0.015);
                mesh.castShadow = true;
                mesh.receiveShadow = true;
                g.add(mesh);

                // Add the chunks/bits inside the filling
                for (let i = 0; i < Math.max(18, fillFx.chunks * 2); i++) {
                    const x = left + 0.1 + ((i * 43) % 100) / 100 * (width - 0.2);
                    const yy = y + fillH * (0.22 + ((i * 29) % 58) / 100);
                    const blob = new THREE.Mesh(new THREE.SphereGeometry(0.04 + (i % 4) * 0.012, 12, 10), i % 3 === 0 ? fillAccentMat : fillMat);
                    blob.position.set(x, yy, faceZ + 0.035);
                    blob.scale.set(1.65, 0.78, 0.55);
                    blob.castShadow = true;
                    g.add(blob);
                }
            };

            addBlock(width, totalH, depth, 0, 0, 0, frostingMat);
            addBlock(0.12, totalH * 0.94, depth + 0.035, width / 2 - 0.06, 0.02, 0.015, frostingMat);
            addBlock(width, topFrostH, depth + 0.04, 0, totalH - topFrostH, 0.02, frostingMat);

            let y = 0.03;
            addFaceBand(spongeH, y, spongeMat); addSpongeTexture(y, spongeH);
            y += spongeH; addCreviceShadow(y); addCreamRibbon(y, creamH); y += creamH;
            addFillingLayer(y); y += fillH; addCreviceShadow(y);
            addCreamRibbon(y, creamH); y += creamH; addCreviceShadow(y);
            addFaceBand(spongeH, y, spongeMat); addSpongeTexture(y, spongeH);
            y += spongeH; addCreviceShadow(y); addCreamRibbon(y, creamH); y += creamH;
            addFillingLayer(y); y += fillH; addCreviceShadow(y);
            addCreamRibbon(y, creamH); y += creamH; addCreviceShadow(y);
            addFaceBand(spongeH, y, spongeMat); addSpongeTexture(y, spongeH);

            for (let i = 0; i < 8; i++) {
                const x = left + 0.24 + (i / 7) * (width - 0.48);
                const base = new THREE.Mesh(new THREE.SphereGeometry(0.085, 14, 10), frostingMat);
                base.position.set(x, totalH + 0.035, 0.05 + Math.sin(i * 1.4) * 0.05);
                base.scale.set(1.2, 0.72, 1);
                g.add(base);
                const peak = new THREE.Mesh(new THREE.ConeGeometry(0.055, 0.11, 12), frostingMat);
                peak.position.set(x + 0.015, totalH + 0.105, 0.05 + Math.sin(i * 1.4) * 0.05);
                peak.rotation.z = Math.sin(i) * 0.25;
                g.add(peak);
            }

            const berryMat = makeMat('#d7192f', { r: 0.32, clearcoat: 0.75, clearcoatRoughness: 0.08 });
            const berry = new THREE.Mesh(new THREE.SphereGeometry(0.16, 18, 12), berryMat);
            berry.position.set(0.42, totalH + 0.16, 0.08);
            berry.scale.set(1.25, 0.42, 0.78);
            berry.rotation.z = -0.45;
            g.add(berry);
            const berryCut = new THREE.Mesh(new THREE.CircleGeometry(0.13, 24), makeMat('#ffd5c9', { r: 0.42 }));
            berryCut.position.set(0.42, totalH + 0.165, 0.205);
            berryCut.rotation.z = -0.45;
            g.add(berryCut);

            const plate = new THREE.Mesh(new THREE.CylinderGeometry(1.65, 1.72, 0.07, 72), makeMat('#e8dfd5', { r: 0.54 }));
            plate.position.y = -0.05;
            plate.receiveShadow = true;
            g.add(plate);
            const shadow = new THREE.Mesh(new THREE.CircleGeometry(1.6, 48), shadowMat);
            shadow.rotation.x = -Math.PI / 2;
            shadow.position.y = -0.015;
            g.add(shadow);

            g.position.y = -totalH / 2;
            g.rotation.x = -0.02;
            g.scale.set(1.22, 1.22, 1.22);
            return g;
        };

        const closeSlice = () => {
            document.getElementById('slice-modal')?.classList.add('hidden');
            document.body.style.overflow = '';
            if (sliceRAF) { cancelAnimationFrame(sliceRAF); sliceRAF = null; }
        };

        // ── Event Wiring ──────────────────────────────────────────────────
        ['front', 'top', 'side', 'inside'].forEach(v => {
            document.getElementById('tab-view-' + v)?.addEventListener('click', () => setView(v));
        });
        document.getElementById('inside-click-hint')?.addEventListener('click', openSlice);
        document.getElementById('slice-modal-close')?.addEventListener('click', closeSlice);
        document.getElementById('slice-modal-backdrop')?.addEventListener('click', closeSlice);
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSlice(); });

        // Expose to compute()
        window.__cake3dUpdate = build;

        // ── Animation Loop ────────────────────────────────────────────────
        const animate = () => {
            requestAnimationFrame(animate);
            if (camFrom && camTo && camT < 1) {
                camT = Math.min(1, camT + 1 / CAM_DUR);
                const e = camT < 0.5 ? 2 * camT * camT : -1 + (4 - 2 * camT) * camT;
                camera.position.lerpVectors(camFrom.pos, camTo.pos, e);
                camera.fov = camFrom.fov + (camTo.fov - camFrom.fov) * e;
                camera.updateProjectionMatrix();
                camera.lookAt(camTo.look);
                if (camT >= 1) { camFrom = camTo = null; }
            }
            if (autoRot) {
                rotY += 0.0055;
                cakeGroup.rotation.y = rotY;
                cutGroup.rotation.y = rotY;
            }
            renderer.render(scene, camera);
        };
        animate();

        // ── Resize ────────────────────────────────────────────────────────
        new ResizeObserver(() => {
            const w = wrap.clientWidth, h = wrap.clientHeight;
            if (!w || !h) return;
            camera.aspect = w / h; camera.updateProjectionMatrix();
            renderer.setSize(w, h);
        }).observe(wrap);

        // ── Init ──────────────────────────────────────────────────────────
        build();
        setView('front');
    }

    // Blender model preview is initialized by resources/js/customize-3d.js.
    // The older inline preview is kept dormant while we use the shared 3D module.
    // initCake3DPreview();
    </script>
@endsection
