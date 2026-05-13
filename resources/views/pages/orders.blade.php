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
                        <input type="text" name="order_number" value="{{ old('order_number') }}" placeholder="ORD-XXXXXXXXXX" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email') }}" placeholder="you@email.com" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full rounded-md bg-pink-600 px-4 py-2 font-semibold text-white transition hover:bg-pink-700">Find Order</button>
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
                @php
                    $status = strtolower((string) $order->status);
                    $paymentMethodLabel = (string) $order->payment_method === 'paymongo' ? 'QRPH' : strtoupper((string) $order->payment_method);
                    $statusClass = match ($status) {
                        'cancelled' => 'bg-red-100 text-red-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'ready' => 'bg-indigo-100 text-indigo-800',
                        'confirmed', 'preparing' => 'bg-blue-100 text-blue-800',
                        default => 'bg-yellow-100 text-yellow-800',
                    };

                    $cancellationMessage = match ($status) {
                        'cancelled' => 'This order is already cancelled.',
                        'ready' => 'This order is already ready and can no longer be cancelled.',
                        'preparing' => 'This order is already preparing and can no longer be cancelled.',
                        'completed' => 'This order is already completed and can no longer be cancelled.',
                        default => null,
                    };
                @endphp

                <div class="mb-4 rounded-lg border border-gray-200 p-4 last:mb-0">
                    <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                        <div>
                            <h3 class="font-semibold">{{ $order->order_number }}</h3>
                            <p class="text-sm text-gray-600">Placed on {{ $order->created_at?->format('F j, Y h:i A') }}</p>
                            <p class="mt-1 text-sm text-gray-600">{{ ucfirst((string) $order->order_type) }}</p>
                            <p class="text-xs text-gray-500">Payment: {{ $paymentMethodLabel }} ({{ ucfirst((string) $order->payment_status) }})</p>
                            <span class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ ucfirst((string) $order->status) }}</span>
                        </div>

                        <div class="space-y-2 text-left md:text-right">
                            <p class="text-sm text-gray-500">Total</p>
                            <p class="text-lg font-bold">&#8369;{{ number_format($order->total, 2) }}</p>

                            <a href="{{ route('orders.show', $order) }}" class="inline-block rounded-md bg-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-300">
                                View Details
                            </a>
                            <a href="{{ route('orders.receipt', $order) }}" class="inline-block rounded-md bg-pink-100 px-3 py-2 text-sm font-semibold text-pink-700 hover:bg-pink-200">
                                Receipt
                            </a>


                            @if (in_array($status, ['pending', 'confirmed'], true))
                                <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Cancel this order?');" class="space-y-2">
                                    @csrf
                                    @if ($isGuestView)
                                        <input type="email" name="customer_email" placeholder="Confirm checkout email" class="w-full rounded-md border border-gray-300 px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-pink-500 md:w-56" required>
                                    @endif
                                    <button type="submit" class="w-full rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700 md:w-auto">Cancel Order</button>
                                </form>
                            @elseif ($cancellationMessage)
                                <p class="text-xs text-gray-500">{{ $cancellationMessage }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-6 text-gray-700">No orders found yet.</div>
            @endforelse
        </section>
    </div>
@endsection
