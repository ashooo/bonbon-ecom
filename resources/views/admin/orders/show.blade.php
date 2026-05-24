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
        $nextStatus = match ($status) {
            'pending' => 'confirmed',
            'confirmed' => 'ready',
            'ready' => 'completed',
            default => null,
        };
        $nextStatusLabel = $nextStatus ? ('Mark as ' . ucfirst($nextStatus)) : null;
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm text-slate-500">Order Details</p>
                <h1 class="text-3xl font-bold">{{ $order->order_number }}</h1>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($order->invoice)
                    <button
                        type="button"
                        class="js-admin-print-invoice inline-flex items-center rounded-2xl bg-pink-600 px-4 py-2 text-sm font-semibold text-white hover:bg-pink-700"
                        data-print-url="{{ route('admin.invoices.print', $order->invoice) }}"
                        data-track-url="{{ route('admin.invoices.track-print', $order->invoice) }}"
                    >
                        Print Invoice
                    </button>
                    <a
                        href="{{ route('admin.invoices.download', $order->invoice) }}"
                        class="inline-flex items-center rounded-2xl bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-300"
                    >
                        Download Invoice
                    </a>
                @endif
                <a
                    href="{{ route('admin.dashboard', $backQuery) }}"
                    class="inline-flex items-center rounded-2xl border border-[#D6B7C3] bg-white px-4 py-2 text-sm font-semibold text-[#6B4957] hover:bg-[#FAF1F5]"
                >
                    Back to Orders
                </a>
            </div>
        </div>

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
                                            @elseif (!empty($item->customization_payload['preview_image']))
                                                <img src="{{ $item->customization_payload['preview_image'] }}" alt="{{ $itemName }}" class="h-10 w-10 rounded-md object-cover bg-white">
                                            @elseif ($item->variant?->product?->main_image_url)
                                                <img src="{{ $item->variant->product->main_image_url }}" alt="{{ $itemName }}" class="h-10 w-10 rounded-md object-cover">
                                            @else
                                                <x-custom-cake-thumbnail :payload="$item->customization_payload" width="40" height="40" class="h-10 w-10 rounded-md object-cover" />
                                            @endif
                                            <span>{{ $itemName }}</span>
                                            <span class="inline-flex rounded-full bg-[#F6EAF0] px-2 py-0.5 text-[11px] font-semibold text-[#8A6070]">{{ $itemVariantLabel }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-sm text-[#6B4A57]">{{ $itemVariantLabel }}</td>
                                    <td class="px-3 py-3 text-sm">{{ $item->quantity }}</td>
                                    <td class="px-3 py-3 text-sm">&#8369;{{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="px-3 py-3 text-sm font-semibold">&#8369;{{ number_format((float) $item->subtotal, 2) }}</td>
                                </tr>
                                @if (is_array($item->customization_payload) && count($item->customization_payload) > 0)
                                    @php
                                        $payloadSummary = [];
                                        foreach ($item->customization_payload as $key => $value) {
                                            if (in_array($key, ['preview_image', 'preview_svg', 'item_name'], true)) {
                                                continue;
                                            }
                                            if ($key === 'toppings') {
                                                $decoded = is_string($value) ? json_decode($value, true) : $value;
                                                $count = is_array($decoded) ? count($decoded) : 0;
                                                if ($count > 0) {
                                                    $payloadSummary[] = 'Toppings: ' . $count . ' pcs';
                                                }
                                                continue;
                                            }
                                            if (is_array($value) || is_object($value)) {
                                                continue;
                                            }
                                            $text = trim((string) $value);
                                            if ($text === '') {
                                                continue;
                                            }
                                            $payloadSummary[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . $text;
                                        }
                                    @endphp
                                    <tr>
                                        <td colspan="5" class="px-3 pb-3 text-xs text-slate-500">
                                            @foreach($payloadSummary as $entry)
                                                <span class="mr-2">{{ $entry }}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-500">No items found for this order.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-800">Order timeline</h3>
                        @if ($order->invoice)
                            <div class="flex items-center gap-2">
                                <button type="button" class="js-admin-print-invoice rounded-lg bg-pink-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-pink-700" data-print-url="{{ route('admin.invoices.print', $order->invoice) }}" data-track-url="{{ route('admin.invoices.track-print', $order->invoice) }}">Print</button>
                                <a href="{{ route('admin.invoices.download', $order->invoice) }}" class="rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Download</a>
                            </div>
                        @endif
                    </div>
                    <div class="mt-3 space-y-2 text-xs text-slate-600">
                        <div class="flex items-center justify-between rounded-lg bg-white px-3 py-2"><span>Placed</span><span>{{ $order->created_at?->format('M d, g:i A') }}</span></div>
                        <div class="flex items-center justify-between rounded-lg bg-white px-3 py-2"><span>Current status</span><span class="font-semibold">{{ ucfirst($status) }}</span></div>
                        <div class="flex items-center justify-between rounded-lg bg-white px-3 py-2"><span>Fulfillment target</span><span>{{ $order->fulfillment_date?->format('M d, Y') ?? 'N/A' }} {{ $order->fulfillment_time ? \Carbon\Carbon::createFromFormat('H:i:s', $order->fulfillment_time)->format('g:i A') : '' }}</span></div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl bg-white p-6 shadow-soft">
                    <h2 class="mb-4 text-xl font-semibold">Update Status</h2>
                    @if($nextStatus)
                        <form method="POST" action="{{ route('admin.orders.status.update', $order) }}" class="js-status-next-form">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $nextStatus }}">
                            <input type="hidden" name="redirect_status" value="{{ request('status', 'all') }}">
                            <input type="hidden" name="redirect_search" value="{{ request('search', '') }}">
                            <input type="hidden" name="redirect_page" value="{{ request('page', 1) }}">
                            <button type="submit" class="w-full rounded-xl bg-pink-600 px-3 py-2 text-sm font-semibold text-white hover:bg-pink-700">{{ $nextStatusLabel }}</button>
                        </form>
                    @else
                        <p class="rounded-xl bg-slate-50 px-3 py-2 text-sm text-slate-600">No further forward action available for this status.</p>
                    @endif

                    <div class="mt-4 border-t border-slate-200 pt-4">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Other status actions</p>
                        <form method="POST" action="{{ route('admin.orders.status.update', $order) }}" class="js-status-other-form grid grid-cols-[1fr_auto] gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="redirect_status" value="{{ request('status', 'all') }}">
                            <input type="hidden" name="redirect_search" value="{{ request('search', '') }}">
                            <input type="hidden" name="redirect_page" value="{{ request('page', 1) }}">
                            <select name="status" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                                @foreach (['pending', 'confirmed', 'ready', 'completed', 'cancelled'] as $optionStatus)
                                    @if($optionStatus !== $status)
                                        <option value="{{ $optionStatus }}">Set {{ ucfirst($optionStatus) }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <button type="submit" class="rounded-xl border border-[#D6B7C3] bg-white px-3 py-2 text-sm font-semibold text-[#6B4957] hover:bg-[#FAF1F5]">Apply</button>
                        </form>
                    </div>
                </div>

                <div class="rounded-3xl bg-white p-6 shadow-soft">
                    <h2 class="mb-4 text-xl font-semibold">Order Info</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between gap-4"><span class="text-slate-500">Status</span><span class="rounded-full px-2 py-1 text-xs font-semibold {{ $statusClass }}">{{ ucfirst($status) }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-slate-500">Type</span><span>{{ ucfirst((string) $order->order_type) }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-slate-500">Payment Method</span><span>{{ (string) $order->payment_method === 'paymongo' ? 'QRPH' : strtoupper((string) $order->payment_method) }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-slate-500">Payment Status</span><span>{{ ucfirst((string) $order->payment_status) }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-slate-500">Placed</span><span>{{ $order->created_at?->format('M d, Y h:i A') }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-slate-500">Fulfillment</span><span>{{ $order->fulfillment_date?->format('M d, Y') }} {{ $order->fulfillment_time ? \Illuminate\Support\Str::of($order->fulfillment_time)->substr(0, 5) : '' }}</span></div>
                        <div class="border-t border-slate-200 pt-3"></div>
                        <div class="flex justify-between gap-4"><span class="text-slate-500">Subtotal</span><span>&#8369;{{ number_format((float) $order->subtotal, 2) }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-slate-500">Delivery Fee</span><span>&#8369;{{ number_format((float) $order->delivery_fee, 2) }}</span></div>
                        <div class="flex justify-between gap-4 border-t border-slate-200 pt-3 text-lg font-bold"><span>Total</span><span>&#8369;{{ number_format((float) $order->total, 2) }}</span></div>
                    </div>
                </div>

                @if ($order->invoice)
                    <div class="rounded-3xl bg-white p-6 shadow-soft">
                        <h2 class="mb-4 text-xl font-semibold">Invoice</h2>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between gap-4"><span class="text-slate-500">Prints</span><span>{{ $order->invoice->print_count }}</span></div>
                            <div class="flex justify-between gap-4"><span class="text-slate-500">Last Printed</span><span>{{ $order->invoice->last_printed_at?->format('M d, Y h:i A') ?? 'Not yet' }}</span></div>
                            <div class="grid grid-cols-1 gap-2 pt-2 sm:grid-cols-2">
                                <button
                                    type="button"
                                    class="js-admin-print-invoice rounded-xl bg-pink-600 px-3 py-2 text-sm font-semibold text-white hover:bg-pink-700"
                                    data-print-url="{{ route('admin.invoices.print', $order->invoice) }}"
                                    data-track-url="{{ route('admin.invoices.track-print', $order->invoice) }}"
                                >
                                    Print
                                </button>
                                <a
                                    href="{{ route('admin.invoices.download', $order->invoice) }}"
                                    class="rounded-xl bg-slate-100 px-3 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-200"
                                >
                                    Download
                                </a>
                            </div>
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

                <div class="rounded-3xl bg-white p-6 shadow-soft">
                    <h2 class="mb-4 text-xl font-semibold">Customer</h2>
                    <div class="space-y-2 text-sm">
                        <p class="font-medium">{{ $order->customer_name }}</p>
                        <p>{{ $order->customer_email }}</p>
                        <p>{{ $order->customer_phone }}</p>
                        @if ($displayDeliveryAddress)
                            <div class="mt-3 rounded-2xl bg-slate-50 p-3">
                                <p class="text-sm text-slate-500">Delivery Address</p>
                                <div class="mt-1 flex items-start justify-between gap-2">
                                    <p>{{ $displayDeliveryAddress }}</p>
                                    @if ($order->delivery_address && filter_var($order->delivery_address, FILTER_VALIDATE_URL))
                                        <a href="{{ $order->delivery_address }}" target="_blank" rel="noopener" class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-white text-slate-600 hover:bg-slate-100" aria-label="Open map">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6"/><path d="M15 18V9H6"/><path d="M21 3h-6"/><path d="M21 9V3"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                        @if ($order->special_instructions)
                            <div class="mt-3 rounded-2xl bg-slate-50 p-3">
                                <p class="text-sm text-slate-500">Special Instructions</p>
                                <p class="mt-1">{{ $order->special_instructions }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<div id="status-confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 px-4" aria-hidden="true">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-soft">
        <h3 class="text-lg font-semibold text-slate-900">Confirm Status Change</h3>
        <p class="mt-2 text-sm text-slate-600">Are you sure you want to continue?</p>
        <div class="mt-6 flex items-center justify-end gap-3">
            <button type="button" id="status-confirm-cancel" class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</button>
            <button type="button" id="status-confirm-submit" class="rounded-xl bg-pink-600 px-4 py-2 text-sm font-semibold text-white hover:bg-pink-700">Confirm</button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function () {
            document.querySelectorAll('.js-admin-print-invoice').forEach((button) => {
                button.addEventListener('click', async () => {
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

                    try {
                        await fetch(button.dataset.trackUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                            },
                        });
                    } catch (error) {
                        console.warn('Invoice print tracking failed.', error);
                    }

                    window.open(button.dataset.printUrl, '_blank', 'noopener');
                });
            });

            const modal = document.getElementById('status-confirm-modal');
            const cancelBtn = document.getElementById('status-confirm-cancel');
            const submitBtn = document.getElementById('status-confirm-submit');
            let pendingForm = null;

            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                pendingForm = null;
            };

            document.querySelectorAll('.js-status-next-form, .js-status-other-form').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    event.preventDefault();
                    pendingForm = form;
                    const selected = form.querySelector('input[name="status"], select[name="status"]');
                    const statusValue = selected ? selected.value : 'updated';
                    const msg = modal.querySelector('p');
                    if (msg) msg.textContent = `Are you sure you want to set this order to ${String(statusValue).toUpperCase()}?`;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                });
            });

            cancelBtn?.addEventListener('click', closeModal);
            modal?.addEventListener('click', (event) => {
                if (event.target === modal) closeModal();
            });
            submitBtn?.addEventListener('click', () => {
                if (pendingForm) pendingForm.submit();
            });
        })();
    </script>
@endpush
