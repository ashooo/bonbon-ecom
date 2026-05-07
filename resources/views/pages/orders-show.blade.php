@extends('layouts.app')

@section('content')
    @php
        $status = strtolower((string) $order->status);
        $statusClass = match ($status) {
            'cancelled' => 'bg-red-100 text-red-800',
            'completed' => 'bg-green-100 text-green-800',
            'ready' => 'bg-indigo-100 text-indigo-800',
            'confirmed', 'preparing' => 'bg-blue-100 text-blue-800',
            default => 'bg-yellow-100 text-yellow-800',
        };
    @endphp

    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm text-gray-600">Order Details</p>
                <h1 class="text-3xl font-bold">{{ $order->order_number }}</h1>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('orders.receipt', $order) }}" class="inline-flex items-center rounded-2xl bg-pink-600 px-4 py-2 text-sm font-semibold text-white hover:bg-pink-700">View Receipt</a>
                <a href="{{ route('orders.index') }}" class="inline-flex items-center rounded-2xl bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Back to Orders</a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="rounded-3xl bg-white p-6 shadow-md xl:col-span-2">
                <h2 class="mb-4 text-xl font-semibold">Items</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gray-200">
                            <tr>
                                <th class="px-3 py-2 text-left text-sm font-medium text-gray-500">Product</th>
                                <th class="px-3 py-2 text-left text-sm font-medium text-gray-500">Variant</th>
                                <th class="px-3 py-2 text-left text-sm font-medium text-gray-500">Qty</th>
                                <th class="px-3 py-2 text-left text-sm font-medium text-gray-500">Unit</th>
                                <th class="px-3 py-2 text-left text-sm font-medium text-gray-500">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($order->items as $item)
                                <tr>
                                    <td class="px-3 py-3 text-sm font-medium">{{ $item->variant?->product?->name ?? 'Unknown Product' }}</td>
                                    <td class="px-3 py-3 text-sm">{{ $item->variant?->name ?? 'N/A' }}</td>
                                    <td class="px-3 py-3 text-sm">{{ $item->quantity }}</td>
                                    <td class="px-3 py-3 text-sm">&#8369;{{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="px-3 py-3 text-sm font-semibold">&#8369;{{ number_format((float) $item->subtotal, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-3 py-6 text-center text-sm text-gray-500">No items found for this order.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl bg-white p-6 shadow-md">
                    <h2 class="mb-4 text-xl font-semibold">Order Info</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between gap-4"><span class="text-gray-500">Status</span><span class="rounded-full px-2 py-1 text-xs font-semibold {{ $statusClass }}">{{ ucfirst((string) $order->status) }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-gray-500">Type</span><span>{{ ucfirst((string) $order->order_type) }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-gray-500">Placed</span><span>{{ $order->created_at?->format('M d, Y h:i A') }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-gray-500">Fulfillment</span><span>{{ $order->fulfillment_date?->format('M d, Y') }} {{ $order->fulfillment_time ? \Illuminate\Support\Str::of($order->fulfillment_time)->substr(0, 5) : '' }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-gray-500">Subtotal</span><span>&#8369;{{ number_format((float) $order->subtotal, 2) }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-gray-500">Delivery Fee</span><span>&#8369;{{ number_format((float) $order->delivery_fee, 2) }}</span></div>
                        <div class="flex justify-between gap-4 border-t border-gray-200 pt-3 text-base font-semibold"><span>Total</span><span>&#8369;{{ number_format((float) $order->total, 2) }}</span></div>
                    </div>
                </div>

                @if (! $isGuestView && $order->invoice)
                    <div class="rounded-3xl bg-white p-6 shadow-md">
                        <h2 class="mb-4 text-xl font-semibold">Invoice</h2>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Available</span><span>Ready</span></div>
                            <div class="flex justify-between gap-4"><span class="text-gray-500">Last Downloaded</span><span>{{ $order->invoice->last_printed_at?->format('M d, Y h:i A') ?? 'Not yet' }}</span></div>
                            <a href="{{ route('api.invoices.download', $order->invoice) }}" class="block rounded-xl bg-pink-600 px-3 py-2 text-center text-sm font-semibold text-white hover:bg-pink-700">
                                Download Invoice
                            </a>
                        </div>
                    </div>
                @endif

                @php
                    $displayDeliveryAddress = null;

                    if ((string) $order->order_type === 'pickup') {
                        $displayDeliveryAddress = 'BonBons PH';
                    } elseif ($order->delivery_address) {
                        $displayDeliveryAddress = trim((string) $order->delivery_address);

                        if (filter_var($displayDeliveryAddress, FILTER_VALIDATE_URL)) {
                            $path = urldecode((string) parse_url($displayDeliveryAddress, PHP_URL_PATH));
                            $displayDeliveryAddress = str_replace('/maps/place/', '', trim($path, '/'));
                            $displayDeliveryAddress = str_replace('+', ' ', $displayDeliveryAddress);
                        }
                    }
                @endphp

                <div class="rounded-3xl bg-white p-6 shadow-md">
                    <h2 class="mb-4 text-xl font-semibold">Customer</h2>
                    <div class="space-y-2 text-sm">
                        <p class="font-medium">{{ $order->customer_name }}</p>
                        <p>{{ $order->customer_email }}</p>
                        <p>{{ $order->customer_phone }}</p>
                        @if ($displayDeliveryAddress)
                            <div class="mt-3 rounded-2xl bg-slate-50 p-3">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Delivery Address</p>
                                <p class="mt-1">{{ $displayDeliveryAddress }}</p>
                            </div>
                        @endif
                        @if ($order->special_instructions)
                            <div class="mt-3 rounded-2xl bg-slate-50 p-3">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Special Instructions</p>
                                <p class="mt-1">{{ $order->special_instructions }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
