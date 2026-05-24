<div id="shop-fees-section" class="admin-section hidden">
    <div class="space-y-6">
        <div class="rounded-3xl border border-pink-200 bg-white p-6 shadow-soft">
            <h2 class="text-xl font-semibold text-[#5A3A3A]">Shop Fees</h2>
            <p class="mt-1 text-sm text-slate-600">Configure order-level fees used in checkout and transaction totals.</p>

            <form action="{{ route('admin.shop-fees.update') }}" method="POST" class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-3">
                @csrf
                <label class="rounded-xl border border-slate-200 bg-pink-50/40 px-4 py-3">
                    <span class="mb-1 block text-xs uppercase tracking-wide text-slate-500">Delivery Fee</span>
                    <input
                        name="delivery_fee"
                        type="number"
                        min="0"
                        step="0.01"
                        required
                        value="{{ old('delivery_fee', number_format((float) ($settings?->delivery_fee ?? 5.99), 2, '.', '')) }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                    />
                </label>

                <label class="rounded-xl border border-slate-200 bg-pink-50/40 px-4 py-3">
                    <span class="mb-1 block text-xs uppercase tracking-wide text-slate-500">Tax Rate (%)</span>
                    <input
                        name="tax_rate"
                        type="number"
                        min="0"
                        max="100"
                        step="0.01"
                        required
                        value="{{ old('tax_rate', number_format((float) ($settings?->tax_rate ?? 10), 2, '.', '')) }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                    />
                </label>

                <label class="rounded-xl border border-slate-200 bg-pink-50/40 px-4 py-3">
                    <span class="mb-1 block text-xs uppercase tracking-wide text-slate-500">Service Fee</span>
                    <input
                        name="service_fee"
                        type="number"
                        min="0"
                        step="0.01"
                        required
                        value="{{ old('service_fee', number_format((float) ($settings?->service_fee ?? 0), 2, '.', '')) }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                    />
                </label>

                <div class="md:col-span-3">
                    <button class="rounded-2xl bg-pink-600 px-6 py-3 text-sm font-semibold text-white hover:bg-pink-700">
                        Save Shop Fees
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
