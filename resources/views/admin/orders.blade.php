<!-- Orders Section -->
<div id="orders-section" class="admin-section hidden">
    <div class="space-y-6">
<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-3xl font-bold">Orders Management</h1>
        <p class="mt-1 text-sm text-slate-500">Manage and track all customer orders</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.orders.index', ['status' => 'all', 'search' => $orderFilters['search'] ?? null]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold inline-flex items-center gap-2 {{ ($orderFilters['status'] ?? 'all') === 'all' ? 'bg-pink-600 text-white' : 'bg-pink-50 text-pink-700 hover:bg-pink-100' }}">
            All <span class="ml-1">{{ $orderCounts['all'] ?? 0 }}</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'pending', 'search' => $orderFilters['search'] ?? null]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold inline-flex items-center gap-2 {{ ($orderFilters['status'] ?? '') === 'pending' ? 'bg-yellow-500 text-white' : 'bg-yellow-50 text-yellow-800 hover:bg-yellow-100' }}">
            Pending <span class="ml-1">{{ $orderCounts['pending'] ?? 0 }}</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'confirmed', 'search' => $orderFilters['search'] ?? null]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold inline-flex items-center gap-2 {{ ($orderFilters['status'] ?? '') === 'confirmed' ? 'bg-indigo-500 text-white' : 'bg-slate-50 text-slate-700 hover:bg-slate-100' }}">
            Confirmed <span class="ml-1">{{ $orderCounts['confirmed'] ?? 0 }}</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'ready', 'search' => $orderFilters['search'] ?? null]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold inline-flex items-center gap-2 {{ ($orderFilters['status'] ?? '') === 'ready' ? 'bg-indigo-700 text-white' : 'bg-indigo-50 text-indigo-800 hover:bg-indigo-100' }}">
            Ready <span class="ml-1">{{ $orderCounts['ready'] ?? 0 }}</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'completed', 'search' => $orderFilters['search'] ?? null]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold inline-flex items-center gap-2 {{ ($orderFilters['status'] ?? '') === 'completed' ? 'bg-green-700 text-white' : 'bg-green-50 text-green-800 hover:bg-green-100' }}">
            Completed <span class="ml-1">{{ $orderCounts['completed'] ?? 0 }}</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'cancelled', 'search' => $orderFilters['search'] ?? null]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold inline-flex items-center gap-2 {{ ($orderFilters['status'] ?? '') === 'cancelled' ? 'bg-red-500 text-white' : 'bg-red-50 text-red-800 hover:bg-red-100' }}">
            Cancelled <span class="ml-1">{{ $orderCounts['cancelled'] ?? 0 }}</span>
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
                    <div class="relative flex-1">
                        <input
                            type="text"
                            name="search"
                            value="{{ $orderFilters['search'] ?? '' }}"
                            placeholder="Search by order number, name, or email"
                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-200 focus:outline-none transition-all duration-200 pl-10"
                        >
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <button type="submit" class="relative group rounded-xl bg-pink-600 px-4 text-sm font-semibold text-white hover:bg-pink-700 transition-all duration-200 shadow-md inline-flex items-center justify-center gap-2" aria-label="Search" style="height: 42px;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-900 text-white text-xs px-2 py-1 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity">Search Orders</span>
                    </button>
                </form>

                <form method="GET" action="{{ route('admin.orders.export') }}" class="grid grid-cols-1 gap-2 sm:grid-cols-[1fr_1fr_auto_auto]">
                    <div>
                        <label for="orders_export_start_date" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Start Date</label>
                        <input id="orders_export_start_date" type="date" name="start_date" value="{{ now()->startOfMonth()->toDateString() }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-200 focus:outline-none transition-all duration-200" required>
                    </div>
                    <div>
                        <label for="orders_export_end_date" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">End Date</label>
                        <input id="orders_export_end_date" type="date" name="end_date" value="{{ now()->toDateString() }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-200 focus:outline-none transition-all duration-200" required>
                    </div>
                    <div>
                        <label for="orders_export_format" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Format</label>
                        <select id="orders_export_format" name="format" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-200 focus:outline-none transition-all duration-200">
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="relative group w-full rounded-xl bg-pink-600 px-3 py-2 text-sm font-semibold text-white hover:bg-pink-700 transition-all duration-200 shadow-md inline-flex items-center justify-center gap-2" style="min-height: 42px;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-900 text-white text-xs px-2 py-1 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity">Export Orders</span>
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
                'confirmed' => 'bg-slate-100 text-slate-700',
                'ready' => 'bg-indigo-100 text-indigo-800',
                default => 'bg-yellow-100 text-yellow-800',
            };
        @endphp
        <tr class="hover:bg-slate-50/50 transition-colors duration-150">
            <td class="px-4 py-4 text-sm">
                <a href="{{ route('admin.orders.show', ['order' => $order, 'status' => $orderFilters['status'] ?? 'all', 'search' => $orderFilters['search'] ?? '', 'page' => method_exists($orders, 'currentPage') ? $orders->currentPage() : 1]) }}" class="font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
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
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                    {{ ucfirst($status) }}
                </span>
            </td>
            <td class="px-4 py-4 text-sm font-semibold">₱{{ number_format((float) $order->total, 2) }}</td>
            <td class="px-4 py-4">
                <div class="flex items-center gap-2">
                    <!-- Update Status Form -->
                    <form method="POST" action="{{ route('admin.orders.status.update', $order) }}" class="update-status-form inline" data-status="{{ $status }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="redirect_status" value="{{ $orderFilters['status'] ?? 'all' }}">
                        <input type="hidden" name="redirect_search" value="{{ $orderFilters['search'] ?? '' }}">
                        <input type="hidden" name="redirect_page" value="{{ method_exists($orders, 'currentPage') ? $orders->currentPage() : 1 }}">
                        <select name="status" class="status-select rounded-xl border border-slate-200 px-2 py-1.5 text-xs focus:border-pink-400 focus:ring-2 focus:ring-pink-200 focus:outline-none">
                            @foreach (['pending', 'confirmed', 'ready', 'completed', 'cancelled'] as $optionStatus)
                                <option value="{{ $optionStatus }}" @selected($status === $optionStatus)>{{ ucfirst($optionStatus) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="update-btn group relative rounded-xl bg-pink-600 p-1.5 text-white hover:bg-pink-700 transition-all duration-200 shadow-sm" title="Update Status">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-900 text-white text-xs px-2 py-1 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity">Update Status</span>
                        </button>
                    </form>

                    <!-- View Details Button -->
                    <a href="{{ route('admin.orders.show', ['order' => $order, 'status' => $orderFilters['status'] ?? 'all', 'search' => $orderFilters['search'] ?? '', 'page' => method_exists($orders, 'currentPage') ? $orders->currentPage() : 1]) }}" class="group relative text-indigo-600 hover:text-indigo-800 transition-colors" title="View Details">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-900 text-white text-xs px-2 py-1 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity">View Details</span>
                    </a>

                    @if ($order->invoice)
                        <!-- Print Invoice Button -->
                        <button type="button" class="js-admin-print-invoice group relative text-pink-600 hover:text-pink-800 transition-colors" data-print-url="{{ route('admin.invoices.print', $order->invoice) }}" data-track-url="{{ route('admin.invoices.track-print', $order->invoice) }}" title="Print Invoice">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-900 text-white text-xs px-2 py-1 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity">Print Invoice</span>
                        </button>

                        <!-- Download Invoice Button -->
                        <a href="{{ route('admin.invoices.download', $order->invoice) }}" class="group relative text-slate-600 hover:text-slate-800 transition-colors" title="Download">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-900 text-white text-xs px-2 py-1 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity">Download Invoice</span>
                        </a>
                    @endif
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

<script>
document.querySelectorAll('.update-status-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const select = this.querySelector('.status-select');
        const newStatus = select.value;
        const currentStatus = this.dataset.status;
        
        let confirmMessage = '';
        switch(newStatus) {
            case 'ready':
                confirmMessage = 'Are you sure you want to mark this order as READY?';
                break;
            case 'completed':
                confirmMessage = 'Are you sure you want to mark this order as COMPLETED?';
                break;
            case 'confirmed':
                confirmMessage = 'Are you sure you want to CONFIRM this order?';
                break;
            case 'cancelled':
                confirmMessage = 'Are you sure you want to CANCEL this order? This action cannot be undone.';
                break;
            case 'pending':
                confirmMessage = 'Are you sure you want to change status back to PENDING?';
                break;
            default:
                confirmMessage = `Are you sure you want to update this order status to ${newStatus.toUpperCase()}?`;
        }
        
        if (confirm(confirmMessage)) {
            this.submit();
        }
    });
});
</script>
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
