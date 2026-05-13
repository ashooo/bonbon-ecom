<!-- Settings Section -->
<div id="settings-section" class="admin-section hidden">
    @php
        $customizationPricing = \App\Support\CustomizationPricing::mergeWithDefaults(($settings?->customization_pricing ?? null));
    @endphp
    <div class="space-y-6"> 

        @if(session('success'))
            <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            @csrf

            <div class="rounded-3xl bg-white p-6 shadow-soft">
                <h2 class="text-xl font-semibold mb-6">Store Information</h2>
                <div class="space-y-4">
                    <div>
                        <label for="brand_name" class="block text-sm font-medium text-slate-700 mb-2">Brand Name</label>
                        <input
                            id="brand_name"
                            name="brand_name"
                            type="text"
                            value="{{ old('brand_name', $settings?->brand_name ?? $storeSettings?->brand_name ?? 'BonBon PH') }}"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                        />
                    </div>

                    <div>
                        <label for="chat_display_name" class="block text-sm font-medium text-slate-700 mb-2">Bonbon Chat Display Name</label>
                        <input
                            id="chat_display_name"
                            name="chat_display_name"
                            type="text"
                            value="{{ old('chat_display_name', $settings?->chat_display_name ?? $storeSettings?->chat_display_name ?? 'Bonbon Chat') }}"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                        />
                    </div>

                    <div>
                        <label for="hero_image" class="block text-sm font-medium text-slate-700 mb-2">Homepage Hero Image</label>
                        <input id="hero_image" name="hero_image" type="file" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" />

                        @if(($settings?->hero_image_url ?? $storeSettings?->hero_image_url))
                            <img
                                src="{{ $settings?->hero_image_url ?? $storeSettings?->hero_image_url }}"
                                alt="Hero preview"
                                class="mt-4 max-h-48 rounded-2xl object-contain"
                            />
                        @endif
                    </div>

                    <div>
                        <label for="chat_avatar" class="block text-sm font-medium text-slate-700 mb-2">Bonbon Chat Avatar</label>
                        <input id="chat_avatar" name="chat_avatar" type="file" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" />

                        @if(($settings?->chat_avatar_url ?? $storeSettings?->chat_avatar_url))
                            <img
                                src="{{ $settings?->chat_avatar_url ?? $storeSettings?->chat_avatar_url }}"
                                alt="Bonbon Chat avatar preview"
                                class="mt-4 h-20 w-20 rounded-full object-cover"
                            />
                        @endif
                    </div>

                    <div>
                        <label for="store_description" class="block text-sm font-medium text-slate-700 mb-2">Store Description</label>
                        <textarea
                            id="store_description"
                            name="store_description"
                            rows="4"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                        >{{ old('store_description', $settings?->store_description ?? $storeSettings?->store_description ?? '') }}</textarea>
                        <p class="mt-2 text-xs text-slate-500">This text appears in the middle of the homepage hero image.</p>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-soft">
                <h2 class="text-xl font-semibold mb-6">Footer Information</h2>
                <div class="space-y-4">
                    <div>
                        <label for="footer_email" class="block text-sm font-medium text-slate-700 mb-2">Contact Email</label>
                        <input
                            id="footer_email"
                            name="footer_email"
                            type="email"
                            value="{{ old('footer_email', $settings?->footer_email ?? $storeSettings?->footer_email ?? '') }}"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                        />
                    </div>

                    <div>
                        <label for="footer_phone" class="block text-sm font-medium text-slate-700 mb-2">Phone</label>
                        <input
                            id="footer_phone"
                            name="footer_phone"
                            type="text"
                            value="{{ old('footer_phone', $settings?->footer_phone ?? $storeSettings?->footer_phone ?? '') }}"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                        />
                    </div>

                    <div>
                        <label for="footer_address" class="block text-sm font-medium text-slate-700 mb-2">Address</label>
                        <input
                            id="footer_address"
                            name="footer_address"
                            type="text"
                            value="{{ old('footer_address', $settings?->footer_address ?? $storeSettings?->footer_address ?? '') }}"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                        />
                    </div>

                    <div>
                        <label for="footer_hours" class="block text-sm font-medium text-slate-700 mb-2">Store Hours</label>
                        <textarea
                            id="footer_hours"
                            name="footer_hours"
                            rows="3"
                            placeholder="e.g., Mon-Fri: 9:00 AM - 6:00 PM&#10;Sat-Sun: 10:00 AM - 5:00 PM"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                        >{{ old('footer_hours', $settings?->footer_hours ?? $storeSettings?->footer_hours ?? '') }}</textarea>
                        <p class="mt-2 text-xs text-slate-500">Display your store operating hours. You can use line breaks to separate days.</p>
                    </div>

                    <div>
                        <label for="copyright_text" class="block text-sm font-medium text-slate-700 mb-2">Copyright Text</label>
                        <input
                            id="copyright_text"
                            name="copyright_text"
                            type="text"
                            value="{{ old('copyright_text', $settings?->copyright_text ?? $storeSettings?->copyright_text ?? '') }}"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                        />
                    </div>
                </div>

                <button class="mt-6 rounded-2xl bg-pink-600 px-6 py-3 text-sm font-semibold text-white hover:bg-pink-700">
                    Save Changes
                </button>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-soft lg:col-span-2">
                <h2 class="text-xl font-semibold mb-2">Customization Pricing</h2>
                <p class="text-sm text-slate-500 mb-6">Set add-on prices used by the custom cake builder.</p>

                <div class="space-y-6">
                    @php
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
                    @endphp
                    @foreach($groups as $groupKey => $groupLabel)
                        <div>
                            <h3 class="text-sm font-semibold text-slate-700 mb-3">{{ $groupLabel }}</h3>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach(($customizationPricing[$groupKey] ?? []) as $optionKey => $priceValue)
                                    <label class="rounded-2xl border border-slate-200 px-3 py-2">
                                        <span class="block text-xs uppercase tracking-wide text-slate-500 mb-1">{{ str_replace('_', ' ', $optionKey) }}</span>
                                        <input
                                            name="customization_pricing[{{ $groupKey }}][{{ $optionKey }}]"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            value="{{ old("customization_pricing.$groupKey.$optionKey", $priceValue) }}"
                                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                                        />
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </form>
    </div>
</div>
