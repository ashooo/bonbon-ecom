@extends('layouts.app')

@section('content')
    <h1 class="text-3xl font-bold mb-8">Your Cart</h1>

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if ($items->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="space-y-4">
                    @foreach ($items as $item)
                        <div class="bg-white rounded-lg shadow-md p-6 flex items-center space-x-4">
                            <img src="{{ $item->product?->main_image_url ?? 'https://via.placeholder.com/100x100?text=Product' }}" alt="{{ $item->product?->name ?? 'Product' }}" class="w-20 h-20 rounded object-cover">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold">{{ $item->product?->name ?? 'Unavailable product' }}</h3>
                                @if ($item->variant?->name)
                                    <p class="text-gray-600">{{ $item->variant->name }}</p>
                                @endif
                                @if (is_array($item->customization_payload) && count($item->customization_payload) > 0)
                                    <p class="mt-1 text-xs text-gray-500">
                                        @foreach($item->customization_payload as $key => $value)
                                            <span class="mr-2">{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}</span>
                                        @endforeach
                                    </p>
                                @endif
                                <p class="text-pink-600 font-bold">&#8369;{{ number_format($item->unit_price, 2) }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <form method="POST" action="{{ route('cart.decrement', $item) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded">-</button>
                                </form>
                                <span class="text-lg font-semibold">{{ $item->quantity }}</span>
                                <form method="POST" action="{{ route('cart.increment', $item) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded">+</button>
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
            <div class="bg-white rounded-lg shadow-md p-6 h-fit">
                <h2 class="text-xl font-bold mb-4">Order Summary</h2>
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
                        <span>Tax (10%)</span>
                        <span>&#8369;{{ number_format($tax, 2) }}</span>
                    </div>
                </div>
                <hr class="my-4">
                <div class="flex justify-between text-lg font-bold mb-6">
                    <span>Total</span>
                    <span>&#8369;{{ number_format($total, 2) }}</span>
                </div>
                <a href="/checkout" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-bold py-3 px-6 rounded-lg text-center block transition duration-300">
                    Proceed to Checkout
                </a>
                <a href="/products" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded-lg text-center block mt-4 transition duration-300">
                    Continue Shopping
                </a>
            </div>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="text-center py-12">
            <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.1 5H19M7 13v8a2 2 0 002 2h10a2 2 0 002-2v-3"></path>
            </svg>
            <h2 class="text-2xl font-bold text-gray-600 mb-4">Your cart is empty</h2>
            <p class="text-gray-500 mb-8">Looks like you haven't added any cakes to your cart yet.</p>
            <a href="/products" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-3 px-6 rounded-lg inline-block transition duration-300">
                Start Shopping
            </a>
        </div>
    @endif
@endsection
