<!-- Inventory Section -->
<div id="inventory-section" class="admin-section hidden">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <h1 class="text-3xl font-bold">Inventory Management</h1>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.inventory.index', ['inventory_status' => 'all', 'inventory_search' => $inventoryFilters['search'] ?? null]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ ($inventoryFilters['status'] ?? 'all') === 'all' ? 'bg-slate-700 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' }}">All ({{ $inventoryCounts['all'] ?? 0 }})</a>
                <a href="{{ route('admin.inventory.index', ['inventory_status' => 'in_stock', 'inventory_search' => $inventoryFilters['search'] ?? null]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ ($inventoryFilters['status'] ?? '') === 'in_stock' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">In Stock ({{ $inventoryCounts['in_stock'] ?? 0 }})</a>
                <a href="{{ route('admin.inventory.index', ['inventory_status' => 'low_stock', 'inventory_search' => $inventoryFilters['search'] ?? null]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ ($inventoryFilters['status'] ?? '') === 'low_stock' ? 'bg-yellow-600 text-white' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}">Low Stock ({{ $inventoryCounts['low_stock'] ?? 0 }})</a>
                <a href="{{ route('admin.inventory.index', ['inventory_status' => 'out_of_stock', 'inventory_search' => $inventoryFilters['search'] ?? null]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ ($inventoryFilters['status'] ?? '') === 'out_of_stock' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">Out of Stock ({{ $inventoryCounts['out_of_stock'] ?? 0 }})</a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>
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

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[2fr_1fr]">
            <div class="rounded-3xl bg-white p-6 shadow-soft">
                <form method="GET" action="{{ route('admin.inventory.index') }}" class="mb-6 flex flex-col gap-3 md:flex-row">
                    <input type="hidden" name="inventory_status" value="{{ $inventoryFilters['status'] ?? 'all' }}">
                    <input type="text" name="inventory_search" value="{{ $inventoryFilters['search'] ?? '' }}" placeholder="Search by SKU, variant, or product" class="w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100">
                    <button type="submit" class="rounded-2xl bg-slate-700 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800">Search</button>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Product / Variant</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">SKU</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Stock</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Adjust</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse(($inventoryItems ?? collect()) as $variant)
                                @php
                                    $stockClass = $variant->stock_quantity <= 0
                                        ? 'bg-red-100 text-red-800'
                                        : ($variant->stock_quantity <= 10 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                                @endphp
                                <tr>
                                    <td class="px-4 py-4 text-sm">
                                        <p class="font-semibold">{{ $variant->product?->name ?? 'Unknown Product' }}</p>
                                        <p class="text-xs text-slate-500">{{ $variant->name }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-sm">{{ $variant->sku }}</td>
                                    <td class="px-4 py-4 text-sm">
                                        <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $stockClass }}">{{ $variant->stock_quantity }}</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <form method="POST" action="{{ route('admin.inventory.adjust', $variant) }}" class="flex flex-wrap items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="inventory_status" value="{{ $inventoryFilters['status'] ?? 'all' }}">
                                            <input type="hidden" name="inventory_search" value="{{ $inventoryFilters['search'] ?? '' }}">
                                            <input type="hidden" name="inventory_page" value="{{ method_exists($inventoryItems, 'currentPage') ? $inventoryItems->currentPage() : 1 }}">

                                            <select name="action" class="rounded-xl border border-slate-200 px-2 py-1 text-xs">
                                                <option value="add">Add</option>
                                                <option value="subtract">Subtract</option>
                                                <option value="set">Set</option>
                                            </select>
                                            <input type="number" min="1" name="quantity" value="1" class="w-20 rounded-xl border border-slate-200 px-2 py-1 text-xs">
                                            <input type="text" name="reason" placeholder="Reason" class="rounded-xl border border-slate-200 px-2 py-1 text-xs">
                                            <button type="submit" class="rounded-xl bg-slate-700 px-3 py-1 text-xs font-semibold text-white hover:bg-slate-800">Apply</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">No inventory items found for the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (isset($inventoryItems) && method_exists($inventoryItems, 'links'))
                    <div class="mt-6">
                        {{ $inventoryItems->appends(['section' => 'inventory'])->links() }}
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl bg-white p-6 shadow-soft">
                    <h3 class="mb-4 text-lg font-semibold">Low Stock Alerts</h3>
                    <div class="space-y-3">
                        @forelse(($lowStockVariants ?? collect()) as $variant)
                            <div class="rounded-2xl border border-yellow-200 bg-yellow-50 p-3">
                                <p class="text-sm font-medium text-yellow-800">{{ $variant->product?->name }} - {{ $variant->name }}</p>
                                <p class="text-xs text-yellow-700">{{ $variant->stock_quantity }} units left</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No low stock alerts.</p>
                        @endforelse

                        @foreach(($outOfStockVariants ?? collect()) as $variant)
                            <div class="rounded-2xl border border-red-200 bg-red-50 p-3">
                                <p class="text-sm font-medium text-red-800">{{ $variant->product?->name }} - {{ $variant->name }}</p>
                                <p class="text-xs text-red-700">Out of stock</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-3xl bg-white p-6 shadow-soft">
                    <h3 class="mb-4 text-lg font-semibold">Movement History</h3>
                    <div class="space-y-3">
                        @forelse(($recentInventoryMovements ?? collect()) as $movement)
                            <div class="rounded-2xl border border-slate-200 p-3">
                                <p class="text-sm font-medium">{{ $movement->variant?->product?->name }} - {{ $movement->variant?->name }}</p>
                                <p class="text-xs text-slate-600">{{ $movement->quantity_change > 0 ? '+' : '' }}{{ $movement->quantity_change }} ({{ $movement->previous_stock }} -> {{ $movement->new_stock }})</p>
                                <p class="text-xs text-slate-500">{{ ucfirst(str_replace('_', ' ', $movement->type)) }} by {{ $movement->actor?->name ?? 'System' }}</p>
                                @if ($movement->reason)
                                    <p class="text-xs text-slate-500">Reason: {{ $movement->reason }}</p>
                                @endif
                                <p class="text-xs text-slate-400">{{ $movement->created_at?->diffForHumans() }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No inventory movement history yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
