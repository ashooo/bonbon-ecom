<!-- Customization Pricing Section -->
<div id="customization-section" class="admin-section hidden">
    @php
        $customizationPricing = \App\Support\CustomizationPricing::mergeWithDefaults(($settings?->customization_pricing ?? null));
        $groups = [
            'size' => 'Base Size',
            'layers' => 'Tiers',
            'sponge' => 'Sponge',
            'filling' => 'Filling',
            'frosting' => 'Frosting',
            'drip' => 'Drip',
            'topper' => 'Topper',
            'toppings' => 'Toppings',
            'rush' => 'Rush',
        ];

        $groupStyles = [
            'size' => ['icon' => 'ruler', 'chip' => 'bg-pink-100 text-pink-700', 'badge' => 'bg-pink-100 text-pink-700', 'ring' => 'ring-pink-200'],
            'layers' => ['icon' => 'layers', 'chip' => 'bg-rose-100 text-rose-700', 'badge' => 'bg-rose-100 text-rose-700', 'ring' => 'ring-rose-200'],
            'sponge' => ['icon' => 'cake', 'chip' => 'bg-pink-100 text-pink-700', 'badge' => 'bg-pink-100 text-pink-700', 'ring' => 'ring-pink-200'],
            'filling' => ['icon' => 'drop', 'chip' => 'bg-[#F7E4E8] text-[#7A5252]', 'badge' => 'bg-[#F7E4E8] text-[#7A5252]', 'ring' => 'ring-pink-200'],
            'frosting' => ['icon' => 'swirl', 'chip' => 'bg-[#F3D5E0] text-[#5A3A3A]', 'badge' => 'bg-[#F3D5E0] text-[#5A3A3A]', 'ring' => 'ring-pink-200'],
            'drip' => ['icon' => 'drip', 'chip' => 'bg-rose-100 text-rose-700', 'badge' => 'bg-rose-100 text-rose-700', 'ring' => 'ring-rose-200'],
            'topper' => ['icon' => 'star', 'chip' => 'bg-pink-100 text-pink-700', 'badge' => 'bg-pink-100 text-pink-700', 'ring' => 'ring-pink-200'],
            'toppings' => ['icon' => 'sparkles', 'chip' => 'bg-rose-100 text-rose-700', 'badge' => 'bg-rose-100 text-rose-700', 'ring' => 'ring-rose-200'],
            'rush' => ['icon' => 'bolt', 'chip' => 'bg-[#FDECEF] text-[#C88A92]', 'badge' => 'bg-[#FDECEF] text-[#C88A92]', 'ring' => 'ring-pink-200'],
        ];
    @endphp

    <div class="space-y-6" id="customization-pricing-root">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft">
            <h2 class="text-xl font-semibold text-[#5A3A3A]">Shop Fees & Charges</h2>
            <p class="mt-1 text-sm text-slate-600">Separate store-level fees used during checkout and transaction totals.</p>
            <form action="{{ route('admin.shop-fees.update') }}" method="POST" class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3">
                @csrf
                <label class="rounded-2xl border border-slate-200 bg-slate-50/60 px-3 py-3 transition hover:border-pink-200">
                    <span class="mb-2 inline-flex h-7 w-7 items-center justify-center rounded-lg bg-pink-100 text-pink-700">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h10v7H3z"/><path d="M13 10h4l3 3v1h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg>
                    </span>
                    <span class="mb-1 block text-xs font-medium uppercase tracking-wide text-slate-500">Delivery Fee</span>
                    <input
                        name="delivery_fee"
                        type="number"
                        min="0"
                        step="0.01"
                        required
                        value="{{ old('delivery_fee', number_format((float) ($settings?->delivery_fee ?? 5.99), 2, '.', '')) }}"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xl font-semibold text-slate-800 focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                    />
                </label>
                <label class="rounded-2xl border border-slate-200 bg-slate-50/60 px-3 py-3 transition hover:border-pink-200">
                    <span class="mb-2 inline-flex h-7 w-7 items-center justify-center rounded-lg bg-rose-100 text-rose-700">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19 19 4"/><circle cx="7" cy="7" r="3"/><circle cx="17" cy="17" r="3"/></svg>
                    </span>
                    <span class="mb-1 block text-xs font-medium uppercase tracking-wide text-slate-500">Tax Rate (%)</span>
                    <input
                        name="tax_rate"
                        type="number"
                        min="0"
                        max="100"
                        step="0.01"
                        required
                        value="{{ old('tax_rate', number_format((float) ($settings?->tax_rate ?? 10), 2, '.', '')) }}"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xl font-semibold text-slate-800 focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                    />
                </label>
                <label class="rounded-2xl border border-slate-200 bg-slate-50/60 px-3 py-3 transition hover:border-pink-200">
                    <span class="mb-2 inline-flex h-7 w-7 items-center justify-center rounded-lg bg-[#F3D5E0] text-[#5A3A3A]">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14.5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </span>
                    <span class="mb-1 block text-xs font-medium uppercase tracking-wide text-slate-500">Service Fee</span>
                    <input
                        name="service_fee"
                        type="number"
                        min="0"
                        step="0.01"
                        required
                        value="{{ old('service_fee', number_format((float) ($settings?->service_fee ?? 0), 2, '.', '')) }}"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xl font-semibold text-slate-800 focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                    />
                </label>
                <div class="md:col-span-3">
                    <button class="rounded-2xl bg-pink-600 px-6 py-3 text-sm font-semibold text-white hover:bg-pink-700">
                        Save Shop Fees
                    </button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft">
                <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">Customization pricing</h2>
                        <p class="text-sm text-slate-500">Set add-on prices used by the custom cake builder.</p>
                    </div>

                    <div class="w-full md:w-80">
                        <label for="customization-filter" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Search option</label>
                        <input
                            id="customization-filter"
                            type="text"
                            placeholder="Search frosting, topper, rush..."
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                        />
                    </div>
                </div>

                <form id="customization-pricing-form" action="{{ route('admin.customization-pricing.update') }}" method="POST" class="space-y-4">
                    @csrf

                    @foreach($groups as $groupKey => $groupLabel)
                        @php
                            $style = $groupStyles[$groupKey] ?? $groupStyles['size'];
                            $groupOptions = $customizationPricing[$groupKey] ?? [];
                            $groupValues = array_values($groupOptions);
                            $groupMin = count($groupValues) ? min($groupValues) : 0;
                            $groupMax = count($groupValues) ? max($groupValues) : 0;
                        @endphp

                        <details class="customization-group rounded-2xl border border-slate-200 bg-slate-50/60" data-group="{{ strtolower($groupLabel) }}">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-4 py-3">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg {{ $style['chip'] }}">
                                        @switch($style['icon'])
                                            @case('ruler')
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 8h16M4 16h16M8 8v3M12 8v3M16 8v3M6 16v-3M10 16v-3M14 16v-3M18 16v-3"/></svg>
                                                @break
                                            @case('layers')
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3 3 8l9 5 9-5-9-5Z"/><path d="m3 12 9 5 9-5"/><path d="m3 16 9 5 9-5"/></svg>
                                                @break
                                            @case('cake')
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20h16M6 20v-6h12v6M8 14V9a4 4 0 0 1 8 0v5"/></svg>
                                                @break
                                            @case('drop')
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3s6 7 6 11a6 6 0 1 1-12 0c0-4 6-11 6-11Z"/></svg>
                                                @break
                                            @case('swirl')
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 14c0-4 3-7 7-7 3 0 5 2 5 4s-2 4-5 4h-1c-1 0-2 .8-2 2s1 2 2 2h8"/></svg>
                                                @break
                                            @case('drip')
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16v4c0 1.7-1.3 3-3 3h-1l-1 3-2-3h-2l-2 3-1-3H7c-1.7 0-3-1.3-3-3V7Z"/></svg>
                                                @break
                                            @case('star')
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 3 2.7 5.5 6.1.9-4.4 4.2 1 6-5.4-2.9-5.4 2.9 1-6L3.2 9.4l6.1-.9L12 3Z"/></svg>
                                                @break
                                            @case('sparkles')
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 3 1.5 3.5L17 8l-3.5 1.5L12 13l-1.5-3.5L7 8l3.5-1.5L12 3Z"/><path d="m5 14 .8 1.8L7.6 17l-1.8.8L5 19.6l-.8-1.8L2.4 17l1.8-.8L5 14Z"/><path d="m19 14 .8 1.8 1.8 1.2-1.8.8-.8 1.8-.8-1.8-1.8-.8 1.8-1.2.8-1.8Z"/></svg>
                                                @break
                                            @default
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v18M3 12h18"/></svg>
                                        @endswitch
                                    </span>
                                    <div>
                                        <h3 class="text-base font-semibold text-slate-800">{{ $groupLabel }}</h3>
                                        <p class="text-xs text-slate-500">{{ count($groupOptions) }} options</p>
                                    </div>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $style['badge'] }}">
                                    &#8369;{{ number_format((float) $groupMin, 2) }} - &#8369;{{ number_format((float) $groupMax, 2) }}
                                </span>
                            </summary>

                            <div class="grid grid-cols-1 gap-3 px-4 pb-4 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($groupOptions as $optionKey => $priceValue)
                                    @php
                                        $baseLabel = trim(str_replace('_', ' ', (string) $optionKey));
                                        $normalizedLabel = strtolower($baseLabel);

                                        if ($groupKey === 'size' && is_numeric($normalizedLabel)) {
                                            $displayLabel = $normalizedLabel . ' inch';
                                        } elseif ($groupKey === 'layers' && is_numeric($normalizedLabel)) {
                                            $displayLabel = $normalizedLabel . ((int) $normalizedLabel === 1 ? ' tier' : ' tiers');
                                        } else {
                                            $displayLabel = $baseLabel;
                                        }

                                        $optionLabel = strtoupper($displayLabel);
                                        $isFree = (float) $priceValue <= 0;
                                    @endphp
                                    <label
                                        class="customization-option rounded-2xl border px-3 py-3 transition {{ $isFree ? 'border-slate-200 bg-slate-100/70' : 'border-slate-300 bg-white' }}"
                                        data-search="{{ strtolower($groupLabel . ' ' . $optionLabel) }}"
                                    >
                                        <span class="mb-1 block text-xs font-medium tracking-wide text-slate-500">{{ $optionLabel }}</span>
                                        <div class="flex items-end justify-between gap-2">
                                            <input
                                                name="customization_pricing[{{ $groupKey }}][{{ $optionKey }}]"
                                                type="hidden"
                                                min="0"
                                                step="0.01"
                                                value="{{ old("customization_pricing.$groupKey.$optionKey", $priceValue) }}"
                                                data-initial="{{ old("customization_pricing.$groupKey.$optionKey", $priceValue) }}"
                                                data-group-key="{{ $groupKey }}"
                                                class="customization-price-input"
                                            />
                                            <input
                                                type="text"
                                                value="{{ $isFree ? 'Free' : '₱' . number_format((float) $priceValue, 2) }}"
                                                class="customization-price-editor w-full rounded-lg border border-transparent bg-transparent px-0 py-0 text-3xl font-semibold leading-none {{ $isFree ? 'text-slate-400' : 'text-slate-800' }} focus:border-pink-200 focus:bg-white/80 focus:px-2 focus:py-1 focus:outline-none"
                                                data-free-class="text-slate-400"
                                                data-paid-class="text-slate-800"
                                            />
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </details>
                    @endforeach

                    <div id="customization-savebar" class="sticky bottom-4 z-20 hidden rounded-2xl border border-pink-200 bg-white/95 px-4 py-3 shadow-soft backdrop-blur">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm font-medium text-slate-700">You have unsaved pricing changes.</p>
                            <div class="flex items-center gap-2">
                                <button type="button" id="customization-reset" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Reset</button>
                                <button class="rounded-xl bg-pink-600 px-4 py-2 text-sm font-semibold text-white hover:bg-pink-700">Save Pricing</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <aside class="self-start rounded-3xl border border-slate-200 bg-white p-6 shadow-soft xl:sticky xl:top-6">
                <h3 class="text-base font-semibold text-slate-800">Live summary</h3>
                <p class="mt-1 text-xs text-slate-500">Based on current values in this form.</p>

                <div class="mt-5 rounded-2xl bg-slate-100 px-4 py-3 text-center">
                    <p id="summary-options" class="text-2xl font-bold text-slate-900">0</p>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Total options</p>
                </div>

                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                        <dt class="text-slate-500">Avg. option price</dt>
                        <dd id="summary-average" class="text-xl font-bold text-pink-600">&#8369;0.00</dd>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                        <dt class="text-slate-500">Lowest option</dt>
                        <dd id="summary-min" class="font-semibold text-slate-800">&#8369;0.00</dd>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                        <dt class="text-slate-500">Highest option</dt>
                        <dd id="summary-max" class="font-semibold text-slate-800">&#8369;0.00</dd>
                    </div>
                </dl>

                <div class="mt-4 rounded-2xl border border-pink-100 bg-pink-50/60 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#7A5252]">Estimated Custom Cake Total</p>
                    <p class="mt-1 text-xs text-slate-600">Combined pricing across all groups.</p>
                    <dl class="mt-3 space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-500">Lowest combo</dt>
                            <dd id="combo-min" class="font-semibold text-slate-800">&#8369;0.00</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-500">Average combo</dt>
                            <dd id="combo-avg" class="font-semibold text-[#C88A92]">&#8369;0.00</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-500">Highest combo</dt>
                            <dd id="combo-max" class="font-semibold text-slate-800">&#8369;0.00</dd>
                        </div>
                    </dl>
                </div>
            </aside>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const root = document.getElementById('customization-pricing-root');
                if (!root) return;

                const form = root.querySelector('#customization-pricing-form');
                const filterInput = root.querySelector('#customization-filter');
                const resetBtn = root.querySelector('#customization-reset');
                const saveBar = root.querySelector('#customization-savebar');
                const optionCards = Array.from(root.querySelectorAll('.customization-option'));
                const groups = Array.from(root.querySelectorAll('.customization-group'));
                const inputs = Array.from(root.querySelectorAll('.customization-price-input'));
                const editors = Array.from(root.querySelectorAll('.customization-price-editor'));

                const optionsEl = root.querySelector('#summary-options');
                const avgEl = root.querySelector('#summary-average');
                const minEl = root.querySelector('#summary-min');
                const maxEl = root.querySelector('#summary-max');
                const comboMinEl = root.querySelector('#combo-min');
                const comboAvgEl = root.querySelector('#combo-avg');
                const comboMaxEl = root.querySelector('#combo-max');
                let summaryState = { count: 0, avg: 0, min: 0, max: 0 };
                let comboState = { min: 0, avg: 0, max: 0 };

                const peso = (value) => {
                    const safe = Number.isFinite(value) ? value : 0;
                    return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(safe);
                };

                const formatPreview = (value) => {
                    if (!Number.isFinite(value) || value <= 0) return 'Free';
                    return '₱' + Number(value).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                };

                const parsePrice = (rawValue) => {
                    const normalized = String(rawValue || '').trim().toLowerCase();
                    if (normalized === '' || normalized === 'free') return 0;
                    const numeric = Number(normalized.replace(/[^0-9.]/g, ''));
                    return Number.isFinite(numeric) ? Math.max(0, numeric) : 0;
                };

                const animateNumber = (from, to, duration, onUpdate) => {
                    const start = performance.now();
                    const step = (timestamp) => {
                        const progress = Math.min((timestamp - start) / duration, 1);
                        const eased = 1 - Math.pow(1 - progress, 3);
                        const current = from + (to - from) * eased;
                        onUpdate(current);
                        if (progress < 1) {
                            window.requestAnimationFrame(step);
                        }
                    };
                    window.requestAnimationFrame(step);
                };

                const updateChipState = (input) => {
                    const value = Number(input.value);
                    const card = input.closest('.customization-option');
                    const editor = card ? card.querySelector('.customization-price-editor') : null;
                    const isFree = !Number.isFinite(value) || value <= 0;

                    if (editor) {
                        if (document.activeElement !== editor) {
                            editor.value = formatPreview(value);
                        }
                        const freeClass = editor.dataset.freeClass || 'text-slate-400';
                        const paidClass = editor.dataset.paidClass || 'text-slate-800';
                        editor.classList.toggle(freeClass, isFree);
                        editor.classList.toggle(paidClass, !isFree);
                    }

                    if (card) {
                        card.classList.toggle('bg-slate-100/70', isFree);
                        card.classList.toggle('border-slate-200', isFree);
                        card.classList.toggle('bg-white', !isFree);
                        card.classList.toggle('border-slate-300', !isFree);
                    }
                };

                const recalcSummary = () => {
                    const values = inputs.map((input) => Number(input.value)).filter((value) => Number.isFinite(value) && value >= 0);
                    const count = values.length;
                    const min = count ? Math.min(...values) : 0;
                    const max = count ? Math.max(...values) : 0;
                    const avg = count ? values.reduce((sum, value) => sum + value, 0) / count : 0;
                    const groupsMap = new Map();

                    inputs.forEach((input) => {
                        const groupKey = input.dataset.groupKey || '';
                        const value = Number(input.value);
                        if (!groupKey || !Number.isFinite(value) || value < 0) return;
                        if (!groupsMap.has(groupKey)) groupsMap.set(groupKey, []);
                        groupsMap.get(groupKey).push(value);
                    });

                    let comboMin = 0;
                    let comboAvg = 0;
                    let comboMax = 0;
                    groupsMap.forEach((groupValues) => {
                        if (!groupValues.length) return;
                        comboMin += Math.min(...groupValues);
                        comboMax += Math.max(...groupValues);
                        comboAvg += groupValues.reduce((sum, val) => sum + val, 0) / groupValues.length;
                    });

                    const prev = summaryState;
                    animateNumber(prev.count, count, 280, (v) => {
                        optionsEl.textContent = String(Math.round(v));
                    });
                    animateNumber(prev.avg, avg, 320, (v) => {
                        avgEl.textContent = peso(v);
                    });
                    animateNumber(prev.min, min, 320, (v) => {
                        minEl.textContent = peso(v);
                    });
                    animateNumber(prev.max, max, 320, (v) => {
                        maxEl.textContent = peso(v);
                    });
                    summaryState = { count, avg, min, max };

                    const prevCombo = comboState;
                    animateNumber(prevCombo.min, comboMin, 360, (v) => {
                        comboMinEl.textContent = peso(v);
                    });
                    animateNumber(prevCombo.avg, comboAvg, 360, (v) => {
                        comboAvgEl.textContent = peso(v);
                    });
                    animateNumber(prevCombo.max, comboMax, 360, (v) => {
                        comboMaxEl.textContent = peso(v);
                    });
                    comboState = { min: comboMin, avg: comboAvg, max: comboMax };
                };

                const refreshDirtyState = () => {
                    let dirty = false;

                    inputs.forEach((input) => {
                        const initial = Number(input.dataset.initial || 0);
                        const current = Number(input.value || 0);
                        const changed = Math.abs(current - initial) > 0.0001;
                        const card = input.closest('.customization-option');

                        if (card) {
                            card.classList.toggle('ring-2', changed);
                            card.classList.toggle('ring-pink-200', changed);
                        }

                        if (changed) dirty = true;
                    });

                    if (saveBar) {
                        saveBar.classList.toggle('hidden', !dirty);
                    }
                };

                const applyFilter = () => {
                    const query = (filterInput?.value || '').trim().toLowerCase();

                    optionCards.forEach((card) => {
                        const target = card.dataset.search || '';
                        const visible = query === '' || target.includes(query);
                        card.classList.toggle('hidden', !visible);
                    });

                    groups.forEach((group) => {
                        const hasVisible = !!group.querySelector('.customization-option:not(.hidden)');
                        group.classList.toggle('hidden', !hasVisible);
                        if (query !== '' && hasVisible) {
                            group.open = true;
                        }
                    });
                };

                inputs.forEach((input) => {
                    updateChipState(input);
                });

                editors.forEach((editor) => {
                    const hidden = editor.closest('.customization-option')?.querySelector('.customization-price-input');
                    if (!hidden) return;

                    editor.addEventListener('focus', () => {
                        editor.value = Number(hidden.value || 0).toFixed(2);
                    });

                    editor.addEventListener('blur', () => {
                        const nextValue = parsePrice(editor.value);
                        hidden.value = nextValue.toFixed(2);
                        updateChipState(hidden);
                        recalcSummary();
                        refreshDirtyState();
                    });

                    editor.addEventListener('keydown', (event) => {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            editor.blur();
                        }
                    });
                });

                filterInput?.addEventListener('input', applyFilter);

                resetBtn?.addEventListener('click', () => {
                    inputs.forEach((input) => {
                        input.value = input.dataset.initial || '0';
                        updateChipState(input);
                    });
                    recalcSummary();
                    refreshDirtyState();
                });

                form?.addEventListener('submit', () => {
                    if (saveBar) saveBar.classList.add('hidden');
                });

                recalcSummary();
                refreshDirtyState();
                applyFilter();
            });
        </script>
    @endpush
@endonce
