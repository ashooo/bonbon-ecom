@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto">
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-3xl font-bold">Your Orders</h1>
            @if ($isGuestView)
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Guest View</span>
            @endif
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($isGuestView)
            <section class="mb-8 rounded-lg bg-white p-6 shadow-md">
                <h2 class="mb-2 text-xl font-bold">Find an Order</h2>
                <p class="mb-4 text-sm text-gray-600">Enter your order number and checkout email to add it to this device.</p>

                <form method="POST" action="{{ route('orders.lookup') }}" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    @csrf
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Order Number</label>
                        <input
                            type="text"
                            name="order_number"
                            value="{{ old('order_number') }}"
                            placeholder="ORD-XXXXXXXXXX"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500"
                            required
                        >
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Email</label>
                        <input
                            type="email"
                            name="customer_email"
                            value="{{ old('customer_email') }}"
                            placeholder="you@email.com"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500"
                            required
                        >
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full rounded-md bg-pink-600 px-4 py-2 font-semibold text-white transition hover:bg-pink-700">
                            Find Order
                        </button>
                    </div>
                </form>
            </section>
        @endif

        <section class="rounded-lg bg-white p-6 shadow-md">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold">Order History</h2>
                <span class="text-sm text-gray-500">{{ $orders->count() }} order(s)</span>
            </div>

            @forelse ($orders as $order)
                <div class="mb-4 rounded-lg border border-gray-200 p-4 last:mb-0">
                    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="font-semibold">{{ $order->order_number }}</h3>
                            <p class="text-sm text-gray-600">Placed on {{ $order->created_at?->format('F j, Y h:i A') }}</p>
                            <p class="text-sm text-gray-600">{{ ucfirst((string) $order->order_type) }} • {{ ucfirst((string) $order->status) }}</p>
                        </div>
                        <div class="text-left md:text-right">
                            <p class="text-sm text-gray-500">Total</p>
                            <p class="text-lg font-bold">&#8369;{{ number_format($order->total, 2) }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-6 text-gray-700">
                    No orders found yet.
                </div>
            @endforelse
        </section>
    </div>
@endsection
