<!-- Products Section -->
<div id="products-section" class="admin-section hidden">
    <div class="space-y-6">
        @php
            $productsCollection = collect($allProducts ?? []);
            if (isset($allProducts) && method_exists($allProducts, 'getCollection')) {
                $productsCollection = $allProducts->getCollection();
            }

            $totalOnPage = $productsCollection->count();
            $activeOnPage = $productsCollection->where('status', 'active')->count();
            $lowStockOnPage = $productsCollection->filter(fn($product) => (int) ($product->stock_quantity ?? 0) > 0 && (int) ($product->stock_quantity ?? 0) <= 10)->count();
            $outOfStockOnPage = $productsCollection->filter(fn($product) => (int) ($product->stock_quantity ?? 0) <= 0)->count();
            $totalProducts = (isset($allProducts) && method_exists($allProducts, 'total')) ? (int) $allProducts->total() : $totalOnPage;
        @endphp

        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
            <div class="space-y-1">
                <h1 class="text-2xl font-semibold tracking-tight text-[#4B2E38]">Products overview</h1>
                <p class="text-sm text-[#8A6A76]">Manage catalog items, stock status, and category alignment</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.products.create') }}" class="rounded-xl bg-[#C47A90] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#B66880]">Add product</a>
                <a href="{{ route('admin.products.bulk-upload.form') }}" class="rounded-xl border border-[#D6B7C3] bg-white px-4 py-2 text-sm font-semibold text-[#6B4957] transition hover:bg-[#FAF1F5]">Bulk upload</a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-[#E9C7D4] bg-gradient-to-br from-[#FFF7FA] to-[#F6DFE9] p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.16em] text-[#8F6172]">Total Products</p>
                <p class="mt-2 text-3xl font-semibold text-[#4D2E38]">{{ $totalProducts }}</p>
                <p class="mt-1 text-xs text-[#8A6A76]">Across all pages</p>
            </div>
            <div class="rounded-2xl border border-[#ECD8E0] bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.16em] text-[#90707A]">Active</p>
                <p class="mt-2 text-3xl font-semibold text-[#4D2E38]">{{ $activeOnPage }}</p>
                <p class="mt-1 text-xs text-[#8A6A76]">Sellable now</p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.16em] text-[#90707A]">Low Stock</p>
                <p class="mt-2 text-3xl font-semibold text-amber-700">{{ $lowStockOnPage }}</p>
                <p class="mt-1 text-xs text-[#8A6A76]">1 to 10 units</p>
            </div>
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.16em] text-[#90707A]">Out of Stock</p>
                <p class="mt-2 text-3xl font-semibold text-rose-600">{{ $outOfStockOnPage }}</p>
                <p class="mt-1 text-xs text-[#8A6A76]">Needs restock</p>
            </div>
        </div>

        <div class="rounded-3xl border border-[#ECD8E0] bg-white p-6 shadow-sm">
            <div class="mb-4 flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-[#4B2E38]">Product list</h2>
                    <p class="text-sm text-[#8A6A76]">Filter and manage products quickly</p>
                </div>

                <form id="products-filter-form" method="GET" action="{{ route('admin.products.index') }}" class="grid w-full gap-2 rounded-2xl border border-[#ECD8E0] bg-[#FFF8FB] p-3 sm:grid-cols-2 xl:w-auto xl:grid-cols-[300px_170px_200px_140px_auto]">
                    <input type="hidden" name="section" value="products">
                    <input type="text" name="product_search" value="{{ $productFilters['search'] ?? '' }}" placeholder="Search products..." class="w-full rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843] focus:border-[#C98A9B] focus:ring-2 focus:ring-[#F5DDE6] focus:outline-none" />

                    <select name="product_status" class="rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843] focus:border-[#C98A9B] focus:ring-2 focus:ring-[#F5DDE6] focus:outline-none">
                        <option value="all" {{ ($productFilters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All statuses</option>
                        <option value="active" {{ ($productFilters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pre_order" {{ ($productFilters['status'] ?? '') === 'pre_order' ? 'selected' : '' }}>Pre-order</option>
                        <option value="inactive" {{ ($productFilters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>

                    <select name="product_category" class="rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843] focus:border-[#C98A9B] focus:ring-2 focus:ring-[#F5DDE6] focus:outline-none">
                        <option value="">All categories</option>
                        @foreach($allCategories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ (string) ($productFilters['category'] ?? '') === (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>

                    <select name="product_per_page" class="rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843] focus:border-[#C98A9B] focus:ring-2 focus:ring-[#F5DDE6] focus:outline-none">
                        @foreach([12, 25, 50] as $size)
                            <option value="{{ $size }}" {{ (int) ($productFilters['per_page'] ?? 12) === $size ? 'selected' : '' }}>Show {{ $size }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="rounded-xl bg-[#C47A90] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#B66880]">Apply</button>
                </form>
            </div>
            <div class="mb-3 flex items-center justify-between text-xs text-[#8A6A76]">
                <p>Stock thresholds: 0 = out of stock, 1-10 = low stock, 11+ = healthy stock.</p>
                <button type="button" id="products-compact-toggle" class="rounded-lg border border-[#E3CDD7] bg-white px-3 py-1.5 font-semibold text-[#6B4A57] hover:bg-[#F7EBF0]">Compact mode</button>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-[#F1E2E8]">
                <table id="products-table" class="w-full min-w-[920px]">
                    <thead class="bg-[#FBF2F6]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Product</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Price / Discount</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Stock</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F4E5EB] bg-white">
                        @forelse($allProducts ?? [] as $product)
                            @php
                                $stock = (int) ($product->stock_quantity ?? 0);
                                $stockClass = $stock <= 0 ? 'bg-rose-50 text-rose-700' : ($stock <= 10 ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700');
                                $statusClass = match($product->status) {
                                    'active' => 'bg-emerald-50 text-emerald-700',
                                    'inactive' => 'bg-slate-100 text-slate-700',
                                    'pre_order' => 'bg-violet-50 text-violet-700',
                                    'draft' => 'bg-amber-50 text-amber-700',
                                    'archived' => 'bg-rose-50 text-rose-700',
                                    default => 'bg-slate-100 text-slate-700'
                                };
                            @endphp
                            <tr class="hover:bg-[#FFFCFD]">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($product->main_image)
                                            <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}" class="h-12 w-12 rounded-xl border border-[#F0E0E7] object-cover" />
                                        @else
                                            <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-[#F0E0E7] bg-[#F8EFF3] text-[#9B7A86]">IMG</div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-semibold text-[#4E303A]">{{ $product->name }}</p>
                                            <p class="text-xs text-[#8A6A76]">ID #{{ $product->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-[#6B4A57]">{{ $product->category->name ?? 'No category' }}</td>
                                <td class="px-4 py-4 text-sm">
                                    <div class="font-semibold text-[#4E303A]">&#8369;{{ number_format((float) $product->effective_price, 2) }}</div>
                                    @if($product->hasDiscount())
                                        <div class="text-xs text-rose-600 line-through">&#8369;{{ number_format((float) $product->price, 2) }}</div>
                                    @endif
                                </td>
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
                                <td class="px-4 py-4 text-sm">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ $product->status === 'pre_order' ? 'Pre-order' : ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.products.edit', ['product' => $product->id]) }}" class="group relative inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#E3CDD7] text-[#6B4A57] hover:bg-[#F7EBF0]" aria-label="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', ['product' => $product->id]) }}" method="POST" class="inline js-confirm-delete-form" data-confirm-title="Delete Product" data-confirm-message="Are you sure you want to delete this product?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 text-rose-700 hover:bg-rose-50" aria-label="Delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-sm text-[#8A6A76]">
                                    No products found. <a href="{{ route('admin.products.create') }}" class="font-semibold text-[#B66880] hover:text-[#9E536A]">Create your first product</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (isset($allProducts) && method_exists($allProducts, 'links'))
                <div class="mt-6">
                    {{ $allProducts->appends([
                        'section' => 'products',
                        'product_search' => $productFilters['search'] ?? null,
                        'product_status' => $productFilters['status'] ?? 'all',
                        'product_category' => $productFilters['category'] ?? null,
                        'product_per_page' => $productFilters['per_page'] ?? 12,
                    ])->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<div id="delete-confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 px-4" aria-hidden="true">
    <div class="w-full max-w-md rounded-2xl border border-[#ECD8E0] bg-white p-6 shadow-lg">
        <h3 id="delete-confirm-title" class="text-lg font-semibold text-[#4B2E38]">Confirm Delete</h3>
        <p id="delete-confirm-message" class="mt-2 text-sm text-[#7E5E6A]">Are you sure you want to continue?</p>
        <div class="mt-6 flex items-center justify-end gap-3">
            <button type="button" id="delete-confirm-cancel" class="rounded-xl border border-[#E5D2DA] bg-white px-4 py-2 text-sm font-semibold text-[#6B4A57] hover:bg-[#F8EFF3]">Cancel</button>
            <button type="button" id="delete-confirm-submit" class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Delete</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('delete-confirm-modal');
        const titleEl = document.getElementById('delete-confirm-title');
        const messageEl = document.getElementById('delete-confirm-message');
        const cancelBtn = document.getElementById('delete-confirm-cancel');
        const submitBtn = document.getElementById('delete-confirm-submit');
        let pendingForm = null;

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');
            pendingForm = null;
        };

        const openModal = (form) => {
            pendingForm = form;
            titleEl.textContent = form.dataset.confirmTitle || 'Confirm Delete';
            messageEl.textContent = form.dataset.confirmMessage || 'Are you sure you want to continue?';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
        };

        document.querySelectorAll('.js-confirm-delete-form').forEach((form) => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                openModal(form);
            });
        });

        cancelBtn?.addEventListener('click', closeModal);
        modal?.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
        submitBtn?.addEventListener('click', () => {
            if (pendingForm) {
                pendingForm.submit();
            }
        });

        const filterForm = document.getElementById('products-filter-form');
        const compactToggle = document.getElementById('products-compact-toggle');
        const productsTable = document.getElementById('products-table');
        let searchTimer = null;

        if (filterForm) {
            filterForm.querySelectorAll('select[name="product_status"], select[name="product_category"], select[name="product_per_page"]').forEach((el) => {
                el.addEventListener('change', () => filterForm.submit());
            });
            const searchInput = filterForm.querySelector('input[name="product_search"]');
            searchInput?.addEventListener('input', () => {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => filterForm.submit(), 300);
            });
        }

        compactToggle?.addEventListener('click', () => {
            productsTable?.classList.toggle('text-xs');
            productsTable?.querySelectorAll('td').forEach((td) => td.classList.toggle('py-2'));
            productsTable?.querySelectorAll('td').forEach((td) => td.classList.toggle('py-4'));
            productsTable?.querySelectorAll('img').forEach((img) => {
                img.classList.toggle('h-10');
                img.classList.toggle('w-10');
                img.classList.toggle('h-12');
                img.classList.toggle('w-12');
            });
        });
    });
</script>
@endpush
