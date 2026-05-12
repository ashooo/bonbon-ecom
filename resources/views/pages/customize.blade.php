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
    </style>
    <section class="mb-8 rounded-[2rem] border border-[#F3D7DD] bg-gradient-to-br from-white via-[#FFF8F9] to-[#FDF0F3] p-8 shadow-[0_20px_45px_rgba(90,58,58,0.12)] md:p-10">
        <p class="mb-3 text-sm font-semibold uppercase tracking-[0.3em] text-pink-600">Cake Builder</p>
        <h1 class="text-4xl font-bold text-[#5A3A3A] md:text-5xl">Build Your Dream Cake</h1>
        <p class="mt-3 max-w-3xl text-[#7A5252]">Choose your base, decorate it, and see a live preview and estimate before adding to cart.</p>
    </section>

        <div class="grid grid-cols-1 gap-8 xl:grid-cols-[1.15fr_0.85fr]">
            <form method="POST" action="{{ route('cart.add') }}" class="space-y-6" id="cake-builder-form">
                @csrf
                <input type="hidden" id="builder-toppings-hidden" name="customization[toppings]" value="[]">
                <input type="hidden" id="builder-preview-svg-hidden" name="customization[preview_svg]" value="">

                <section class="rounded-3xl border border-[#F3D7DD] bg-white p-5 shadow-lg">
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-[#5A3A3A]">Build Steps</h2>
                        <span id="builder-step-label" class="text-sm font-semibold text-pink-600">Step 1 of 2</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs font-semibold md:text-sm">
                        <div id="step-pill-1" class="rounded-xl bg-pink-600 px-3 py-2 text-center text-white">Structure</div>
                        <div id="step-pill-2" class="rounded-xl bg-[#F7E7EB] px-3 py-2 text-center text-[#7A5252]">Toppings & Text</div>
                    </div>
                </section>

                <section data-step="1" class="rounded-3xl border border-[#F3D7DD] bg-white p-6 shadow-lg">
                    <h2 class="mb-4 text-2xl font-bold text-[#5A3A3A]">Foundation</h2>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Shape</label>
                            <select id="builder-shape" name="customization[shape]" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3">
                                <option value="Round">Round</option><option value="Square">Square</option><option value="Heart">Heart</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Base Size</label>
                            <select id="builder-size" name="customization[size]" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3">
                                <option value="6">6 inches</option><option value="8">8 inches</option><option value="10">10 inches</option><option value="12">12 inches</option>
                            </select>
                        </div>
                    </div>
                </section>

                <section data-step="1" class="rounded-3xl border border-[#F3D7DD] bg-white p-6 shadow-lg">
                    <h2 class="mb-4 text-2xl font-bold text-[#5A3A3A]">Design</h2>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Cake Flavor</label>
                            <select name="customization[sponge]" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3">
                                <option value="Vanilla">Vanilla</option>
                                <option value="Chocolate">Chocolate</option>
                                <option value="Red Velvet">Red Velvet</option>
                                <option value="Lemon">Lemon</option>
                                <option value="Strawberry">Strawberry</option>
                                <option value="Funfetti">Funfetti</option>
                            </select>
                            <p class="mt-1 text-xs text-[#8f6a73]">This is the cake base flavor inside each tier.</p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Filling</label>
                            <select name="customization[filling]" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3">
                                <option value="Chocolate Mousse">Chocolate mousse</option>
                                <option value="Strawberry Jam">Strawberry jam</option>
                                <option value="Vanilla Cream">Vanilla cream</option>
                                <option value="Nutella">Nutella</option>
                                <option value="Cookies & Cream">Cookies & cream</option>
                                <option value="Buttercream">Buttercream</option>
                            </select>
                            <p class="mt-1 text-xs text-[#8f6a73]">Flavor layer between sponge tiers.</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-semibold uppercase tracking-[0.08em] text-[#7a7474]">Frosting Color (+PHP 10)</label>
                            <input id="builder-frosting" type="hidden" name="customization[frosting]" value="ivory">
                            <input id="builder-frosting-custom-hidden" type="hidden" name="customization[frosting_custom]" value="">
                            <div class="flex flex-wrap items-center gap-3" id="builder-frosting-swatches">
                                <button type="button" data-frosting="white" class="frosting-swatch h-11 w-11 rounded-full border border-[#dddddd] bg-[#f2f2f2]" aria-label="White frosting"></button>
                                <button type="button" data-frosting="ivory" class="frosting-swatch h-11 w-11 rounded-full border border-[#dddddd] bg-[#e9e2cf]" aria-label="Ivory frosting"></button>
                                <button type="button" data-frosting="blush" class="frosting-swatch h-11 w-11 rounded-full border border-[#dddddd] bg-[#edd3d6]" aria-label="Blush frosting"></button>
                                <button type="button" data-frosting="sage" class="frosting-swatch h-11 w-11 rounded-full border border-[#dddddd] bg-[#d2e1d8]" aria-label="Sage frosting"></button>
                                <button type="button" data-frosting="powder_blue" class="frosting-swatch h-11 w-11 rounded-full border border-[#dddddd] bg-[#d6e1ea]" aria-label="Powder blue frosting"></button>
                                <button type="button" data-frosting="chocolate" class="frosting-swatch h-11 w-11 rounded-full border border-[#dddddd] bg-[#4a2f1f]" aria-label="Chocolate frosting"></button>
                                <button type="button" data-frosting="mocha" class="frosting-swatch h-11 w-11 rounded-full border-2 border-[#ec5a61] bg-[#6a4638] text-white" aria-label="Mocha frosting">✓</button>
                                <button type="button" data-frosting="lavender" class="frosting-swatch h-11 w-11 rounded-full border border-[#dddddd] bg-[#d7b2ef]" aria-label="Lavender frosting"></button>
                                <button id="builder-frosting-custom-btn" type="button" data-frosting="custom" class="custom-frosting-swatch frosting-swatch h-11 w-11 rounded-full border border-[#dddddd] text-xl font-bold leading-none text-[#7A5252]" aria-label="Custom frosting color">+</button>
                            </div>
                            <input id="builder-frosting-custom" type="color" value="#6a4638" class="sr-only" tabindex="-1" aria-hidden="true">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Drip</label>
                            <select id="builder-drip" name="customization[drip]" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3">
                                <option value="none">No drip</option>
                                <option value="chocolate">Chocolate drip</option>
                                <option value="white_chocolate">White chocolate drip</option>
                                <option value="pink">Pink drip</option>
                                <option value="caramel">Caramel drip</option>
                            </select>
                            <p class="mt-1 text-xs text-[#8f6a73]">Turn drip overlay on or off.</p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Tiers</label>
                            <select id="builder-layers" name="customization[layers]" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3">
                                <option value="1">1 tier</option><option value="2">2 tiers</option><option value="3">3 tiers</option><option value="4">4 tiers</option>
                            </select>
                            <p class="mt-1 text-xs text-[#8f6a73]">Adds vertical cake levels for larger designs.</p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Topper</label>
                            <select id="builder-topper" name="customization[topper]" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3">
                                <option value="none">No topper</option><option value="name">Name topper</option><option value="acrylic">Acrylic topper</option><option value="edible_print">Edible print topper</option>
                            </select>
                            <p class="mt-1 text-xs text-[#8f6a73]">Decorative sign placed at the top of the cake.</p>
                        </div>
                    </div>
                </section>

                <section data-step="2" class="rounded-3xl border border-[#F3D7DD] bg-white p-6 shadow-lg hidden">
                    <h2 class="mb-4 text-2xl font-bold text-[#5A3A3A]">Toppings & Text</h2>
                    <p class="mb-4 text-sm text-[#7A5252]">Add flat toppings and personalize text placement, then check it in the Live Preview tabs.</p>

                    <div class="space-y-4">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Message on cake</label>
                                <textarea id="builder-message" name="customization[message]" maxlength="50" rows="3" placeholder="Message on cake" class="w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3"></textarea>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Text color</label>
                                <input id="builder-text-color" type="color" value="#7a3444" class="color-pill h-11 w-full cursor-pointer rounded-2xl border border-[#F3D7DB] bg-white p-0">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Topping shapes</label>
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
                                <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Topping color</label>
                                <input id="builder-topping-color" type="color" value="#ff7eac" class="color-pill h-11 w-full cursor-pointer rounded-2xl border border-[#F3D7DB] bg-white p-0">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-[#5A3A3A]">Quick toppings</label>
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

                <section data-step="2" class="rounded-3xl border border-[#F3D7DD] bg-white p-6 shadow-lg hidden">
                    <h2 class="mb-4 text-2xl font-bold text-[#5A3A3A]">Finish</h2>
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="flex items-center gap-3 rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3">
                            <input id="builder-rush" type="checkbox" value="yes" class="h-4 w-4">
                            <input id="builder-rush-hidden" type="hidden" name="customization[rush]" value="no">
                            <span>Rush order (+&#8369;350)</span>
                        </label>
                        <input name="quantity" type="number" min="1" value="1" class="rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3">
                    </div>
                    <textarea name="special_instructions" rows="3" placeholder="Special instructions" class="mt-4 w-full rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-4 py-3"></textarea>
                </section>

                <div class="flex gap-3">
                    <button id="builder-prev-step" type="button" class="rounded-2xl border border-[#F3D7DB] bg-[#FFF7F7] px-6 py-4 text-base font-bold text-[#7A5252] disabled:cursor-not-allowed disabled:opacity-50">Back</button>
                    <button id="builder-next-step" type="button" class="w-full rounded-2xl bg-pink-500 px-6 py-4 text-lg font-bold text-white hover:bg-pink-600">Next: Toppings</button>
                    <button id="builder-submit" class="hidden w-full rounded-2xl bg-pink-600 px-6 py-4 text-lg font-bold text-white hover:bg-pink-700">Add Custom Cake to Cart</button>
                </div>
            </form>

            <aside class="rounded-3xl border border-[#F3D7DD] bg-white p-6 shadow-lg h-fit xl:sticky xl:top-24">
                <h2 class="mb-4 text-2xl font-bold text-[#5A3A3A]">Live Preview</h2>
                <div class="mb-3 grid grid-cols-2 gap-2">
                    <button id="preview-tab-front" type="button" class="rounded-xl border border-[#ec5a61] bg-[#FDECEF] px-3 py-2 text-sm font-semibold text-[#5A3A3A]">Front View</button>
                    <button id="preview-tab-top" type="button" class="rounded-xl border border-[#F3D7DB] bg-[#FFF7F7] px-3 py-2 text-sm font-semibold text-[#7A5252]">Top View</button>
                </div>
                <div class="relative mb-6 overflow-hidden rounded-3xl border border-[#F3D7DD] bg-gradient-to-b from-[#fff6f8] to-[#ffe7ef] p-4">
                    <div id="preview-panel-front">
                        <div class="absolute right-3 top-3 z-20 w-32 rounded-2xl border border-[#ecc9d1] bg-white/95 p-2 shadow-md">
                            <p class="mb-1 text-[10px] font-bold uppercase tracking-[0.14em] text-[#7A5252]">Inside View</p>
                            <svg id="cake-inside-svg" class="h-20 w-full" viewBox="0 0 120 82" aria-label="Cake inside preview">
                                <rect x="18" y="10" width="84" height="58" rx="10" fill="#f7d6a5" stroke="#c99c6f" stroke-width="1.2"></rect>
                                <g id="cake-inside-layers"></g>
                            </svg>
                            <p id="cake-inside-label" class="mt-1 truncate text-[10px] font-semibold text-[#7A5252]"></p>
                        </div>
                        <svg id="cake-svg" class="mx-auto h-[330px] w-full max-w-sm" viewBox="0 0 400 420" aria-label="Cake preview">
                            <ellipse id="cake-shadow" cx="200" cy="370" rx="145" ry="50" fill="#dcb1bf" opacity="0.5"></ellipse>
                            <g id="cake-layers"></g>
                            <text id="cake-message-preview" x="200" y="96" text-anchor="middle" font-size="14" font-weight="700" fill="#7A3444"></text>
                            <g id="cake-topper" style="display:none;">
                                <rect x="145" y="58" width="110" height="24" rx="12" fill="#ffffff" opacity="0.94"></rect>
                                <text id="cake-topper-text" x="200" y="74" text-anchor="middle" font-size="11" font-weight="700" fill="#7a4252"></text>
                            </g>
                        </svg>
                    </div>
                    <div id="preview-panel-top" class="hidden">
                        <svg id="cake-top-svg" class="mx-auto h-[330px] w-full max-w-sm" viewBox="0 0 320 320" aria-label="Cake top view">
                            <g id="cake-top-base"></g>
                            <g id="cake-top-toppings"></g>
                            <text id="cake-top-message-preview" x="160" y="165" text-anchor="middle" font-size="16" font-weight="700" fill="#7A3444"></text>
                        </svg>
                    </div>
                </div>

                <h2 class="mb-4 text-2xl font-bold text-[#5A3A3A]">Live Estimate</h2>
                <div class="space-y-2 text-sm text-[#6E4D53]">
                    <div class="flex justify-between"><span>Builder subtotal</span><span id="estimate-addon">&#8369;0.00</span></div>
                </div>
                <div class="mt-4 border-t border-[#F3D7DD] pt-4 flex justify-between text-xl font-bold text-[#5A3A3A]">
                    <span>Estimated Total</span><span id="estimate-total">&#8369;0.00</span>
                </div>
                <p class="mt-4 text-sm text-[#7A4F57]">Pricing is estimated and will be finalized at checkout.</p>
            </aside>
        </div>

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

            const pricing = {
                size: { '6': 450, '8': 700, '10': 980, '12': 1280 },
                layers: { '1': 0, '2': 240, '3': 420, '4': 620 },
                frosting: { white: 10, ivory: 10, blush: 10, sage: 10, powder_blue: 10, chocolate: 10, mocha: 10, lavender: 10, custom: 10 },
                drip: { none: 0, chocolate: 70, white_chocolate: 80, pink: 80, caramel: 90 },
                topper: { none: 0, name: 120, acrylic: 200, edible_print: 180 },
                rush: { no: 0, yes: 350 }
            };

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
                const sponge = spongeSelect.value || 'Vanilla';
                const filling = fillingSelect.value || 'Vanilla Cream';
                const spongeColor = spongeTone[sponge] || '#f5d7a5';
                const fillingColor = fillingTone[filling] || '#f6f0dc';
                const fillingLineColor = darkenHex(fillingColor, 0.72);
                const frameY = 10;
                const frameH = 58;
                const bodyX = 22;
                const bodyW = 76;
                const bodyY = frameY + 4;
                const bodyH = frameH - 8;
                let markup = '';
                const thinFillH = 2.4;
                const thin1Y = bodyY + (bodyH * 0.34) - (thinFillH / 2);
                const thin2Y = bodyY + (bodyH * 0.68) - (thinFillH / 2);

                markup += `<rect x="${bodyX}" y="${bodyY.toFixed(2)}" width="${bodyW}" height="${bodyH.toFixed(2)}" rx="2" fill="${spongeColor}"></rect>`;
                markup += `<rect x="${bodyX}" y="${thin1Y.toFixed(2)}" width="${bodyW}" height="${thinFillH.toFixed(2)}" rx="1" fill="${fillingLineColor}"></rect>`;
                markup += `<rect x="${bodyX}" y="${thin2Y.toFixed(2)}" width="${bodyW}" height="${thinFillH.toFixed(2)}" rx="1" fill="${fillingLineColor}"></rect>`;
                cakeInsideLayersEl.innerHTML = markup;
                cakeInsideLabelEl.textContent = `${sponge} + ${filling}`;
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
                // Heart implicit equation, normalized around center for robust hit-test.
                const nx = (x - 160) / 88;
                const ny = (y - 154) / 76;
                const v = Math.pow((nx * nx) + (ny * ny) - 1, 3) - (nx * nx * Math.pow(ny, 3));
                return v <= 0;
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
                        ? { minX: 52, maxX: 268, minY: 72, maxY: 268 }
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

            const renderTopView = () => {
                const [toneTop] = getFrostingTone();
                const topMarkup = shapeTopPath[shapeSelect.value] || shapeTopPath.Round;
                const clipShape = topMarkup;
                const effectiveDripMode = dripSelect.value || 'none';
                const topDripColor = dripTone[effectiveDripMode] || darkenHex(toneTop, 0.65);
                const topSurfaceColor = effectiveDripMode !== 'none' ? topDripColor : toneTop;
                const topDripOverlay = effectiveDripMode !== 'none'
                    ? `<g fill="none" stroke="${topDripColor}" stroke-width="14" stroke-linecap="round" opacity="0.95">${topMarkup}</g>`
                    : '';
                topViewBaseEl.innerHTML = `
                    <defs>
                        <clipPath id="cake-top-clip">
                            ${clipShape}
                        </clipPath>
                    </defs>
                    <g fill="${topSurfaceColor}" stroke="#bf8b99" stroke-width="2">
                        ${topMarkup}
                    </g>
                    ${topDripOverlay}
                `;
                topViewToppingsEl.innerHTML = `<g clip-path="url(#cake-top-clip)">${toppingItems.map((item, idx) => toppingSvg(item.shape, item.x, item.y, item.color, idx)).join('')}</g>`;
                const rawMessage = (messageInput.value || '').slice(0, 50);
                renderMultilineSvgText(topViewMessageEl, rawMessage, 16, 160, 165);
                topViewMessageEl.setAttribute('fill', textColorInput.value || '#7A3444');
                toppingsHiddenInput.value = JSON.stringify(toppingItems);
            };

            const renderMultilineSvgText = (textEl, rawText, maxLineChars, x, y) => {
                const text = (rawText || '').slice(0, 50);
                const lines = [];
                const hardLines = text.split(/\r?\n/);
                hardLines.forEach((line) => {
                    const content = line.trim();
                    if (!content) {
                        lines.push('');
                        return;
                    }
                    let start = 0;
                    while (start < content.length) {
                        lines.push(content.slice(start, start + maxLineChars));
                        start += maxLineChars;
                    }
                });
                const normalizedLines = lines.length ? lines.slice(0, 4) : [''];
                textEl.replaceChildren();
                textEl.setAttribute('x', String(x));
                textEl.setAttribute('y', String(y));
                textEl.setAttribute('text-anchor', 'middle');
                const lineHeight = 18;
                const baselineOffset = ((normalizedLines.length - 1) * lineHeight) / 2;
                normalizedLines.forEach((line, idx) => {
                    const tspan = document.createElementNS('http://www.w3.org/2000/svg', 'tspan');
                    tspan.setAttribute('x', String(x));
                    tspan.setAttribute('dy', idx === 0 ? String(-baselineOffset) : String(lineHeight));
                    tspan.textContent = line;
                    textEl.appendChild(tspan);
                });
            };

            const setPreviewTab = (tab) => {
                const showTop = tab === 'top';
                previewPanelFront.classList.toggle('hidden', showTop);
                previewPanelTop.classList.toggle('hidden', !showTop);
                previewTabFrontBtn.classList.toggle('border-[#ec5a61]', !showTop);
                previewTabFrontBtn.classList.toggle('bg-[#FDECEF]', !showTop);
                previewTabFrontBtn.classList.toggle('text-[#5A3A3A]', !showTop);
                previewTabFrontBtn.classList.toggle('border-[#F3D7DB]', showTop);
                previewTabFrontBtn.classList.toggle('bg-[#FFF7F7]', showTop);
                previewTabFrontBtn.classList.toggle('text-[#7A5252]', showTop);
                previewTabTopBtn.classList.toggle('border-[#ec5a61]', showTop);
                previewTabTopBtn.classList.toggle('bg-[#FDECEF]', showTop);
                previewTabTopBtn.classList.toggle('text-[#5A3A3A]', showTop);
                previewTabTopBtn.classList.toggle('border-[#F3D7DB]', !showTop);
                previewTabTopBtn.classList.toggle('bg-[#FFF7F7]', !showTop);
                previewTabTopBtn.classList.toggle('text-[#7A5252]', !showTop);
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
                if (currentStep === 2) setPreviewTab('top');
                renderTopView();
            };

            const renderCake = () => {
                const layers = Number(layersSelect.value || 1);
                const baseWidthAtSix = 124;
                // Keep size differences subtle so larger sizes don't blow out the preview frame.
                const sizeScaleMap = { '6': 1, '8': 1.08, '10': 1.16, '12': 1.24 };
                const heightMap = { '6': 26, '8': 30, '10': 34, '12': 38 };
                const baseWidth = baseWidthAtSix * Number(sizeScaleMap[sizeSelect.value] || 1);
                const layerHeight = heightMap[sizeSelect.value] || 36;
                const [toneTop, toneBottom] = getFrostingTone();
                const shape = shapeSelect.value;
                const dripMode = dripSelect.value || 'none';
                const tierGap = Number(tierGapByShape[shape] ?? 4);
                const scaledBaseWidth = baseWidth * overallCakeScale;
                const scaledLayerHeight = layerHeight * overallCakeScale;
                const seatedOverlap = Math.max(8, scaledLayerHeight * 0.26);
                const stackStep = Math.max(14, (scaledLayerHeight - seatedOverlap) + tierGap);
                const previewTop = 94;
                const previewBottom = 300;
                const baseTopY = previewBottom - scaledLayerHeight;
                const topMostY = baseTopY - ((layers - 1) * stackStep);
                const pushDown = Math.max(0, previewTop - topMostY);

                cakeLayersEl.innerHTML = '';
                const baseTierYAnchor = baseTopY + pushDown;
                const extraLiftForLevel = (level) => {
                    const ratio = Math.max(0.18, 0.72 - ((level - 1) * 0.18));
                    return Math.max(8, scaledLayerHeight * ratio);
                };
                const tierYFromBase = (tierIndex) => {
                    if (tierIndex === 0) return baseTierYAnchor;
                    let lift = 0;
                    for (let lvl = 1; lvl <= tierIndex; lvl++) {
                        const extra = extraLiftForLevel(lvl);
                        lift += (scaledLayerHeight / 2) + extra;
                    }
                    return baseTierYAnchor - lift;
                };

                let baseTierX = 200 - (scaledBaseWidth / 2);
                let baseTierY = baseTierYAnchor;
                let baseTierWidth = scaledBaseWidth;
                const contactShadowColor = darkenHex(toneBottom, 0.62);
                for (let i = 0; i < layers; i++) {
                    const width = Math.max(86, scaledBaseWidth - i * (14 * overallCakeScale));
                    const x = (200 - (width / 2));
                    const y = tierYFromBase(i);
                    if (i === 0) {
                        baseTierX = x;
                        baseTierY = y;
                        baseTierWidth = width;
                    }
                    if (i > 0) {
                        const contactShadow = document.createElementNS('http://www.w3.org/2000/svg', 'ellipse');
                        contactShadow.setAttribute('cx', String(x + (width / 2)));
                        contactShadow.setAttribute('cy', String(y + Math.max(9, scaledLayerHeight * 0.78)));
                        contactShadow.setAttribute('rx', String(Math.max(18, width * 0.43)));
                        contactShadow.setAttribute('ry', String(Math.max(4, scaledLayerHeight * 0.13)));
                        contactShadow.setAttribute('fill', contactShadowColor);
                        contactShadow.setAttribute('opacity', String(Math.max(0.06, 0.14 - (i * 0.02))));
                        cakeLayersEl.appendChild(contactShadow);
                    }
                    cakeLayersEl.appendChild(drawLayer(shape, x, y, width, scaledLayerHeight, toneTop, toneBottom, i, dripMode));
                }

                const baseCenterX = baseTierX + (baseTierWidth / 2);
                const shadowY = baseTierY + (scaledLayerHeight * 2);
                const shadowRx = Math.max(92, baseTierWidth * 0.78);
                const shadowRy = Math.max(26, shadowRx * 0.4);
                cakeShadowEl.setAttribute('opacity', '0.5');
                cakeShadowEl.setAttribute('cx', String(baseCenterX));
                cakeShadowEl.setAttribute('cy', String(shadowY));
                cakeShadowEl.setAttribute('rx', String(shadowRx));
                cakeShadowEl.setAttribute('ry', String(shadowRy));

                const message = (messageInput.value || '').slice(0, 50);
                messageInput.value = message;
                renderMultilineSvgText(messagePreviewEl, message, 18, 200, 96);
                messagePreviewEl.setAttribute('fill', textColorInput.value || '#7A3444');

                const topperMap = { none: '', name: 'Name Topper', acrylic: 'Acrylic Topper', edible_print: 'Edible Print' };
                const topperLabel = topperMap[topperSelect.value] || '';
                topperWrapEl.style.display = topperLabel === '' ? 'none' : 'block';
                topperTextEl.textContent = topperLabel;
                renderTopView();
                renderInsidePreview();
            };


            const compute = () => {
                const subtotal =
                    Number(pricing.size[sizeSelect.value] || 0) +
                    Number(pricing.layers[layersSelect.value] || 0) +
                    Number(pricing.frosting[frostingSelect.value] || 0) +
                    Number(pricing.drip[dripSelect.value] || 0) +
                    Number(pricing.topper[topperSelect.value] || 0) +
                    Number(pricing.rush[rushCheckbox.checked ? 'yes' : 'no'] || 0);

                rushHidden.value = rushCheckbox.checked ? 'yes' : 'no';
                frostingCustomHidden.value = frostingSelect.value === 'custom' ? (frostingCustomInput.value || '') : '';
                addonEl.textContent = php(subtotal);
                totalEl.textContent = php(subtotal);
                renderCake();
                syncPreviewSvgSnapshot();
            };

            const syncPreviewSvgSnapshot = () => {
                try {
                    if (!cakeSvgEl) return;
                    const clone = cakeSvgEl.cloneNode(true);
                    clone.removeAttribute('id');
                    clone.removeAttribute('class');
                    clone.setAttribute('width', '160');
                    clone.setAttribute('height', '120');

                    // Keep gradient/clip ids working in saved SVG by remapping them to unique names.
                    const uid = `snap${Date.now().toString(36)}${Math.random().toString(36).slice(2, 7)}`;
                    const idMap = new Map();
                    clone.querySelectorAll('[id]').forEach((node, idx) => {
                        const oldId = node.getAttribute('id');
                        if (!oldId) return;
                        const nextId = `${uid}-${idx}`;
                        idMap.set(oldId, nextId);
                        node.setAttribute('id', nextId);
                    });
                    const refAttrs = ['fill', 'stroke', 'filter', 'clip-path', 'mask', 'href', 'xlink:href'];
                    clone.querySelectorAll('*').forEach((node) => {
                        refAttrs.forEach((attr) => {
                            const value = node.getAttribute(attr);
                            if (!value) return;
                            let updated = value;
                            idMap.forEach((nextId, oldId) => {
                                updated = updated.replace(new RegExp(`url\\(#${oldId}\\)`, 'g'), `url(#${nextId})`);
                                if (updated === `#${oldId}`) updated = `#${nextId}`;
                            });
                            if (updated !== value) node.setAttribute(attr, updated);
                        });
                    });
                    previewSvgHiddenInput.value = clone.outerHTML;
                } catch (error) {
                    previewSvgHiddenInput.value = '';
                }
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
                renderTopView();
            };

            const removeSingleShapeTopping = (shapeType) => {
                for (let i = toppingItems.length - 1; i >= 0; i--) {
                    if (toppingItems[i].shape === shapeType && !toppingItems[i].preset) {
                        toppingItems.splice(i, 1);
                        break;
                    }
                }
                renderTopView();
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
                    renderTopView();
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
                renderTopView();
            };

            clearToppingsBtn.addEventListener('click', () => {
                toppingItems.length = 0;
                activeToppingPresets.clear();
                quickToppingBtns.forEach((b) => {
                    b.classList.remove('bg-[#FDECEF]', 'border-[#ec5a61]', 'text-[#5A3A3A]');
                    b.classList.add('bg-[#FFF7F7]', 'border-[#F3D7DB]', 'text-[#7A5252]');
                });
                renderTopView();
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
            previewTabFrontBtn.addEventListener('click', () => setPreviewTab('front'));
            previewTabTopBtn.addEventListener('click', () => setPreviewTab('top'));

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
            setPreviewTab('front');
            updateStepView();
        })();
    </script>
@endsection

