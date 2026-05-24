@extends('layouts.app')

@section('content')
    @php
        $items = $items ?? collect();
        $subtotal = $subtotal ?? 0;
        $configuredDeliveryFee = $configuredDeliveryFee ?? 5.99;
        $configuredTaxRate = $configuredTaxRate ?? 10;
        $serviceFee = $serviceFee ?? 0;
        $delivery = $delivery ?? 0;
        $tax = $tax ?? ($subtotal * ($configuredTaxRate / 100));
        $total = $total ?? ($subtotal + $delivery + $tax + $serviceFee);
        $maxPreOrderDays = $maxPreOrderDays ?? 0;
        $minFulfillmentDate = $minFulfillmentDate ?? now()->toDateString();
        $minFulfillmentTime = $minFulfillmentTime ?? now()->format('H:i');
    @endphp

    <h1 class="text-3xl font-bold mb-8">Checkout</h1>

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
            <p class="font-semibold">Please fix the following:</p>
            <ul class="mt-2 list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Order Summary -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-[#5A3A3A] flex items-center gap-2">
                    <svg class="w-6 h-6 text-[#C88A92]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Order Summary
                </h2>
                <span class="bg-[#F8E2E7] text-[#C88A92] px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                    {{ $items->count() }} {{ Str::plural('Item', $items->count()) }}
                </span>
            </div>

            <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-[#F5E6E8] overflow-hidden">
                @if ($items->count() > 0)
                    <div class="p-8 space-y-6">
                        <div class="max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                            @foreach ($items as $item)
                                @php
                                    $itemName = $item->product?->name ?? (($item->customization_payload['item_name'] ?? null) ?: 'Custom Cake');
                                    $itemVariantLabel = $item->variant?->name ?? (($item->product || $item->variant) ? null : 'Custom Design');
                                @endphp
                                <div class="flex items-center space-x-4 pb-6 border-b border-dashed border-[#F5E6E8] last:border-0 last:pb-0 mb-6 last:mb-0">
                                    <div class="relative">
                                        @if (!empty($item->customization_payload['preview_svg']))
                                            <div class="w-20 h-20 overflow-hidden rounded-2xl ring-4 ring-[#F8E2E7]/30 bg-white [&_svg]:h-full [&_svg]:w-full">
                                                {!! $item->customization_payload['preview_svg'] !!}
                                            </div>
                                        @elseif (!empty($item->customization_payload['preview_image']))
                                            <img
                                                src="{{ $item->customization_payload['preview_image'] }}"
                                                alt="{{ $itemName }}"
                                                class="w-20 h-20 rounded-2xl object-cover ring-4 ring-[#F8E2E7]/30 bg-white"
                                            >
                                        @elseif ($item->product?->main_image_url)
                                            <img
                                                src="{{ $item->product->main_image_url }}"
                                                alt="{{ $itemName }}"
                                                class="w-20 h-20 rounded-2xl object-cover ring-4 ring-[#F8E2E7]/30"
                                            >
                                        @else
                                            <x-custom-cake-thumbnail :payload="$item->customization_payload" width="80" height="80" class="w-20 h-20 rounded-2xl object-cover ring-4 ring-[#F8E2E7]/30" />
                                        @endif
                                        <span class="absolute -top-0 -right-0 bg-[#5A3A3A] text-white text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full shadow-lg">
                                            {{ $item->quantity }}
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-bold text-[#5A3A3A] leading-tight">{{ $itemName }}</h3>
                                        @if ($item->variant?->name || $itemVariantLabel)
                                            <p class="text-[#8C6770] text-sm mt-1">{{ $item->variant?->name ?? $itemVariantLabel }}</p>
                                        @endif
                                        @if ($item->product?->status === 'pre_order' && ($item->product?->pre_order_days ?? 0) > 0)
                                            <div class="flex items-center gap-1.5 mt-2">
                                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                                <p class="text-amber-700 text-[11px] font-semibold uppercase tracking-wider">Pre-order: {{ $item->product->pre_order_days }} day lead time</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        @php($displayUnitPrice = (float) ($item->resolved_unit_price ?? $item->unit_price ?? 0))
                                        @if ($displayUnitPrice > 0)
                                            <p class="text-sm font-bold text-[#C88A92]">&#8369;{{ number_format($displayUnitPrice * $item->quantity, 2) }}</p>
                                            <p class="text-[10px] text-gray-400 font-medium">&#8369;{{ number_format($displayUnitPrice, 2) }} / pc</p>
                                        @else
                                            <p class="text-sm font-bold text-[#C88A92]">Free</p>
                                            <p class="text-[10px] text-gray-400 font-medium">&#8369;0.00 / pc</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="bg-[#FFF9FA] rounded-2xl p-6 space-y-3 border border-[#F8E2E7]/50">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-[#8C6770]">Subtotal</span>
                                <span id="checkout-subtotal" class="font-bold text-[#5A3A3A]" data-value="{{ number_format($subtotal, 2, '.', '') }}">&#8369;{{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-[#8C6770]">Delivery Fee</span>
                                <span id="checkout-delivery" class="font-bold text-[#5A3A3A]" data-delivery-fee="{{ number_format($configuredDeliveryFee, 2, '.', '') }}">&#8369;{{ number_format($delivery, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-[#8C6770]">Tax ({{ number_format((float) $configuredTaxRate, 2) }}%)</span>
                                <span id="checkout-tax" class="font-bold text-[#5A3A3A]" data-value="{{ number_format($tax, 2, '.', '') }}">&#8369;{{ number_format($tax, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-[#8C6770]">Service Fee</span>
                                <span id="checkout-service-fee" class="font-bold text-[#5A3A3A]" data-value="{{ number_format($serviceFee, 2, '.', '') }}">&#8369;{{ number_format($serviceFee, 2) }}</span>
                            </div>
                            
                            <div class="pt-4 mt-2 border-t border-[#EED9DE]">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-[#5A3A3A]">Total Amount</span>
                                    <span id="checkout-total" class="text-2xl font-black text-[#C88A92]" data-value="{{ number_format($total, 2, '.', '') }}">&#8369;{{ number_format($total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="w-20 h-20 bg-[#F8E2E7] rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-[#C88A92]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#5A3A3A] mb-2">Your cart is empty</h3>
                        <p class="text-[#8C6770] mb-8">Looks like you haven't added any sweet treats yet!</p>
                        <a href="/#shop" class="inline-flex items-center gap-2 bg-[#5A3A3A] hover:bg-[#7A5252] text-white font-bold py-3 px-8 rounded-full transition duration-300 shadow-lg shadow-[#5A3A3A]/20">
                            <span>Browse Shop</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Delivery Information and Payment -->
        <div class="space-y-6">
            <h2 class="text-2xl font-bold text-[#5A3A3A] flex items-center gap-2">
                <svg class="w-6 h-6 text-[#C88A92]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Delivery Information
            </h2>

            <form method="POST" action="{{ route('checkout.store') }}" class="space-y-8">
                @csrf
                <input type="hidden" name="selection_mode" value="1">
                @foreach(($selectedItemIds ?? []) as $selectedItemId)
                    <input type="hidden" name="selected_item_ids[]" value="{{ (int) $selectedItemId }}">
                @endforeach
                
                <!-- Main Form Card -->
                <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-[#F5E6E8] p-8 md:p-10 space-y-10">
                    
                    <!-- Section: Contact Information -->
                    <div class="space-y-6">
                        <h3 class="text-sm font-black text-[#C88A92] uppercase tracking-[0.2em] flex items-center gap-3">
                            <span class="w-8 h-[1px] bg-[#EED9DE]"></span>
                            Contact Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-[#8C6770] ml-1 uppercase tracking-wider">Full Name</label>
                                <input
                                    type="text"
                                    name="customer_name"
                                    value="{{ old('customer_name', auth()->user()->name ?? '') }}"
                                    required
                                    placeholder="Enter your full name"
                                    class="w-full px-5 py-4 bg-[#F9EFF1]/30 border border-[#F5E6E8] rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#C88A92]/20 focus:border-[#C88A92] transition-all placeholder:text-[#B28D95]/50 text-[#5A3A3A] font-medium"
                                >
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-[#8C6770] ml-1 uppercase tracking-wider">Email Address</label>
                                <input
                                    type="email"
                                    name="customer_email"
                                    value="{{ old('customer_email', auth()->user()->email ?? '') }}"
                                    required
                                    placeholder="your@email.com"
                                    class="w-full px-5 py-4 bg-[#F9EFF1]/30 border border-[#F5E6E8] rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#C88A92]/20 focus:border-[#C88A92] transition-all placeholder:text-[#B28D95]/50 text-[#5A3A3A] font-medium"
                                >
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <label class="text-xs font-bold text-[#8C6770] ml-1 uppercase tracking-wider">Phone Number</label>
                                <input
                                    type="tel"
                                    name="customer_phone"
                                    value="{{ old('customer_phone', auth()->user()->phone ?? '') }}"
                                    required
                                    placeholder="09XX XXX XXXX"
                                    class="w-full px-5 py-4 bg-[#F9EFF1]/30 border border-[#F5E6E8] rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#C88A92]/20 focus:border-[#C88A92] transition-all placeholder:text-[#B28D95]/50 text-[#5A3A3A] font-medium"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Section: Delivery Method -->
                    <div class="space-y-6">
                        <h3 class="text-sm font-black text-[#C88A92] uppercase tracking-[0.2em] flex items-center gap-3">
                            <span class="w-8 h-[1px] bg-[#EED9DE]"></span>
                            Order Details
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-[#8C6770] ml-1 uppercase tracking-wider">Order Type</label>
                                <div class="relative group">
                                    <select
                                        id="order_type"
                                        name="order_type"
                                        class="w-full appearance-none px-5 py-4 bg-[#F9EFF1]/30 border border-[#F5E6E8] rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#C88A92]/20 focus:border-[#C88A92] transition-all text-[#5A3A3A] font-medium cursor-pointer"
                                    >
                                        <option value="pickup" {{ old('order_type', 'pickup') === 'pickup' ? 'selected' : '' }}>Store Pickup</option>
                                        <option value="delivery" {{ old('order_type') === 'delivery' ? 'selected' : '' }}>Home Delivery</option>
                                    </select>
                                    <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-[#C88A92]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-[#8C6770] ml-1 uppercase tracking-wider">Fulfillment Date</label>
                                <input
                                    type="date"
                                    name="fulfillment_date"
                                    value="{{ old('fulfillment_date') }}"
                                    min="{{ $minFulfillmentDate }}"
                                    required
                                    class="w-full px-5 py-4 bg-[#F9EFF1]/30 border border-[#F5E6E8] rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#C88A92]/20 focus:border-[#C88A92] transition-all text-[#5A3A3A] font-medium"
                                >
                                @if ($maxPreOrderDays > 0)
                                    <p class="text-[10px] text-amber-600 font-bold uppercase tracking-tighter mt-1">
                                        Note: {{ $maxPreOrderDays }} day lead time required
                                    </p>
                                @endif
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <label class="text-xs font-bold text-[#8C6770] ml-1 uppercase tracking-wider">Fulfillment Time</label>
                                <input
                                    type="time"
                                    id="fulfillment_time"
                                    name="fulfillment_time"
                                    value="{{ old('fulfillment_time') }}"
                                    required
                                    class="w-full px-5 py-4 bg-[#F9EFF1]/30 border border-[#F5E6E8] rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#C88A92]/20 focus:border-[#C88A92] transition-all text-[#5A3A3A] font-medium"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Section: Delivery Address (Conditional) -->
                    <div id="delivery_address_group" class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-black text-[#C88A92] uppercase tracking-[0.2em] flex items-center gap-3">
                                <span class="w-8 h-[1px] bg-[#EED9DE]"></span>
                                Delivery Address
                            </h3>
                            
                            @if(Auth::check() && $savedAddresses->count() > 0)
                                <div class="relative inline-block text-left" id="saved-addresses-dropdown">
                                    <button type="button" class="flex items-center gap-1.5 text-[11px] font-black text-[#5A3A3A] bg-[#F8E2E7] hover:bg-[#F0D5DB] px-3 py-1.5 rounded-full transition-colors uppercase tracking-widest shadow-sm">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                                        </svg>
                                        Pinned Addresses
                                    </button>
                                    <div id="address-menu" class="hidden absolute right-0 mt-2 w-72 origin-top-right rounded-2xl bg-white shadow-[0_10px_40px_rgba(0,0,0,0.1)] ring-1 ring-black ring-opacity-5 focus:outline-none z-50 overflow-hidden border border-[#F5E6E8]">
                                        <div class="py-2">
                                            @foreach($savedAddresses as $address)
                                                <button type="button" 
                                                    class="address-option w-full text-left px-4 py-3 text-sm hover:bg-[#FFF9FA] transition-colors border-b border-[#F9EFF1] last:border-0"
                                                    data-address="{{ $address->line1 }}{{ $address->line2 ? ', ' . $address->line2 : '' }}, {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}"
                                                >
                                                    <p class="font-bold text-[#5A3A3A]">{{ $address->label }}</p>
                                                    <p class="text-[11px] text-[#8C6770] truncate mt-0.5">
                                                        {{ $address->line1 }}, {{ $address->city }}
                                                    </p>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-4">
                            <textarea
                                id="delivery_address"
                                name="delivery_address"
                                rows="3"
                                class="w-full px-5 py-4 bg-[#F9EFF1]/30 border border-[#F5E6E8] rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#C88A92]/20 focus:border-[#C88A92] transition-all placeholder:text-[#B28D95]/50 text-[#5A3A3A] font-medium resize-none"
                                {{ old('order_type') === 'delivery' ? 'required' : '' }}
                                placeholder="House/Unit, Street, Barangay, City"
                            >{{ old('delivery_address') }}</textarea>

                            <input type="hidden" id="delivery_lat" name="delivery_lat" value="{{ old('delivery_lat') }}">
                            <input type="hidden" id="delivery_lng" name="delivery_lng" value="{{ old('delivery_lng') }}">

                            <button type="button" id="open-map-picker" class="w-full flex items-center justify-center gap-3 px-5 py-4 bg-gradient-to-r from-[#F8E2E7] to-[#FFF0F3] border-2 border-dashed border-[#E6B7BE] rounded-2xl text-[#5A3A3A] font-bold transition-all hover:border-[#C88A92] hover:shadow-lg hover:shadow-[#C88A92]/10 hover:-translate-y-0.5 group">
                                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                                    <svg class="w-5 h-5 text-[#C88A92]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <span class="block text-sm font-black">Select Delivery Location</span>
                                    <span class="block text-[10px] text-[#8C6770] font-semibold uppercase tracking-wider">Pin your address on the map</span>
                                </div>
                                <svg class="w-5 h-5 ml-auto text-[#C88A92] group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Section: Special Instructions -->
                    <div class="space-y-6">
                        <h3 class="text-sm font-black text-[#C88A92] uppercase tracking-[0.2em] flex items-center gap-3">
                            <span class="w-8 h-[1px] bg-[#EED9DE]"></span>
                            Special Instructions
                        </h3>
                        <textarea
                            name="special_instructions"
                            rows="2"
                            class="w-full px-5 py-4 bg-[#F9EFF1]/30 border border-[#F5E6E8] rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#C88A92]/20 focus:border-[#C88A92] transition-all placeholder:text-[#B28D95]/50 text-[#5A3A3A] font-medium resize-none"
                            placeholder="Optional notes for your order (e.g., allergies, landmark)"
                        >{{ old('special_instructions') }}</textarea>
                    </div>

                    <!-- Section: Payment & Action -->
                    <div class="pt-6 border-t border-[#F5E6E8] space-y-8">
                        <div class="bg-[#F9EFF1]/50 rounded-2xl p-6 border border-[#F8E2E7]">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-xs font-black text-[#C88A92] uppercase tracking-[0.2em]">Payment Method</h4>
                                <span class="bg-[#5A3A3A] text-white text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded">Active</span>
                            </div>
                            <div class="space-y-3">
                                @auth
                                    <label class="flex cursor-pointer items-center gap-4 rounded-xl border border-[#EED9DE] bg-white px-4 py-3 transition hover:border-[#C88A92]">
                                        <input type="radio" name="payment_method" value="cod" class="h-4 w-4 text-[#C88A92] focus:ring-[#C88A92]" {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}>
                                        <div class="w-10 h-10 bg-[#F9EFF1] rounded-xl flex items-center justify-center shadow-sm text-[#C88A92]">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-[#5A3A3A] text-sm leading-none">Cash on Delivery</p>
                                            <p class="text-[11px] text-[#8C6770] mt-1">Pay when receiving your order</p>
                                        </div>
                                    </label>

                                    <label class="flex cursor-pointer items-center gap-4 rounded-xl border border-[#EED9DE] bg-white px-4 py-3 transition hover:border-[#C88A92]">
                                        <input type="radio" name="payment_method" value="paymongo" class="h-4 w-4 text-[#C88A92] focus:ring-[#C88A92]" {{ old('payment_method') === 'paymongo' ? 'checked' : '' }}>
                                        <div class="w-10 h-10 bg-[#F9EFF1] rounded-xl flex items-center justify-center shadow-sm text-[#C88A92]">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-[#5A3A3A] text-sm leading-none">QRPH Online Payment</p>
                                            <p class="text-[11px] text-[#8C6770] mt-1">Secure QRPH checkout via PayMongo</p>
                                        </div>
                                    </label>
                                @else
                                    <label class="flex cursor-pointer items-center gap-4 rounded-xl border border-[#EED9DE] bg-white px-4 py-3 transition hover:border-[#C88A92]">
                                        <input type="radio" name="payment_method" value="paymongo" class="h-4 w-4 text-[#C88A92] focus:ring-[#C88A92]" {{ old('payment_method', 'paymongo') === 'paymongo' ? 'checked' : '' }}>
                                        <div class="w-10 h-10 bg-[#F9EFF1] rounded-xl flex items-center justify-center shadow-sm text-[#C88A92]">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-[#5A3A3A] text-sm leading-none">QRPH Online Payment</p>
                                            <p class="text-[11px] text-[#8C6770] mt-1">Secure QRPH checkout via PayMongo</p>
                                        </div>
                                    </label>
                                    <div class="rounded-xl border border-dashed border-[#EED9DE] bg-[#FFF9FA] px-4 py-3 text-xs text-[#8C6770]">
                                        Guest checkout is available via <span class="font-bold text-[#5A3A3A]">QRPH Online Payment</span> only.
                                    </div>
                                @endauth
                            </div>
                        </div>

                        <button 
                            type="submit"
                            class="w-full bg-[#5A3A3A] hover:bg-[#7A5252] disabled:bg-gray-300 text-white font-black py-5 px-8 rounded-full text-lg transition-all duration-300 shadow-xl shadow-[#5A3A3A]/20 transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3"
                            {{ $items->count() === 0 ? 'disabled' : '' }}
                        >
                            <span>Place Your Order</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Map Picker Modal -->
    <div id="map-modal" class="fixed inset-0 z-[9999] hidden">
        <div id="map-modal-backdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity"></div>
        <div class="absolute inset-3 md:inset-6 lg:inset-10 bg-white rounded-[2rem] shadow-2xl overflow-hidden flex flex-col z-10 map-modal-card">
            <!-- Header -->
            <div class="shrink-0 px-6 py-4 border-b border-[#F5E6E8] flex items-center justify-between bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#F8E2E7] rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#C88A92]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-[#5A3A3A] text-base">Choose Delivery Location</h3>
                        <p class="text-[11px] text-[#8C6770]">Drag the pin or tap to set your location</p>
                    </div>
                </div>
                <button type="button" id="close-map-modal" class="w-10 h-10 rounded-full bg-[#F9EFF1] hover:bg-[#F0D5DB] flex items-center justify-center text-[#5A3A3A] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Search Bar -->
            <div class="shrink-0 px-6 py-3 bg-[#FFF9FA] border-b border-[#F5E6E8]">
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-[#B28D95]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="map-search-input" placeholder="Search for a place or address..." class="w-full pl-11 pr-4 py-3 bg-white border border-[#F5E6E8] rounded-xl text-sm text-[#5A3A3A] placeholder:text-[#B28D95]/60 focus:outline-none focus:ring-2 focus:ring-[#C88A92]/20 focus:border-[#C88A92]">
                </div>
            </div>

            <!-- Map Container -->
            <div class="flex-1 relative min-h-0">
                <div id="map-picker" class="absolute inset-0"></div>

                <!-- Map View Toggle (Street / Satellite) -->
                <div id="map-view-toggle" class="absolute top-4 left-4 z-[1000] flex bg-white rounded-full shadow-lg border border-[#F5E6E8] overflow-hidden">
                    <button type="button" data-layer="street" class="map-layer-btn active px-4 py-2.5 text-[11px] font-black uppercase tracking-wider transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        Map
                    </button>
                    <button type="button" data-layer="satellite" class="map-layer-btn px-4 py-2.5 text-[11px] font-black uppercase tracking-wider transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Satellite
                    </button>
                </div>

                <!-- GPS Locate Button -->
                <button type="button" id="map-locate-me" class="absolute bottom-5 right-5 z-[1000] w-12 h-12 bg-white rounded-full shadow-lg flex items-center justify-center text-[#C88A92] hover:text-[#5A3A3A] hover:shadow-xl transition-all border border-[#F5E6E8]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0013 3.06V1h-2v2.06A8.994 8.994 0 003.06 11H1v2h2.06A8.994 8.994 0 0011 20.94V23h2v-2.06A8.994 8.994 0 0020.94 13H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z" />
                    </svg>
                </button>

                <!-- Road Snap Toast -->
                <div id="map-snap-toast" class="absolute bottom-20 left-1/2 -translate-x-1/2 z-[1000] hidden">
                    <div class="bg-[#5A3A3A] text-white text-xs font-bold px-4 py-2.5 rounded-full shadow-xl flex items-center gap-2 whitespace-nowrap map-toast-anim">
                        <svg class="w-4 h-4 text-[#F8E2E7]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        Snapped to nearest road
                    </div>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="shrink-0 px-6 py-4 border-t border-[#F5E6E8] bg-white">
                <div id="map-selected-address" class="mb-3 min-h-[2.5rem] flex items-center">
                    <span class="text-sm text-[#8C6770]">Move the pin to select your delivery location</span>
                </div>
                <button type="button" id="confirm-map-location" class="w-full bg-[#5A3A3A] hover:bg-[#7A5252] text-white font-black py-4 rounded-full text-sm transition-all duration-300 shadow-lg shadow-[#5A3A3A]/20 flex items-center justify-center gap-2 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Confirm This Location
                </button>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #F9EFF1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #EED9DE; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #C88A92; }

        .map-modal-card { animation: mapSlideUp 0.3s ease-out; }
        @keyframes mapSlideUp {
            from { opacity: 0; transform: translateY(30px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .bonbon-pin {
            width: 44px; height: 54px; position: relative; cursor: grab;
            filter: drop-shadow(0 4px 8px rgba(90,58,58,0.3));
            transition: transform 0.15s ease;
        }
        .bonbon-pin:active { cursor: grabbing; transform: scale(1.15); }
        .bonbon-pin svg { width: 100%; height: 100%; }
        .bonbon-pin-pulse {
            position: absolute; bottom: -4px; left: 50%; transform: translateX(-50%);
            width: 14px; height: 6px; background: rgba(90,58,58,0.18);
            border-radius: 50%; animation: pinPulse 1.5s ease-in-out infinite;
        }
        @keyframes pinPulse {
            0%, 100% { transform: translateX(-50%) scale(1); opacity: 0.4; }
            50% { transform: translateX(-50%) scale(1.8); opacity: 0; }
        }
        #map-picker .leaflet-control-zoom a {
            background: white !important; color: #5A3A3A !important;
            border: 1px solid #F5E6E8 !important; border-radius: 12px !important;
            width: 36px !important; height: 36px !important; line-height: 36px !important;
            font-size: 18px !important; font-weight: 700 !important;
        }
        #map-picker .leaflet-control-zoom { border: none !important; border-radius: 14px !important; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.08) !important; }

        /* Layer Toggle */
        .map-layer-btn { color: #8C6770; background: transparent; }
        .map-layer-btn.active { color: white; background: #5A3A3A; }
        .map-layer-btn:not(.active):hover { background: #F8E2E7; color: #5A3A3A; }

        /* Toast Animation */
        .map-toast-anim {
            animation: toastSlideUp 0.35s ease-out;
        }
        @keyframes toastSlideUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        (function () {
            const orderType = document.getElementById('order_type');
            const deliveryAddress = document.getElementById('delivery_address');
            const deliveryGroup = document.getElementById('delivery_address_group');
            const subtotalEl = document.getElementById('checkout-subtotal');
            const taxEl = document.getElementById('checkout-tax');
            const deliveryEl = document.getElementById('checkout-delivery');
            const serviceFeeEl = document.getElementById('checkout-service-fee');
            const totalEl = document.getElementById('checkout-total');
            const fulfillmentDateInput = document.querySelector('input[name="fulfillment_date"]');
            const fulfillmentTimeInput = document.getElementById('fulfillment_time');

            // Pinned Address Elements
            const addressDropdownBtn = document.querySelector('#saved-addresses-dropdown button');
            const addressMenu = document.getElementById('address-menu');
            const addressOptions = document.querySelectorAll('.address-option');

            // Map Picker Elements
            const mapModal = document.getElementById('map-modal');
            const openMapBtn = document.getElementById('open-map-picker');
            const closeMapBtn = document.getElementById('close-map-modal');
            const mapBackdrop = document.getElementById('map-modal-backdrop');
            const confirmMapBtn = document.getElementById('confirm-map-location');
            const mapAddressEl = document.getElementById('map-selected-address');
            const mapSearchInput = document.getElementById('map-search-input');
            const mapLocateBtn = document.getElementById('map-locate-me');
            const deliveryLatInput = document.getElementById('delivery_lat');
            const deliveryLngInput = document.getElementById('delivery_lng');

            const minDate = @json($minFulfillmentDate);
            const minTime = @json($minFulfillmentTime);

            if (!orderType || !deliveryAddress) return;

            /* ── Totals ── */
            const formatPeso = (v) => `₱${Number(v).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2})}`;

            const refreshTotals = () => {
                if (!subtotalEl || !taxEl || !deliveryEl || !serviceFeeEl || !totalEl) return;
                const subtotal = Number(subtotalEl.dataset.value || '0');
                const tax = Number(taxEl.dataset.value || '0');
                const deliveryFee = Number(deliveryEl.dataset.deliveryFee || '0');
                const serviceFee = Number(serviceFeeEl.dataset.value || '0');
                const delivery = orderType.value === 'delivery' ? deliveryFee : 0;
                deliveryEl.textContent = formatPeso(delivery);
                totalEl.textContent = formatPeso(subtotal + tax + delivery + serviceFee);
            };

            /* ── Time enforcement ── */
            const enforceMinTime = () => {
                if (!fulfillmentDateInput || !fulfillmentTimeInput) return;
                if (fulfillmentDateInput.value === minDate) {
                    fulfillmentTimeInput.min = minTime;
                    if (fulfillmentTimeInput.value && fulfillmentTimeInput.value < minTime) {
                        fulfillmentTimeInput.value = minTime;
                    }
                } else {
                    fulfillmentTimeInput.removeAttribute('min');
                }
            };

            /* ── Toggle delivery section ── */
            const toggleDelivery = () => {
                if (orderType.value === 'delivery') {
                    deliveryGroup.style.display = 'block';
                    deliveryAddress.setAttribute('required', 'required');
                } else {
                    deliveryGroup.style.display = 'none';
                    deliveryAddress.removeAttribute('required');
                }
                refreshTotals();
            };

            /* ── Pinned Address Dropdown ── */
            if (addressDropdownBtn && addressMenu) {
                addressDropdownBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    addressMenu.classList.toggle('hidden');
                });
                document.addEventListener('click', () => addressMenu.classList.add('hidden'));
                addressOptions.forEach(opt => {
                    opt.addEventListener('click', () => {
                        deliveryAddress.value = opt.dataset.address;
                        addressMenu.classList.add('hidden');
                        flashField(deliveryAddress);
                    });
                });
            }

            const flashField = (el) => {
                el.classList.add('ring-2', 'ring-[#C88A92]');
                setTimeout(() => el.classList.remove('ring-2', 'ring-[#C88A92]'), 1200);
            };

            /* ══════════════════════════════════════════
               MAP PICKER (Leaflet + OpenStreetMap)
            ══════════════════════════════════════════ */
            let map = null;
            let marker = null;
            let selectedLat = null;
            let selectedLng = null;
            let selectedAddr = '';
            let searchTimer = null;
            const defaultCenter = [14.5995, 120.9842]; // Metro Manila

            const pinSvg = `<div class="bonbon-pin">
                <svg viewBox="0 0 44 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22 0C10 0 0 10 0 22c0 16.5 22 32 22 32s22-15.5 22-32C44 10 34 0 22 0z" fill="#5A3A3A"/>
                    <circle cx="22" cy="20" r="9" fill="#F8E2E7"/>
                    <circle cx="22" cy="20" r="5" fill="#C88A92"/>
                </svg>
                <div class="bonbon-pin-pulse"></div>
            </div>`;

            const pinIcon = typeof L !== 'undefined' ? L.divIcon({
                html: pinSvg,
                iconSize: [44, 54],
                iconAnchor: [22, 54],
                className: '',
            }) : null;

            /* ── Tile Layers ── */
            let streetLayer = null;
            let satelliteLayer = null;
            let activeLayer = 'street';
            const snapToast = document.getElementById('map-snap-toast');
            let snapToastTimer = null;

            const initMap = () => {
                if (map) { map.invalidateSize(); return; }
                if (typeof L === 'undefined') return;

                map = L.map('map-picker', { center: defaultCenter, zoom: 13, zoomControl: false });
                L.control.zoom({ position: 'topright' }).addTo(map);

                streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://openstreetmap.org/copyright">OSM</a>',
                    maxZoom: 19,
                });

                satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: '&copy; Esri &mdash; Source: Esri, Maxar, Earthstar Geographics',
                    maxZoom: 19,
                });

                streetLayer.addTo(map);

                /* Layer toggle buttons */
                document.querySelectorAll('.map-layer-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const layer = btn.dataset.layer;
                        if (layer === activeLayer) return;
                        activeLayer = layer;
                        document.querySelectorAll('.map-layer-btn').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        if (layer === 'satellite') {
                            map.removeLayer(streetLayer);
                            satelliteLayer.addTo(map);
                        } else {
                            map.removeLayer(satelliteLayer);
                            streetLayer.addTo(map);
                        }
                    });
                });

                marker = L.marker(defaultCenter, { draggable: true, icon: pinIcon }).addTo(map);

                marker.on('dragend', () => {
                    const p = marker.getLatLng();
                    snapToRoad(p.lat, p.lng);
                });

                map.on('click', (e) => {
                    marker.setLatLng(e.latlng);
                    snapToRoad(e.latlng.lat, e.latlng.lng);
                });

                locateUser();
            };

            /* ── OSRM Road Snapping ── */
            const snapToRoad = async (lat, lng) => {
                try {
                    const r = await fetch(`https://router.project-osrm.org/nearest/v1/driving/${lng},${lat}`);
                    const d = await r.json();
                    if (d.code === 'Ok' && d.waypoints && d.waypoints.length > 0) {
                        const snapped = d.waypoints[0].location; // [lng, lat]
                        const sLat = snapped[1], sLng = snapped[0];
                        const dist = distanceMeters(lat, lng, sLat, sLng);

                        if (dist > 5) {
                            // Animate marker to snapped position
                            marker.setLatLng([sLat, sLng]);
                            showSnapToast();
                        }
                        reverseGeocode(sLat, sLng);
                        return;
                    }
                } catch { /* OSRM unreachable – fall through */ }
                // Fallback: no snap, just geocode original position
                reverseGeocode(lat, lng);
            };

            const distanceMeters = (lat1, lng1, lat2, lng2) => {
                const R = 6371000;
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLng = (lng2 - lng1) * Math.PI / 180;
                const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLng/2)**2;
                return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            };

            const showSnapToast = () => {
                if (!snapToast) return;
                clearTimeout(snapToastTimer);
                snapToast.classList.remove('hidden');
                snapToastTimer = setTimeout(() => snapToast.classList.add('hidden'), 2500);
            };

            const locateUser = () => {
                if (!navigator.geolocation) return;
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        const lat = pos.coords.latitude, lng = pos.coords.longitude;
                        map.flyTo([lat, lng], 17, { duration: 1.2 });
                        marker.setLatLng([lat, lng]);
                        snapToRoad(lat, lng);
                    },
                    () => { /* user denied – stay at default */ },
                    { enableHighAccuracy: true, timeout: 8000 }
                );
            };

            const reverseGeocode = async (lat, lng) => {
                selectedLat = lat;
                selectedLng = lng;
                mapAddressEl.innerHTML = `<div class="flex items-center gap-2"><span class="w-4 h-4 rounded-full border-2 border-[#C88A92] border-t-transparent animate-spin"></span><span class="text-sm text-[#8C6770]">Finding address…</span></div>`;

                try {
                    const r = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&addressdetails=1&accept-language=en`);
                    const d = await r.json();
                    if (d && d.display_name) {
                        const a = d.address || {};
                        const parts = [a.house_number, a.road, a.neighbourhood || a.suburb, a.city || a.town || a.municipality, a.state || a.region, a.postcode].filter(Boolean);
                        selectedAddr = parts.join(', ') || d.display_name;
                        mapAddressEl.innerHTML = `<div class="flex items-start gap-2"><svg class="w-4 h-4 text-[#C88A92] mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span class="text-sm text-[#5A3A3A] font-medium leading-snug">${selectedAddr}</span></div>`;
                    }
                } catch {
                    selectedAddr = `Lat ${lat.toFixed(6)}, Lng ${lng.toFixed(6)}`;
                    mapAddressEl.innerHTML = `<span class="text-sm text-amber-600">Could not fetch address name. You can still confirm.</span>`;
                }
            };

            /* ── Modal open / close ── */
            const openMap = () => {
                if (!mapModal) return;
                mapModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                setTimeout(() => initMap(), 150);
            };

            const closeMap = () => {
                if (!mapModal) return;
                mapModal.classList.add('hidden');
                document.body.style.overflow = '';
            };

            openMapBtn?.addEventListener('click', openMap);
            closeMapBtn?.addEventListener('click', closeMap);
            mapBackdrop?.addEventListener('click', closeMap);

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && mapModal && !mapModal.classList.contains('hidden')) closeMap();
            });

            /* ── Locate Me button ── */
            mapLocateBtn?.addEventListener('click', locateUser);

            /* ── Confirm location ── */
            confirmMapBtn?.addEventListener('click', () => {
                if (selectedAddr) {
                    deliveryAddress.value = selectedAddr;
                    if (deliveryLatInput) deliveryLatInput.value = selectedLat;
                    if (deliveryLngInput) deliveryLngInput.value = selectedLng;
                    flashField(deliveryAddress);
                }
                closeMap();
            });

            /* ── Search ── */
            mapSearchInput?.addEventListener('input', () => {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(async () => {
                    const q = mapSearchInput.value.trim();
                    if (q.length < 3 || !map) return;
                    try {
                        const r = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(q)}&countrycodes=ph&limit=1`);
                        const res = await r.json();
                        if (res.length) {
                            const lat = parseFloat(res[0].lat), lng = parseFloat(res[0].lon);
                            map.flyTo([lat, lng], 17, { duration: 1.2 });
                            marker.setLatLng([lat, lng]);
                            snapToRoad(lat, lng);
                        }
                    } catch { /* silently fail */ }
                }, 600);
            });

            mapSearchInput?.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') e.preventDefault();
            });

            /* ── Init ── */
            orderType.addEventListener('change', toggleDelivery);
            fulfillmentDateInput?.addEventListener('change', enforceMinTime);
            enforceMinTime();
            toggleDelivery();
        })();
    </script>
@endsection
