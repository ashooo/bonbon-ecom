@extends('layouts.app')

@section('content')
    <h1 class="text-3xl font-bold text-[#4D2E38] mb-8">Your Cart</h1>

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if ($items->count() > 0)
        <form method="GET" action="{{ route('checkout.index') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <input type="hidden" name="selection_mode" value="1">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="space-y-4">
                    <div class="bg-[#FFFFFF] rounded-2xl border border-[#ECD8E0] p-4 flex items-center justify-between">
                        <label class="inline-flex items-center gap-2 text-sm font-semibold text-[#4D2E38]">
                            <input type="checkbox" id="select-all-cart-items" class="h-4 w-4 rounded border-[#C88A92] text-[#C47A90] focus:ring-[#C47A90]" checked>
                            Select all items
                        </label>
                    </div>
                    @foreach ($items as $item)
                        @php
                            $itemName = $item->product?->name ?? (($item->customization_payload['item_name'] ?? null) ?: 'Custom Cake');
                            $itemVariantLabel = $item->variant?->name ?? (($item->product || $item->variant) ? 'N/A' : 'Custom Design');
                        @endphp
                        <div class="bg-[#FFFFFF] rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-[#ECD8E0] p-6 flex items-center space-x-4">
                            <input
                                type="checkbox"
                                name="selected_item_ids[]"
                                value="{{ $item->id }}"
                                class="cart-item-checkbox h-5 w-5 rounded border-[#C88A92] text-[#C47A90] focus:ring-[#C47A90]"
                                checked
                            >
                            @if (!empty($item->customization_payload['preview_svg']))
                                <div class="h-20 w-20 overflow-hidden rounded bg-white [&_svg]:h-full [&_svg]:w-full">
                                    {!! $item->customization_payload['preview_svg'] !!}
                                </div>
                            @elseif ($item->product?->main_image_url)
                                <img src="{{ $item->product->main_image_url }}" alt="{{ $itemName }}" class="w-20 h-20 rounded-2xl object-cover ring-4 ring-[#FBEAF1]/50">
                            @else
                                <x-custom-cake-thumbnail :payload="$item->customization_payload" width="80" height="80" class="h-20 w-20 rounded object-cover" />
                            @endif
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-[#4D2E38]">{{ $itemName }}</h3>
                                <p class="text-[#8A6A76]">{{ $itemVariantLabel }}</p>
                                @if (is_array($item->customization_payload) && count($item->customization_payload) > 0)
                                    <p class="mt-1 text-xs text-[#8F6172]">
                                        @foreach($item->customization_payload as $key => $value)
                                            @continue(in_array($key, ['preview_image', 'preview_svg'], true))
                                            <span class="mr-2">{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}</span>
                                        @endforeach
                                    </p>
                                @endif
                                <p class="text-[#C47A90] font-bold">&#8369;{{ number_format($item->unit_price, 2) }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <form method="POST" action="{{ route('cart.decrement', $item) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-[#FBEAF1] hover:bg-[#E9C7D4] px-2 py-1 rounded">-</button>
                                </form>
                                <span class="text-lg font-semibold">{{ $item->quantity }}</span>
                                <form method="POST" action="{{ route('cart.increment', $item) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-[#FBEAF1] hover:bg-[#E9C7D4] px-2 py-1 rounded">+</button>
                                </form>
                            </div>
                            <form method="POST" action="{{ route('cart.remove', $item) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-[#FFFFFF] rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-[#ECD8E0] p-6 h-fit">
                <h2 class="text-xl font-bold text-[#4D2E38] mb-4">Order Summary</h2>
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span>&#8369;{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Delivery</span>
                        <span>&#8369;{{ number_format($delivery, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tax ({{ number_format((float) ($taxRate ?? 10), 2) }}%)</span>
                        <span>&#8369;{{ number_format($tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Service Fee</span>
                        <span>&#8369;{{ number_format((float) ($serviceFee ?? 0), 2) }}</span>
                    </div>
                </div>
                <hr class="my-4 border-[#ECD8E0]">
                <div class="flex justify-between text-lg font-bold mb-6">
                    <span>Total</span>
                    <span>&#8369;{{ number_format($total, 2) }}</span>
                </div>
                <button type="submit" class="w-full bg-[#C47A90] hover:bg-[#B66880] text-white font-bold py-3 px-6 rounded-lg text-center block transition duration-300">
                    Proceed to Checkout
                </button>
                <a href="/#shop" class="w-full bg-[#FBEAF1] hover:bg-[#E9C7D4] text-[#4D2E38] font-bold py-3 px-6 rounded-lg text-center block mt-4 transition duration-300">
                    Continue Shopping
                </a>
            </div>
        </form>

        <script>
            (() => {
                const selectAll = document.getElementById('select-all-cart-items');
                const itemChecks = Array.from(document.querySelectorAll('.cart-item-checkbox'));
                if (!selectAll || itemChecks.length === 0) return;

                const syncSelectAll = () => {
                    selectAll.checked = itemChecks.every((checkbox) => checkbox.checked);
                };

                selectAll.addEventListener('change', () => {
                    itemChecks.forEach((checkbox) => {
                        checkbox.checked = selectAll.checked;
                    });
                });

                itemChecks.forEach((checkbox) => {
                    checkbox.addEventListener('change', syncSelectAll);
                });
            })();
        </script>
    @else
        <!-- Empty Cart -->
        <div class="text-center py-12">
            <svg class="w-24 h-24 text-[#8F6172] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.1 5H19M7 13v8a2 2 0 002 2h10a2 2 0 002-2v-3"></path>
            </svg>
            <h2 class="text-2xl font-bold text-[#4D2E38] mb-4">Your cart is empty</h2>
            <p class="text-[#8F6172] mb-8">Looks like you haven't added any cakes to your cart yet.</p>
            <a href="/#shop" class="bg-[#C47A90] hover:bg-[#B66880] text-white font-bold py-3 px-6 rounded-lg inline-block transition duration-300">
                Start Shopping
            </a>
        </div>
    @endif
@endsection
