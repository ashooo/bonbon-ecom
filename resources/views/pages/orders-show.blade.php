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

        $statusTimeline = [
            ['key' => 'pending', 'label' => 'Order Placed', 'icon' => 'clipboard-document-list'],
            ['key' => 'confirmed', 'label' => 'Order Confirmed', 'icon' => 'check-badge'],
            ['key' => 'ready', 'label' => $order->order_type === 'delivery' ? 'Out for Delivery' : 'Ready for Pickup', 'icon' => $order->order_type === 'delivery' ? 'truck' : 'archive-box'],
            ['key' => 'completed', 'label' => $order->order_type === 'delivery' ? 'Delivered' : 'Picked Up', 'icon' => $order->order_type === 'delivery' ? 'home' : 'sparkles'],
        ];

        $statusOrder = collect($statusTimeline)->pluck('key')->values()->all();
        $statusPosition = array_search($status, $statusOrder, true);
        $statusPosition = $statusPosition === false ? -1 : $statusPosition;

        $historyByStatus = $order->statusHistory
            ->groupBy('to_status')
            ->map(fn ($group) => $group->first());
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

        <div class="rounded-3xl bg-white p-6 shadow-md">
            <h2 class="mb-4 text-xl font-semibold">Order Timeline</h2>

            @if ($status === 'cancelled')
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-semibold">Order Cancelled</p>
                    <p class="mt-1">This order was cancelled{{ $order->updated_at ? ' on ' . $order->updated_at->format('M d, Y h:i A') : '' }}.</p>
                </div>
            @endif

            <div class="overflow-x-auto pb-2">
                <div class="min-w-[680px]">
                    <div class="mb-8 flex items-center">
                        @foreach ($statusTimeline as $index => $step)
                            @php
                                $isDone = $status !== 'cancelled' && $statusPosition >= $index;
                                $isCurrent = $status === $step['key'];
                            @endphp

                            <div class="relative">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 text-sm font-semibold {{ $isDone ? 'border-pink-500 bg-pink-500 text-white' : 'border-pink-200 bg-white text-pink-300' }}">
                                    {{ $isDone ? '✓' : $index + 1 }}
                                </div>
                                @if ($isCurrent)
                                    <span class="absolute -bottom-5 left-1/2 -translate-x-1/2 whitespace-nowrap text-[11px] font-semibold uppercase tracking-wide text-pink-700">Current</span>
                                @endif
                            </div>

                            @if (! $loop->last)
                                <div class="h-1 flex-1 {{ $status !== 'cancelled' && $statusPosition > $index ? 'bg-pink-500' : 'bg-pink-200' }}"></div>
                            @endif
                        @endforeach
                    </div>

                    <div class="grid grid-cols-4 gap-3">
                        @foreach ($statusTimeline as $index => $step)
                            @php
                                $isDone = $status !== 'cancelled' && $statusPosition >= $index;
                                $timestamp = $historyByStatus[$step['key']]->changed_at ?? null;
                            @endphp
                            <div class="rounded-2xl border p-3 {{ $isDone ? 'border-pink-200 bg-pink-50' : 'border-slate-200 bg-slate-50' }}">
                                <p class="text-pink-600">
                                    @if ($step['icon'] === 'clipboard-document-list')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M8.25 4.5a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 .75.75V6h1.5A2.25 2.25 0 0 1 19.5 8.25v9A2.25 2.25 0 0 1 17.25 19.5h-10.5A2.25 2.25 0 0 1 4.5 17.25v-9A2.25 2.25 0 0 1 6.75 6h1.5V4.5Zm1.5 0V6h4.5V4.5h-4.5Zm-1.5 5.25a.75.75 0 0 0 0 1.5h7.5a.75.75 0 0 0 0-1.5h-7.5Zm0 3a.75.75 0 0 0 0 1.5h4.5a.75.75 0 0 0 0-1.5h-4.5Z"/></svg>
                                    @elseif ($step['icon'] === 'check-badge')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M2.25 12a9.75 9.75 0 1 1 16.417 7.083l1.875 1.875a.75.75 0 1 1-1.06 1.06l-1.875-1.874A9.75 9.75 0 0 1 2.25 12Zm13.28-2.03a.75.75 0 0 0-1.06-1.06l-3.22 3.22-1.72-1.72a.75.75 0 1 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.06 0l3.75-3.75Z" clip-rule="evenodd"/></svg>
                                    @elseif ($step['icon'] === 'truck')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M3.75 4.5A2.25 2.25 0 0 0 1.5 6.75v8.25A2.25 2.25 0 0 0 3.75 17.25H4.5a2.25 2.25 0 1 0 4.5 0h6a2.25 2.25 0 1 0 4.5 0h.75A2.25 2.25 0 0 0 22.5 15V12a.75.75 0 0 0-.22-.53l-2.25-2.25A.75.75 0 0 0 19.5 9h-2.25V6.75A2.25 2.25 0 0 0 15 4.5H3.75Z"/></svg>
                                    @elseif ($step['icon'] === 'archive-box')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M3.75 3.75A2.25 2.25 0 0 0 1.5 6v1.5c0 .414.336.75.75.75h19.5a.75.75 0 0 0 .75-.75V6a2.25 2.25 0 0 0-2.25-2.25H3.75Zm-1.5 6a.75.75 0 0 0-.75.75v7.5A2.25 2.25 0 0 0 3.75 20.25h16.5A2.25 2.25 0 0 0 22.5 18v-7.5a.75.75 0 0 0-.75-.75H2.25Zm6 3a.75.75 0 0 0 0 1.5h7.5a.75.75 0 0 0 0-1.5h-7.5Z" clip-rule="evenodd"/></svg>
                                    @elseif ($step['icon'] === 'home')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12.75 3.75a1.125 1.125 0 0 0-1.5 0l-8.25 7.5a1.125 1.125 0 0 0 .75 1.875h.75v6A2.25 2.25 0 0 0 6.75 21h10.5a2.25 2.25 0 0 0 2.25-2.25v-6h.75a1.125 1.125 0 0 0 .75-1.875l-8.25-7.5Z"/></svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M9.813 15.904 9 18.75l-1.313-2.063a9 9 0 1 1 9.968-2.266l2.063 1.313-2.846.813-.813 2.846-1.313-2.063a9.03 9.03 0 0 1-4.933-1.426Z"/></svg>
                                    @endif
                                </p>
                                <p class="mt-1 text-sm font-semibold text-slate-800">{{ $step['label'] }}</p>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $timestamp ? $timestamp->format('M d, Y h:i A') : ($isDone ? 'Completed' : 'Waiting') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
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
                                @php
                                    $itemName = $item->variant?->product?->name ?? (($item->customization_payload['item_name'] ?? null) ?: 'Custom Cake');
                                    $itemVariantLabel = $item->variant?->name ?? (($item->variant_id ? 'N/A' : 'Custom Design'));
                                @endphp
                                <tr>
                                    <td class="px-3 py-3 text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            @if (!empty($item->customization_payload['preview_svg']))
                                                <div class="h-10 w-10 overflow-hidden rounded-md bg-white [&_svg]:h-full [&_svg]:w-full">
                                                    {!! $item->customization_payload['preview_svg'] !!}
                                                </div>
                                            @elseif ($item->variant?->product?->main_image_url)
                                                <img src="{{ $item->variant->product->main_image_url }}" alt="{{ $itemName }}" class="h-10 w-10 rounded-md object-cover">
                                            @else
                                                <x-custom-cake-thumbnail :payload="$item->customization_payload" width="40" height="40" class="h-10 w-10 rounded-md object-cover" />
                                            @endif
                                            <span>{{ $itemName }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-sm">{{ $itemVariantLabel }}</td>
                                    <td class="px-3 py-3 text-sm">{{ $item->quantity }}</td>
                                    <td class="px-3 py-3 text-sm">&#8369;{{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="px-3 py-3 text-sm font-semibold">&#8369;{{ number_format((float) $item->subtotal, 2) }}</td>
                                </tr>
                                @if (is_array($item->customization_payload) && count($item->customization_payload) > 0)
                                    <tr>
                                        <td colspan="5" class="px-3 pb-3 text-xs text-gray-500">
                                            @foreach($item->customization_payload as $key => $value)
                                                @continue(in_array($key, ['preview_image', 'preview_svg'], true))
                                                <span class="mr-2">{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endif
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
