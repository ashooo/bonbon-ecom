<!-- Orders Section -->
<div id="orders-section" class="admin-section hidden">
    <div class="space-y-6">
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold tracking-tight text-[#4B2E38]">Orders overview</h1>
            <p class="text-sm text-[#8A6A76]">Track the pipeline and process orders efficiently</p>
        </div>

        <div class="grid grid-cols-2 gap-4 xl:grid-cols-6">
            <div class="rounded-2xl border border-pink-200 bg-pink-100 p-4 shadow-sm"><p class="text-xs uppercase tracking-[0.16em] text-pink-700">All</p><p class="mt-2 text-3xl font-semibold text-[#4D2E38]">{{ $orderCounts['all'] ?? 0 }}</p></div>
            <div class="rounded-2xl border border-amber-300 bg-amber-100 p-4 shadow-sm"><p class="text-xs uppercase tracking-[0.16em] text-amber-700">Pending</p><p class="mt-2 text-3xl font-semibold text-amber-800">{{ $orderCounts['pending'] ?? 0 }}</p></div>
            <div class="rounded-2xl border border-sky-300 bg-sky-100 p-4 shadow-sm"><p class="text-xs uppercase tracking-[0.16em] text-sky-700">Confirmed</p><p class="mt-2 text-3xl font-semibold text-sky-800">{{ $orderCounts['confirmed'] ?? 0 }}</p></div>
            <div class="rounded-2xl border border-violet-300 bg-violet-100 p-4 shadow-sm"><p class="text-xs uppercase tracking-[0.16em] text-violet-700">Ready</p><p class="mt-2 text-3xl font-semibold text-violet-800">{{ $orderCounts['ready'] ?? 0 }}</p></div>
            <div class="rounded-2xl border border-emerald-300 bg-emerald-100 p-4 shadow-sm"><p class="text-xs uppercase tracking-[0.16em] text-emerald-700">Completed</p><p class="mt-2 text-3xl font-semibold text-emerald-800">{{ $orderCounts['completed'] ?? 0 }}</p></div>
            <div class="rounded-2xl border border-rose-300 bg-rose-100 p-4 shadow-sm"><p class="text-xs uppercase tracking-[0.16em] text-rose-700">Cancelled</p><p class="mt-2 text-3xl font-semibold text-rose-800">{{ $orderCounts['cancelled'] ?? 0 }}</p></div>
        </div>

        <div class="rounded-3xl border border-[#ECD8E0] bg-white p-6 shadow-sm">
            <div class="mb-4 grid grid-cols-1 gap-3 xl:grid-cols-[1fr_auto]">
                <form id="orders-filter-form" method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 gap-2 rounded-2xl border border-[#ECD8E0] bg-[#FFF8FB] p-3 sm:grid-cols-2 xl:grid-cols-[280px_220px]">
                    <input type="hidden" name="section" value="orders">
                    <input type="text" name="search" value="{{ $orderFilters['search'] ?? '' }}" placeholder="Search order no, customer, email..." class="w-full rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843] focus:border-[#C98A9B] focus:ring-2 focus:ring-[#F5DDE6] focus:outline-none">
                    <select name="status" class="rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843] focus:border-[#C98A9B] focus:ring-2 focus:ring-[#F5DDE6] focus:outline-none">
                        @foreach (['all', 'pending', 'confirmed', 'ready', 'completed', 'cancelled'] as $statusOption)
                            <option value="{{ $statusOption }}" @selected(($orderFilters['status'] ?? 'all') === $statusOption)>{{ ucfirst($statusOption) }}</option>
                        @endforeach
                    </select>
                </form>
                <details class="rounded-2xl border border-[#ECD8E0] bg-white">
                    <summary class="cursor-pointer list-none px-4 py-3 text-sm font-semibold text-[#6B4A57]">Export orders</summary>
                    <form method="GET" action="{{ route('admin.orders.export') }}" class="grid grid-cols-1 gap-2 border-t border-[#F2DFE6] p-3 sm:grid-cols-[1fr_1fr_120px_auto]">
                        <input type="date" name="start_date" value="{{ now()->startOfMonth()->toDateString() }}" class="rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843]" required>
                        <input type="date" name="end_date" value="{{ now()->toDateString() }}" class="rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843]" required>
                        <select name="format" class="rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843]"><option value="csv">CSV</option><option value="excel">Excel</option><option value="pdf">PDF</option></select>
                        <button type="submit" class="rounded-xl bg-[#C47A90] px-4 py-2 text-sm font-semibold text-white hover:bg-[#B66880]">Export</button>
                    </form>
                </details>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-[#F1E2E8]">
                <table id="orders-table" class="w-full min-w-[1100px]">
                    <thead class="bg-[#FBF2F6]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Order</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Items</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Schedule</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F4E5EB] bg-white">
                        @forelse(($orders ?? collect()) as $order)
                            @php
                                $status = $order->status ?? 'pending';
                                $statusClass = match ($status) {
                                    'completed' => 'bg-emerald-50 text-emerald-700',
                                    'cancelled' => 'bg-rose-50 text-rose-700',
                                    'confirmed' => 'bg-sky-50 text-sky-700',
                                    'ready' => 'bg-violet-50 text-violet-700',
                                    default => 'bg-amber-50 text-amber-700',
                                };
                                $nextStatus = match ($status) { 'pending' => 'confirmed', 'confirmed' => 'ready', 'ready' => 'completed', default => null };
                            @endphp
                            <tr class="js-order-row cursor-pointer hover:bg-[#FFFCFD]" data-order-url="{{ route('admin.orders.show', ['order' => $order, 'status' => $orderFilters['status'] ?? 'all', 'search' => $orderFilters['search'] ?? '', 'page' => method_exists($orders, 'currentPage') ? $orders->currentPage() : 1]) }}">
                                <td class="px-4 py-4 text-sm">
                                    <a href="{{ route('admin.orders.show', ['order' => $order, 'status' => $orderFilters['status'] ?? 'all', 'search' => $orderFilters['search'] ?? '', 'page' => method_exists($orders, 'currentPage') ? $orders->currentPage() : 1]) }}" class="font-semibold text-[#B66880] hover:text-[#9E536A] hover:underline">{{ $order->order_number }}</a>
                                    <p class="text-xs text-[#8A6A76]">{{ $order->created_at?->format('M d, g:i A') }}</p>
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    <p class="font-medium text-[#4E303A]">{{ $order->customer_name }}</p>
                                    <p class="text-xs text-[#8A6A76]">{{ $order->customer_email }}</p>
                                </td>
                                <td class="px-4 py-4 text-sm text-[#6B4A57]">
                                    @php $itemCount = (int) $order->items->sum('quantity'); @endphp
                                    <p>{{ $itemCount }} {{ \Illuminate\Support\Str::plural('item', $itemCount) }}</p>
                                    <p class="text-xs text-[#8A6A76]">{{ $order->items->first()?->variant?->product?->name ?? 'No items' }}@if ($order->items->count() > 1) +{{ $order->items->count() - 1 }} more @endif</p>
                                </td>
                                <td class="px-4 py-4 text-sm text-[#6B4A57]">
                                    <p class="font-medium">{{ ucfirst((string) $order->order_type) }}</p>
                                    <p class="text-xs">{{ $order->fulfillment_date?->format('M d, Y') ?? 'N/A' }} {{ $order->fulfillment_time ? \Carbon\Carbon::createFromFormat('H:i:s', $order->fulfillment_time)->format('g:i A') : '' }}</p>
                                </td>
                                <td class="px-4 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ ucfirst($status) }}</span></td>
                                <td class="px-4 py-4 text-sm font-semibold text-[#4E303A]">&#8369;{{ number_format((float) $order->total, 2) }}</td>
                                <td class="js-row-actions px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <a
                                            href="{{ route('admin.orders.show', ['order' => $order, 'status' => $orderFilters['status'] ?? 'all', 'search' => $orderFilters['search'] ?? '', 'page' => method_exists($orders, 'currentPage') ? $orders->currentPage() : 1]) }}"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#E3CDD7] text-[#6B4A57] hover:bg-[#F7EBF0]"
                                            title="View order details"
                                            aria-label="View order details"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </a>
                                        <a
                                            href="{{ route('admin.orders.print-slip', $order) }}"
                                            target="_blank"
                                            rel="noopener"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#E3CDD7] text-[#6B4A57] hover:bg-[#F7EBF0]"
                                            title="Print delivery slip"
                                            aria-label="Print delivery slip"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M6 9V4h12v5"/>
                                                <rect x="6" y="14" width="12" height="6" rx="1"/>
                                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                                <path d="M16 18h.01"/>
                                            </svg>
                                        </a>
                                        @if($nextStatus)
                                            <form method="POST" action="{{ route('admin.orders.status.update', $order) }}" class="inline js-status-update-form" data-next-status="{{ $nextStatus }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $nextStatus }}">
                                                <input type="hidden" name="redirect_status" value="{{ $orderFilters['status'] ?? 'all' }}">
                                                <input type="hidden" name="redirect_search" value="{{ $orderFilters['search'] ?? '' }}">
                                                <input type="hidden" name="redirect_page" value="{{ method_exists($orders, 'currentPage') ? $orders->currentPage() : 1 }}">
                                                <button type="submit" class="rounded-lg bg-[#C47A90] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#B66880]">Mark {{ ucfirst($nextStatus) }}</button>
                                            </form>
                                        @endif
                                        <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#E3CDD7] text-[#6B4A57] hover:bg-[#F7EBF0] js-more-actions-btn"
                                            data-order-id="{{ $order->id }}"
                                            data-order-status="{{ $status }}"
                                            data-redirect-status="{{ $orderFilters['status'] ?? 'all' }}"
                                            data-redirect-search="{{ $orderFilters['search'] ?? '' }}"
                                            data-redirect-page="{{ method_exists($orders, 'currentPage') ? $orders->currentPage() : 1 }}"
                                            data-has-invoice="{{ $order->invoice ? '1' : '0' }}"
                                            data-print-url="{{ $order->invoice ? route('admin.invoices.print', $order->invoice) : '' }}"
                                            data-track-url="{{ $order->invoice ? route('admin.invoices.track-print', $order->invoice) : '' }}"
                                            data-download-url="{{ $order->invoice ? route('admin.invoices.download', $order->invoice) : '' }}"
                                            aria-label="More actions">•••</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-sm text-[#8A6A76]">No orders found for the selected filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (isset($orders) && method_exists($orders, 'links'))
                <div class="mt-6">{{ $orders->appends(['section' => 'orders', 'status' => $orderFilters['status'] ?? 'all', 'search' => $orderFilters['search'] ?? null])->links() }}</div>
            @endif
        </div>
    </div>
</div>

<div id="status-confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 px-4" aria-hidden="true">
    <div class="w-full max-w-md rounded-2xl border border-[#ECD8E0] bg-white p-6 shadow-lg">
        <h3 class="text-lg font-semibold text-[#4B2E38]" id="status-confirm-title">Confirm Status Change</h3>
        <p class="mt-2 text-sm text-[#7E5E6A]" id="status-confirm-message">Are you sure you want to continue?</p>
        <div class="mt-6 flex items-center justify-end gap-3">
            <button type="button" id="status-confirm-cancel" class="rounded-xl border border-[#E5D2DA] bg-white px-4 py-2 text-sm font-semibold text-[#6B4A57] hover:bg-[#F8EFF3]">Cancel</button>
            <button type="button" id="status-confirm-submit" class="rounded-xl bg-[#C47A90] px-4 py-2 text-sm font-semibold text-white hover:bg-[#B66880]">Confirm</button>
        </div>
    </div>
</div>

<div id="order-actions-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 px-4" aria-hidden="true">
    <div class="w-full max-w-md rounded-2xl border border-[#ECD8E0] bg-white p-6 shadow-lg">
        <h3 class="text-lg font-semibold text-[#4B2E38]">More actions</h3>
        <div id="order-actions-content" class="mt-4 space-y-2"></div>
        <div class="mt-4 flex justify-end">
            <button type="button" id="order-actions-close" class="rounded-xl border border-[#E5D2DA] bg-white px-4 py-2 text-sm font-semibold text-[#6B4A57] hover:bg-[#F8EFF3]">Close</button>
        </div>
    </div>
</div>

<form id="order-status-action-form" method="POST" class="hidden">@csrf @method('PATCH')</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('orders-filter-form');
        let searchTimer = null;
        filterForm?.querySelector('select[name="status"]')?.addEventListener('change', () => filterForm.submit());
        filterForm?.querySelector('input[name="search"]')?.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => filterForm.submit(), 300);
        });

        const confirmModal = document.getElementById('status-confirm-modal');
        const confirmMsg = document.getElementById('status-confirm-message');
        const confirmCancel = document.getElementById('status-confirm-cancel');
        const confirmSubmit = document.getElementById('status-confirm-submit');
        let pendingStatusForm = null;
        const openConfirm = (form, status) => {
            pendingStatusForm = form;
            confirmMsg.textContent = `Are you sure you want to mark this order as ${String(status).toUpperCase()}?`;
            confirmModal.classList.remove('hidden');
            confirmModal.classList.add('flex');
        };
        const closeConfirm = () => {
            confirmModal.classList.add('hidden');
            confirmModal.classList.remove('flex');
            pendingStatusForm = null;
        };
        document.querySelectorAll('.js-status-update-form').forEach((form) => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                openConfirm(form, form.dataset.nextStatus || form.querySelector('input[name="status"]')?.value || 'updated');
            });
        });
        confirmCancel?.addEventListener('click', closeConfirm);
        confirmModal?.addEventListener('click', (e) => { if (e.target === confirmModal) closeConfirm(); });
        confirmSubmit?.addEventListener('click', () => { if (pendingStatusForm) pendingStatusForm.submit(); });

        const actionsModal = document.getElementById('order-actions-modal');
        const actionsContent = document.getElementById('order-actions-content');
        const actionsClose = document.getElementById('order-actions-close');
        const actionForm = document.getElementById('order-status-action-form');
        const openActions = (data) => {
            const statuses = ['pending', 'confirmed', 'ready', 'completed', 'cancelled'].filter((s) => s !== data.orderStatus);
            actionsContent.innerHTML = '';
            statuses.forEach((status) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'w-full rounded-lg border border-[#E3CDD7] px-3 py-2 text-left text-sm text-[#6B4A57] hover:bg-[#F7EBF0]';
                btn.textContent = `Set ${status.charAt(0).toUpperCase() + status.slice(1)}`;
                btn.addEventListener('click', () => {
                    actionForm.action = `/admin/orders/${data.orderId}/status`;
                    actionForm.innerHTML = `@csrf @method('PATCH')
                        <input type="hidden" name="status" value="${status}">
                        <input type="hidden" name="redirect_status" value="${data.redirectStatus}">
                        <input type="hidden" name="redirect_search" value="${data.redirectSearch}">
                        <input type="hidden" name="redirect_page" value="${data.redirectPage}">`;
                    closeActions();
                    openConfirm(actionForm, status);
                });
                actionsContent.appendChild(btn);
            });
            if (data.hasInvoice === '1') {
                const printBtn = document.createElement('button');
                printBtn.type = 'button';
                printBtn.className = 'w-full rounded-lg border border-[#E3CDD7] px-3 py-2 text-left text-sm text-[#6B4A57] hover:bg-[#F7EBF0]';
                printBtn.textContent = 'Print invoice';
                printBtn.addEventListener('click', async () => {
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
                    try { await fetch(data.trackUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } }); } catch {}
                    window.open(data.printUrl, '_blank', 'noopener');
                });
                actionsContent.appendChild(printBtn);
                const download = document.createElement('a');
                download.className = 'block w-full rounded-lg border border-[#E3CDD7] px-3 py-2 text-left text-sm text-[#6B4A57] hover:bg-[#F7EBF0]';
                download.href = data.downloadUrl;
                download.textContent = 'Download invoice';
                actionsContent.appendChild(download);
            }
            actionsModal.classList.remove('hidden');
            actionsModal.classList.add('flex');
        };
        const closeActions = () => {
            actionsModal.classList.add('hidden');
            actionsModal.classList.remove('flex');
        };
        document.querySelectorAll('.js-more-actions-btn').forEach((btn) => {
            btn.addEventListener('click', () => openActions(btn.dataset));
        });
        actionsClose?.addEventListener('click', closeActions);
        actionsModal?.addEventListener('click', (e) => { if (e.target === actionsModal) closeActions(); });

        document.querySelectorAll('.js-row-actions').forEach((cell) => {
            cell.addEventListener('click', (event) => {
                event.stopPropagation();
            });
        });

        document.querySelectorAll('#orders-table tbody tr.js-order-row[data-order-url]').forEach((row) => {
            row.addEventListener('click', (event) => {
                const interactive = event.target.closest('a, button, form, select, input, textarea, label, summary, details, .js-row-actions');
                if (interactive) return;
                window.location.href = row.dataset.orderUrl;
            });
        });
    });
</script>
@endpush
