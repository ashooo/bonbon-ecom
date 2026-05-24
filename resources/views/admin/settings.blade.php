<!-- Settings Section -->
<div id="settings-section" class="admin-section hidden">
    <div class="space-y-6">
        <div class="relative overflow-hidden rounded-3xl border border-pink-100 bg-[linear-gradient(135deg,#fff7fb_0%,#ffeaf4_48%,#ffe2f0_100%)] px-6 py-6 shadow-sm">
            <div class="pointer-events-none absolute -right-10 -top-14 h-36 w-36 rounded-full bg-pink-200/40 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-10 left-24 h-28 w-28 rounded-full bg-rose-200/35 blur-2xl"></div>
            <div class="relative flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-pink-500">Admin Panel</p>
                    <h1 class="mt-1 text-2xl font-semibold text-[#6f2148]">Store Settings</h1>
                    <p class="mt-1 text-sm text-[#8b4a67]">Manage brand identity and footer details shown across your storefront.</p>
                </div>
                <div class="inline-flex w-fit items-center gap-2 rounded-full border border-pink-200 bg-white/90 px-4 py-2 text-xs font-medium text-pink-700 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    Live configuration
                </div>
            </div>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            @csrf

            <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_12px_30px_rgba(15,23,42,0.07)]">
                <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4">
                    <h2 class="text-xl font-semibold text-slate-800">Store Information</h2>
                    <span class="rounded-full bg-pink-50 px-3 py-1 text-xs font-medium text-pink-700">Brand</span>
                </div>
                <div class="space-y-4">
                    <div>
                        <label for="brand_name" class="block text-sm font-medium text-slate-700 mb-2">Brand Name</label>
                        <input
                            id="brand_name"
                            name="brand_name"
                            type="text"
                            value="{{ old('brand_name', $settings?->brand_name ?? $storeSettings?->brand_name ?? 'BonBon PH') }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3 text-sm text-slate-700 focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100"
                        />
                    </div>

                    <div>
                        <label for="chat_display_name" class="block text-sm font-medium text-slate-700 mb-2">Bonbon Chat Display Name</label>
                        <input
                            id="chat_display_name"
                            name="chat_display_name"
                            type="text"
                            value="{{ old('chat_display_name', $settings?->chat_display_name ?? $storeSettings?->chat_display_name ?? 'Bonbon Chat') }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3 text-sm text-slate-700 focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100"
                        />
                    </div>

                    <div>
                        <label for="hero_image" class="block text-sm font-medium text-slate-700 mb-2">Homepage Hero Image</label>
                        <input id="hero_image" name="hero_image" type="file" class="w-full rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3 text-sm" />

                        @if(($settings?->hero_image_url ?? $storeSettings?->hero_image_url))
                            <img
                                src="{{ $settings?->hero_image_url ?? $storeSettings?->hero_image_url }}"
                                alt="Hero preview"
                                class="mt-4 max-h-48 w-full rounded-2xl border border-slate-100 bg-white object-contain p-2"
                            />
                        @endif
                    </div>

                    <div>
                        <label for="chat_avatar" class="block text-sm font-medium text-slate-700 mb-2">Bonbon Chat Avatar</label>
                        <input id="chat_avatar" name="chat_avatar" type="file" class="w-full rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3 text-sm" />

                        @if(($settings?->chat_avatar_url ?? $storeSettings?->chat_avatar_url))
                            <img
                                src="{{ $settings?->chat_avatar_url ?? $storeSettings?->chat_avatar_url }}"
                                alt="Bonbon Chat avatar preview"
                                class="mt-4 h-20 w-20 rounded-full border-2 border-white object-cover shadow-sm ring-1 ring-slate-200"
                            />
                        @endif
                    </div>

                    <div>
                        <label for="store_description" class="block text-sm font-medium text-slate-700 mb-2">Store Description</label>
                        <textarea
                            id="store_description"
                            name="store_description"
                            rows="4"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3 text-sm text-slate-700 focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100"
                        >{{ old('store_description', $settings?->store_description ?? $storeSettings?->store_description ?? '') }}</textarea>
                        <p class="mt-2 text-xs text-slate-500">This text appears in the middle of the homepage hero image.</p>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_12px_30px_rgba(15,23,42,0.07)]">
                <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4">
                    <h2 class="text-xl font-semibold text-slate-800">Footer Information</h2>
                    <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-medium text-rose-700">Public</span>
                </div>
                <div class="space-y-4">
                    <div>
                        <label for="footer_email" class="block text-sm font-medium text-slate-700 mb-2">Contact Email</label>
                        <input
                            id="footer_email"
                            name="footer_email"
                            type="email"
                            value="{{ old('footer_email', $settings?->footer_email ?? $storeSettings?->footer_email ?? '') }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3 text-sm text-slate-700 focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100"
                        />
                    </div>

                    <div>
                        <label for="footer_phone" class="block text-sm font-medium text-slate-700 mb-2">Phone</label>
                        <input
                            id="footer_phone"
                            name="footer_phone"
                            type="text"
                            value="{{ old('footer_phone', $settings?->footer_phone ?? $storeSettings?->footer_phone ?? '') }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3 text-sm text-slate-700 focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100"
                        />
                    </div>

                    <div>
                        <label for="footer_address" class="block text-sm font-medium text-slate-700 mb-2">Address</label>
                        <input
                            id="footer_address"
                            name="footer_address"
                            type="text"
                            value="{{ old('footer_address', $settings?->footer_address ?? $storeSettings?->footer_address ?? '') }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3 text-sm text-slate-700 focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100"
                        />
                    </div>

                    <div>
                        <label for="footer_hours" class="block text-sm font-medium text-slate-700 mb-2">Store Hours</label>
                        <textarea
                            id="footer_hours"
                            name="footer_hours"
                            rows="3"
                            placeholder="e.g., Mon-Fri: 9:00 AM - 6:00 PM&#10;Sat-Sun: 10:00 AM - 5:00 PM"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3 text-sm text-slate-700 focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100"
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
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-3 text-sm text-slate-700 focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100"
                        />
                    </div>
                </div>

                <button class="mt-6 inline-flex items-center rounded-xl bg-[linear-gradient(135deg,#e6499b_0%,#d61f7a_100%)] px-6 py-3 text-sm font-semibold text-white shadow-[0_10px_24px_rgba(214,31,122,0.35)] transition hover:brightness-105 focus:outline-none focus:ring-2 focus:ring-pink-300 focus:ring-offset-2">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
