<!-- Inventory Section -->
<div id="inventory-section" class="admin-section hidden">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
            <div class="space-y-1">
                <h1 class="text-2xl font-semibold tracking-tight text-[#4B2E38]">Inventory overview</h1>
                <p class="text-sm text-[#8A6A76]">Track variant stock, adjust quantities, and monitor movement history</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-[#E9C7D4] bg-gradient-to-br from-[#FFF7FA] to-[#F6DFE9] p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.16em] text-[#8F6172]">Tracked Variants</p>
                <p class="mt-2 text-3xl font-semibold text-[#4D2E38]">{{ $inventoryCounts['all'] ?? 0 }}</p>
                <p class="mt-1 text-xs text-[#8A6A76]">Total active variants</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.16em] text-emerald-700">In Stock</p>
                <p class="mt-2 text-3xl font-semibold text-emerald-700">{{ $inventoryCounts['in_stock'] ?? 0 }}</p>
                <p class="mt-1 text-xs text-[#8A6A76]">Above threshold</p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.16em] text-amber-700">Low Stock</p>
                <p class="mt-2 text-3xl font-semibold text-amber-700">{{ $inventoryCounts['low_stock'] ?? 0 }}</p>
                <p class="mt-1 text-xs text-[#8A6A76]">1 to 10 units</p>
            </div>
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.16em] text-rose-700">Out of Stock</p>
                <p class="mt-2 text-3xl font-semibold text-rose-700">{{ $inventoryCounts['out_of_stock'] ?? 0 }}</p>
                <p class="mt-1 text-xs text-[#8A6A76]">Needs restock</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 2xl:grid-cols-[2fr_1fr]">
            <div class="rounded-3xl border border-[#ECD8E0] bg-white p-6 shadow-sm">
                <div class="mb-4 flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-[#4B2E38]">Stock list</h2>
                        <p class="text-sm text-[#8A6A76]">Adjust stock quickly per SKU and variant</p>
                    </div>
                    <form id="inventory-filter-form" method="GET" action="{{ route('admin.inventory.index') }}" class="grid w-full gap-2 rounded-2xl border border-[#ECD8E0] bg-[#FFF8FB] p-3 sm:grid-cols-2 xl:w-auto xl:grid-cols-[300px_180px_140px_auto]">
                        <input type="hidden" name="section" value="inventory">
                        <input type="text" name="inventory_search" value="{{ $inventoryFilters['search'] ?? '' }}" placeholder="Search by SKU, variant, product..." class="w-full rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843] focus:border-[#C98A9B] focus:ring-2 focus:ring-[#F5DDE6] focus:outline-none">

                        <select name="inventory_status" class="rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843] focus:border-[#C98A9B] focus:ring-2 focus:ring-[#F5DDE6] focus:outline-none">
                            <option value="all" {{ ($inventoryFilters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All statuses</option>
                            <option value="in_stock" {{ ($inventoryFilters['status'] ?? '') === 'in_stock' ? 'selected' : '' }}>In stock</option>
                            <option value="low_stock" {{ ($inventoryFilters['status'] ?? '') === 'low_stock' ? 'selected' : '' }}>Low stock</option>
                            <option value="out_of_stock" {{ ($inventoryFilters['status'] ?? '') === 'out_of_stock' ? 'selected' : '' }}>Out of stock</option>
                        </select>

                        <select name="inventory_per_page" class="rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843] focus:border-[#C98A9B] focus:ring-2 focus:ring-[#F5DDE6] focus:outline-none">
                            @foreach([12, 25, 50] as $size)
                                <option value="{{ $size }}" {{ (int) ($inventoryFilters['per_page'] ?? 12) === $size ? 'selected' : '' }}>Show {{ $size }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="rounded-xl bg-[#C47A90] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#B66880]">Apply</button>
                    </form>
                </div>

                <div class="mb-3 flex items-center justify-between text-xs text-[#8A6A76]">
                    <p>Stock thresholds: 0 = out of stock, 1-10 = low stock, 11+ = healthy stock.</p>
                    <button type="button" id="inventory-compact-toggle" class="rounded-lg border border-[#E3CDD7] bg-white px-3 py-1.5 font-semibold text-[#6B4A57] hover:bg-[#F7EBF0]">Compact mode</button>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-[#F1E2E8]">
                    <table id="inventory-table" class="w-full min-w-[980px]">
                        <thead class="bg-[#FBF2F6]">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Product / Variant</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">SKU</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Stock</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Adjust Stock</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F4E5EB] bg-white">
                            @forelse(($inventoryItems ?? collect()) as $variant)
                                @php
                                    $stock = (int) ($variant->stock_quantity ?? 0);
                                    $stockClass = $stock <= 0 ? 'bg-rose-50 text-rose-700' : ($stock <= 10 ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700');
                                @endphp
                                <tr class="hover:bg-[#FFFCFD]">
                                    <td class="px-4 py-4 text-sm">
                                        <p class="font-semibold text-[#4E303A]">{{ $variant->product?->name ?? 'Unknown Product' }}</p>
                                        <p class="text-xs text-[#8A6A76]">{{ $variant->name }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-[#6B4A57]">{{ $variant->sku }}</td>
                                    <td class="px-4 py-4 text-sm">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $stockClass }}">
                                            @if($stock > 0 && $stock <= 10)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 inline h-3.5 w-3.5 align-[-2px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M12 9v4"></path>
                                                    <path d="M12 17h.01"></path>
                                                    <path d="m10.29 3.86-8.16 14A2 2 0 0 0 3.87 21h16.26a2 2 0 0 0 1.74-3.14l-8.16-14a2 2 0 0 0-3.42 0z"></path>
                                                </svg>
                                            @endif
                                            {{ $stock }} units
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <form method="POST" action="{{ route('admin.inventory.adjust', $variant) }}" class="grid grid-cols-[130px_90px_1fr_auto] items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="inventory_status" value="{{ $inventoryFilters['status'] ?? 'all' }}">
                                            <input type="hidden" name="inventory_search" value="{{ $inventoryFilters['search'] ?? '' }}">
                                            <input type="hidden" name="inventory_per_page" value="{{ $inventoryFilters['per_page'] ?? 12 }}">
                                            <input type="hidden" name="inventory_page" value="{{ method_exists($inventoryItems, 'currentPage') ? $inventoryItems->currentPage() : 1 }}">

                                            <select name="action" class="rounded-xl border border-[#E7D2DA] px-2 py-2 text-xs text-[#533843]">
                                                <option value="add">Add</option>
                                                <option value="subtract">Subtract</option>
                                                <option value="set">Set exact</option>
                                            </select>
                                            <input type="number" min="1" name="quantity" value="1" class="rounded-xl border border-[#E7D2DA] px-2 py-2 text-xs text-[#533843]">
                                            <input type="text" name="reason" placeholder="Reason (e.g. recount, received shipment)" class="rounded-xl border border-[#E7D2DA] px-2 py-2 text-xs text-[#533843]">
                                            <button type="submit" class="rounded-xl bg-[#C47A90] px-3 py-2 text-xs font-semibold text-white hover:bg-[#B66880]">Apply</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-[#8A6A76]">No inventory items found for the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (isset($inventoryItems) && method_exists($inventoryItems, 'links'))
                    <div class="mt-6">
                        {{ $inventoryItems->appends([
                            'section' => 'inventory',
                            'inventory_search' => $inventoryFilters['search'] ?? null,
                            'inventory_status' => $inventoryFilters['status'] ?? 'all',
                            'inventory_per_page' => $inventoryFilters['per_page'] ?? 12,
                        ])->links() }}
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-[#ECD8E0] bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-[#4B2E38]">Low Stock Alerts</h3>
                    <div class="space-y-3">
                        @forelse(($lowStockVariants ?? collect()) as $variant)
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-3">
                                <p class="text-sm font-medium text-amber-800">{{ $variant->product?->name }} - {{ $variant->name }}</p>
                                <p class="text-xs text-amber-700">{{ $variant->stock_quantity }} units left</p>
                            </div>
                        @empty
                            <p class="text-sm text-[#8A6A76]">No low stock alerts.</p>
                        @endforelse

                        @foreach(($outOfStockVariants ?? collect()) as $variant)
                            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-3">
                                <p class="text-sm font-medium text-rose-800">{{ $variant->product?->name }} - {{ $variant->name }}</p>
                                <p class="text-xs text-rose-700">Out of stock</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-3xl border border-[#ECD8E0] bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-[#4B2E38]">Movement History</h3>
                    <div class="space-y-3">
                        @forelse(($recentInventoryMovements ?? collect()) as $movement)
                            <div class="rounded-2xl border border-[#F1E2E8] bg-[#FFFCFD] p-3">
                                <p class="text-sm font-medium text-[#4E303A]">{{ $movement->variant?->product?->name }} - {{ $movement->variant?->name }}</p>
                                <p class="text-xs text-[#6B4A57]">{{ $movement->quantity_change > 0 ? '+' : '' }}{{ $movement->quantity_change }} ({{ $movement->previous_stock }} -> {{ $movement->new_stock }})</p>
                                <p class="text-xs text-[#8A6A76]">{{ ucfirst(str_replace('_', ' ', $movement->type)) }} by {{ $movement->actor?->name ?? 'System' }}</p>
                                @if ($movement->reason)
                                    <p class="text-xs text-[#8A6A76]">Reason: {{ $movement->reason }}</p>
                                @endif
                                <p class="text-xs text-slate-400">{{ $movement->created_at?->diffForHumans() }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-[#8A6A76]">No inventory movement history yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('inventory-filter-form');
        const compactToggle = document.getElementById('inventory-compact-toggle');
        const inventoryTable = document.getElementById('inventory-table');
        let searchTimer = null;

        if (filterForm) {
            filterForm.querySelectorAll('select[name="inventory_status"], select[name="inventory_per_page"]').forEach((el) => {
                el.addEventListener('change', () => filterForm.submit());
            });
            const searchInput = filterForm.querySelector('input[name="inventory_search"]');
            searchInput?.addEventListener('input', () => {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => filterForm.submit(), 300);
            });
        }

        compactToggle?.addEventListener('click', () => {
            inventoryTable?.classList.toggle('text-xs');
            inventoryTable?.querySelectorAll('td').forEach((td) => td.classList.toggle('py-2'));
            inventoryTable?.querySelectorAll('td').forEach((td) => td.classList.toggle('py-4'));
        });
    });
</script>
@endpush
