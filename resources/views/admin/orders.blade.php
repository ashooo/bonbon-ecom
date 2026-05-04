<!-- Orders Section -->
<div id="orders-section" class="admin-section hidden">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <h1 class="text-3xl font-bold">Orders Management</h1>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.orders.index', ['status' => 'all', 'search' => $orderFilters['search'] ?? null]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ ($orderFilters['status'] ?? 'all') === 'all' ? 'bg-slate-700 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' }}">
                    All ({{ $orderCounts['all'] ?? 0 }})
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'pending', 'search' => $orderFilters['search'] ?? null]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ ($orderFilters['status'] ?? '') === 'pending' ? 'bg-yellow-600 text-white' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}">
                    Pending ({{ $orderCounts['pending'] ?? 0 }})
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'confirmed', 'search' => $orderFilters['search'] ?? null]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ ($orderFilters['status'] ?? '') === 'confirmed' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                    Confirmed ({{ $orderCounts['confirmed'] ?? 0 }})
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'ready', 'search' => $orderFilters['search'] ?? null]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ ($orderFilters['status'] ?? '') === 'ready' ? 'bg-indigo-600 text-white' : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200' }}">
                    Ready ({{ $orderCounts['ready'] ?? 0 }})
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'completed', 'search' => $orderFilters['search'] ?? null]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ ($orderFilters['status'] ?? '') === 'completed' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                    Completed ({{ $orderCounts['completed'] ?? 0 }})
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'cancelled', 'search' => $orderFilters['search'] ?? null]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ ($orderFilters['status'] ?? '') === 'cancelled' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                    Cancelled ({{ $orderCounts['cancelled'] ?? 0 }})
                </a>
            </div>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-soft">
            @if (session('success'))
                <div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-6 grid grid-cols-1 gap-4 xl:grid-cols-[1fr_auto]">
                <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-col gap-3 md:flex-row">
                    <input type="hidden" name="status" value="{{ $orderFilters['status'] ?? 'all' }}">
                    <input
                        type="text"
                        name="search"
                        value="{{ $orderFilters['search'] ?? '' }}"
                        placeholder="Search by order number, name, or email"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                    >
                    <button type="submit" class="rounded-2xl bg-slate-700 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                        Search
                    </button>
                </form>

                <form method="GET" action="{{ route('admin.orders.export') }}" class="grid grid-cols-1 gap-2 sm:grid-cols-[1fr_1fr_auto_auto]">
                    <div>
                        <label for="orders_export_start_date" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Start Date</label>
                        <input id="orders_export_start_date" type="date" name="start_date" value="{{ now()->startOfMonth()->toDateString() }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100" required>
                    </div>
                    <div>
                        <label for="orders_export_end_date" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">End Date</label>
                        <input id="orders_export_end_date" type="date" name="end_date" value="{{ now()->toDateString() }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100" required>
                    </div>
                    <div>
                        <label for="orders_export_format" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Format</label>
                        <select id="orders_export_format" name="format" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100">
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full rounded-xl bg-pink-600 px-4 py-2 text-sm font-semibold text-white hover:bg-pink-700">
                            Export Orders
                        </button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Order</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Customer</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Items</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Schedule</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Total</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse(($orders ?? collect()) as $order)
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
                            <tr class="cursor-pointer hover:bg-slate-50" data-order-url="{{ route('admin.orders.show', [
                                'order' => $order,
                                'status' => $orderFilters['status'] ?? 'all',
                                'search' => $orderFilters['search'] ?? '',
                                'page' => method_exists($orders, 'currentPage') ? $orders->currentPage() : 1,
                            ]) }}">
                                <td class="px-4 py-4 text-sm">
                                    <a
                                        href="{{ route('admin.orders.show', [
                                            'order' => $order,
                                            'status' => $orderFilters['status'] ?? 'all',
                                            'search' => $orderFilters['search'] ?? '',
                                            'page' => method_exists($orders, 'currentPage') ? $orders->currentPage() : 1,
                                        ]) }}"
                                        class="font-semibold text-indigo-600 hover:text-indigo-800 hover:underline"
                                    >
                                        {{ $order->order_number }}
                                    </a>
                                    <p class="text-xs text-slate-500">{{ $order->created_at?->format('M d, Y h:i A') }}</p>
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    <p class="font-medium">{{ $order->customer_name }}</p>
                                    <p class="text-xs text-slate-500">{{ $order->customer_email }}</p>
                                    <p class="text-xs text-slate-500">{{ $order->customer_phone }}</p>
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    <p>{{ $order->items->sum('quantity') }} item(s)</p>
                                    <p class="text-xs text-slate-500">
                                        {{ $order->items->first()?->variant?->product?->name ?? 'No items' }}
                                        @if ($order->items->count() > 1)
                                            +{{ $order->items->count() - 1 }} more
                                        @endif
                                    </p>
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    <p class="font-medium">{{ ucfirst((string) $order->order_type) }}</p>
                                    <p class="text-xs text-slate-500">
                                        {{ $order->fulfillment_date?->format('M d, Y') ?? 'N/A' }}
                                        {{ $order->fulfillment_time ? \Illuminate\Support\Str::of($order->fulfillment_time)->substr(0, 5) : '' }}
                                    </p>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm font-semibold">&#8369;{{ number_format((float) $order->total, 2) }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-col gap-2">
                                        <a
                                            href="{{ route('admin.orders.show', [
                                                'order' => $order,
                                                'status' => $orderFilters['status'] ?? 'all',
                                                'search' => $orderFilters['search'] ?? '',
                                                'page' => method_exists($orders, 'currentPage') ? $orders->currentPage() : 1,
                                            ]) }}"
                                            class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline"
                                        >
                                            View Details
                                        </a>

                                        @if ($order->invoice)
                                            <div class="flex flex-wrap gap-2">
                                                <button
                                                    type="button"
                                                    class="js-admin-print-invoice text-xs font-semibold text-pink-600 hover:text-pink-800 hover:underline"
                                                    data-print-url="{{ route('admin.invoices.print', $order->invoice) }}"
                                                    data-track-url="{{ route('admin.invoices.track-print', $order->invoice) }}"
                                                >
                                                    Print Invoice
                                                </button>
                                                <a
                                                    href="{{ route('admin.invoices.download', $order->invoice) }}"
                                                    class="text-xs font-semibold text-slate-600 hover:text-slate-800 hover:underline"
                                                >
                                                    Download
                                                </a>
                                            </div>
                                        @endif

                                        <form method="POST" action="{{ route('admin.orders.status.update', $order) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="redirect_status" value="{{ $orderFilters['status'] ?? 'all' }}">
                                            <input type="hidden" name="redirect_search" value="{{ $orderFilters['search'] ?? '' }}">
                                            <input type="hidden" name="redirect_page" value="{{ method_exists($orders, 'currentPage') ? $orders->currentPage() : 1 }}">
                                            <select name="status" class="rounded-xl border border-slate-200 px-3 py-1 text-xs">
                                                @foreach (['pending', 'confirmed', 'ready', 'completed', 'cancelled'] as $optionStatus)
                                                    <option value="{{ $optionStatus }}" @selected($status === $optionStatus)>{{ ucfirst($optionStatus) }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="rounded-xl bg-slate-700 px-3 py-1 text-xs font-semibold text-white hover:bg-slate-800">
                                                Update
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500">
                                    No orders found for the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (isset($orders) && method_exists($orders, 'links'))
                <div class="mt-6">
                    {{ $orders->appends(['section' => 'orders'])->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    (function () {
        document.querySelectorAll('#orders-section tbody tr[data-order-url]').forEach((row) => {
            row.addEventListener('click', (event) => {
                const interactive = event.target.closest('a, button, form, select, input, textarea, label');
                if (interactive) return;
                window.location.href = row.dataset.orderUrl;
            });
        });

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
    })();
</script>
