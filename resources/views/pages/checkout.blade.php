@extends('layouts.app')

@section('content')
    @php
        $items = $items ?? collect();
        $subtotal = $subtotal ?? 0;
        $delivery = $delivery ?? 5.99;
        $tax = $tax ?? ($subtotal * 0.1);
        $total = $total ?? ($subtotal + $delivery + $tax);
        $maxPreOrderDays = $maxPreOrderDays ?? 0;
        $minFulfillmentDate = $minFulfillmentDate ?? now()->toDateString();
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
        <div>
            <h2 class="text-2xl font-bold mb-6">Order Summary</h2>
            <div class="bg-white rounded-lg shadow-md p-6">
                @if ($items->count() > 0)
                    <div class="space-y-4">
                        @foreach ($items as $item)
                            <div class="flex items-center space-x-4">
                                <img
                                    src="{{ $item->product?->main_image_url ?? 'https://via.placeholder.com/80x80?text=Product' }}"
                                    alt="{{ $item->product?->name ?? 'Product' }}"
                                    class="w-16 h-16 rounded object-cover"
                                >
                                <div class="flex-1">
                                    <h3 class="font-semibold">{{ $item->product?->name ?? 'Unavailable product' }}</h3>
                                    @if ($item->variant?->name)
                                        <p class="text-gray-600">{{ $item->variant->name }}</p>
                                    @endif
                                    @if ($item->product?->status === 'pre_order' && ($item->product?->pre_order_days ?? 0) > 0)
                                        <p class="text-amber-700 text-sm">Pre-order: {{ $item->product->pre_order_days }} day lead time</p>
                                    @endif
                                    <p class="text-pink-600">&#8369;{{ number_format($item->unit_price, 2) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                                    <p class="font-semibold">&#8369;{{ number_format($item->unit_price * $item->quantity, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <hr class="my-6">

                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>&#8369;{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Delivery</span>
                            <span>&#8369;{{ number_format($delivery, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tax</span>
                            <span>&#8369;{{ number_format($tax, 2) }}</span>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="flex justify-between text-xl font-bold">
                        <span>Total</span>
                        <span>&#8369;{{ number_format($total, 2) }}</span>
                    </div>
                @else
                    <p class="text-gray-600">Your cart is empty.</p>
                    <a href="/products" class="mt-4 inline-block bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                        Continue Shopping
                    </a>
                @endif
            </div>
        </div>

        <!-- Delivery Information and Payment -->
        <div>
            <!-- Delivery Information -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-6">Delivery Information</h2>
                <form method="POST" action="{{ route('checkout.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input
                            type="text"
                            name="customer_name"
                            value="{{ old('customer_name', auth()->user()->name ?? '') }}"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input
                            type="email"
                            name="customer_email"
                            value="{{ old('customer_email', auth()->user()->email ?? '') }}"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input
                            type="tel"
                            name="customer_phone"
                            value="{{ old('customer_phone', auth()->user()->phone ?? '') }}"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Order Type</label>
                        <select
                            id="order_type"
                            name="order_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                        >
                            <option value="pickup" {{ old('order_type', 'pickup') === 'pickup' ? 'selected' : '' }}>Pickup</option>
                            <option value="delivery" {{ old('order_type') === 'delivery' ? 'selected' : '' }}>Delivery</option>
                        </select>
                    </div>

                    <div id="delivery_address_group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Address</label>
                        <textarea
                            id="delivery_address"
                            name="delivery_address"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                            {{ old('order_type') === 'delivery' ? 'required' : '' }}
                            placeholder="House/Unit, Street, Barangay, City"
                        >{{ old('delivery_address') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Required if order type is Delivery.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fulfillment Date</label>
                        @if ($maxPreOrderDays > 0)
                            <p class="text-xs text-amber-700 mb-2">
                                Earliest available schedule is {{ $maxPreOrderDays }} day(s) from now because of pre-order items in your cart.
                            </p>
                        @endif
                        <input
                            type="date"
                            name="fulfillment_date"
                            value="{{ old('fulfillment_date') }}"
                            min="{{ $minFulfillmentDate }}"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fulfillment Time</label>
                        <input
                            type="time"
                            name="fulfillment_time"
                            value="{{ old('fulfillment_time') }}"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Special Instructions</label>
                        <textarea
                            name="special_instructions"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500"
                            placeholder="Optional notes for your order"
                        >{{ old('special_instructions') }}</textarea>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold mb-6">Payment Method</h2>
                        <div class="space-y-4">
                            <div class="border border-gray-300 rounded-md p-4">
                                <label class="flex items-center">
                                    <input type="radio" checked disabled class="mr-3">
                                    <span class="font-medium">Cash on Delivery / Store Confirmation</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Place Order Button -->
                    <button class="w-full bg-pink-600 hover:bg-pink-700 disabled:bg-gray-400 text-white font-bold py-4 px-6 rounded-lg text-lg transition duration-300" {{ $items->count() === 0 ? 'disabled' : '' }}>
                        Place Order
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const orderType = document.getElementById('order_type');
            const deliveryAddress = document.getElementById('delivery_address');
            if (!orderType || !deliveryAddress) return;

            const toggleDelivery = () => {
                if (orderType.value === 'delivery') {
                    deliveryAddress.setAttribute('required', 'required');
                } else {
                    deliveryAddress.removeAttribute('required');
                }
            };

            orderType.addEventListener('change', toggleDelivery);
            toggleDelivery();
        })();
    </script>
@endsection
