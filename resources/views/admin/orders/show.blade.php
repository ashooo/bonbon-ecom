@extends('layouts.admin')

@section('content')
    @php
        $status = $order->status ?? 'pending';
        $statusClass = match ($status) {
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'ready' => 'bg-indigo-100 text-indigo-800',
            default => 'bg-yellow-100 text-yellow-800',
        };
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm text-slate-500">Order Details</p>
                <h1 class="text-3xl font-bold">{{ $order->order_number }}</h1>
            </div>
            <a
                href="{{ route('admin.dashboard', $backQuery) }}"
                class="inline-flex items-center rounded-2xl bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800"
            >
                Back to Orders
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 p-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="rounded-3xl bg-white p-6 shadow-soft xl:col-span-2">
                <h2 class="mb-4 text-xl font-semibold">Items</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-slate-200">
                            <tr>
                                <th class="px-3 py-2 text-left text-sm font-medium text-slate-500">Product</th>
                                <th class="px-3 py-2 text-left text-sm font-medium text-slate-500">Variant</th>
                                <th class="px-3 py-2 text-left text-sm font-medium text-slate-500">Qty</th>
                                <th class="px-3 py-2 text-left text-sm font-medium text-slate-500">Unit</th>
                                <th class="px-3 py-2 text-left text-sm font-medium text-slate-500">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($order->items as $item)
                                <tr>
                                    <td class="px-3 py-3 text-sm font-medium">{{ $item->variant?->product?->name ?? 'Unknown Product' }}</td>
                                    <td class="px-3 py-3 text-sm">{{ $item->variant?->name ?? 'N/A' }}</td>
                                    <td class="px-3 py-3 text-sm">{{ $item->quantity }}</td>
                                    <td class="px-3 py-3 text-sm">&#8369;{{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="px-3 py-3 text-sm font-semibold">&#8369;{{ number_format((float) $item->subtotal, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-500">No items found for this order.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl bg-white p-6 shadow-soft">
                    <h2 class="mb-4 text-xl font-semibold">Order Info</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between gap-4"><span class="text-slate-500">Status</span><span class="rounded-full px-2 py-1 text-xs font-semibold {{ $statusClass }}">{{ ucfirst($status) }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-slate-500">Type</span><span>{{ ucfirst((string) $order->order_type) }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-slate-500">Placed</span><span>{{ $order->created_at?->format('M d, Y h:i A') }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-slate-500">Fulfillment</span><span>{{ $order->fulfillment_date?->format('M d, Y') }} {{ $order->fulfillment_time ? \Illuminate\Support\Str::of($order->fulfillment_time)->substr(0, 5) : '' }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-slate-500">Subtotal</span><span>&#8369;{{ number_format((float) $order->subtotal, 2) }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-slate-500">Delivery Fee</span><span>&#8369;{{ number_format((float) $order->delivery_fee, 2) }}</span></div>
                        <div class="flex justify-between gap-4 border-t border-slate-200 pt-3 text-base font-semibold"><span>Total</span><span>&#8369;{{ number_format((float) $order->total, 2) }}</span></div>
                    </div>
                </div>

                <div class="rounded-3xl bg-white p-6 shadow-soft">
                    <h2 class="mb-4 text-xl font-semibold">Customer</h2>
                    <div class="space-y-2 text-sm">
                        <p class="font-medium">{{ $order->customer_name }}</p>
                        <p>{{ $order->customer_email }}</p>
                        <p>{{ $order->customer_phone }}</p>
                        @if ($order->delivery_address)
                            <div class="mt-3 rounded-2xl bg-slate-50 p-3">
                                <p class="text-xs uppercase tracking-wide text-slate-500">Delivery Address</p>
                                <p class="mt-1">{{ $order->delivery_address }}</p>
                            </div>
                        @endif
                        @if ($order->special_instructions)
                            <div class="mt-3 rounded-2xl bg-slate-50 p-3">
                                <p class="text-xs uppercase tracking-wide text-slate-500">Special Instructions</p>
                                <p class="mt-1">{{ $order->special_instructions }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="rounded-3xl bg-white p-6 shadow-soft">
                    <h2 class="mb-4 text-xl font-semibold">Update Status</h2>
                    <form method="POST" action="{{ route('admin.orders.status.update', $order) }}" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="redirect_status" value="{{ request('status', 'all') }}">
                        <input type="hidden" name="redirect_search" value="{{ request('search', '') }}">
                        <input type="hidden" name="redirect_page" value="{{ request('page', 1) }}">
                        <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                            @foreach (['pending', 'confirmed', 'ready', 'completed', 'cancelled'] as $optionStatus)
                                <option value="{{ $optionStatus }}" @selected($status === $optionStatus)>{{ ucfirst($optionStatus) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="w-full rounded-xl bg-slate-700 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            Save Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
