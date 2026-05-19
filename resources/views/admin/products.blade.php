<!-- Products Section -->
<div id="products-section" class="admin-section hidden">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-3xl font-bold">Products Management</h1>
        <p class="mt-1 text-sm text-slate-500">Manage your product catalog and categories</p>
    </div>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.products.create') }}" class="rounded-2xl bg-pink-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-pink-700 transition-all duration-200 shadow-md">
            Add Single Product
        </a>
        <a href="{{ route('admin.products.bulk-upload.form') }}" class="rounded-2xl px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#330A02] transition-all duration-200 shadow-md" style="background-color: #440E03;">
            Bulk Upload Products
        </a>
        <a href="{{ route('admin.categories.create') }}" class="rounded-2xl bg-pink-50 px-5 py-2.5 text-sm font-semibold hover:bg-pink-100 transition-all duration-200 shadow-md" style="color: #440E03;">
            Add Categories
        </a>
    </div>
</div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        <div class="rounded-3xl bg-white p-6 shadow-soft">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold">Product List</h2>
                <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap gap-3">
                    <input
                        type="text"
                        name="product_search"
                        value="{{ $productFilters['search'] ?? '' }}"
                        placeholder="Search products..."
                        class="w-80 rounded-2xl border border-slate-200 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-200 focus:outline-none transition-all duration-200"
                    />
                    <select name="product_status" class="rounded-2xl border border-slate-200 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-200 focus:outline-none transition-all duration-200">
                        <option value="all" {{ ($productFilters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="active" {{ ($productFilters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pre_order" {{ ($productFilters['status'] ?? '') === 'pre_order' ? 'selected' : '' }}>Pre-order</option>
                        <option value="inactive" {{ ($productFilters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <select name="product_category" class="rounded-2xl border border-slate-200 px-4 py-2.5 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-200 focus:outline-none transition-all duration-200">
                        <option value="">All Categories</option>
                        @foreach($allCategories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ (string) ($productFilters['category'] ?? '') === (string) $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                        <button type="submit" class="relative group rounded-2xl bg-pink-600 p-2.5 text-white hover:bg-pink-700 transition-all duration-200 shadow-md" aria-label="Apply Filters">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12l5 5L20 7" />
                            </svg>
                            <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-900 text-white text-xs px-2 py-1 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity">Apply Filters</span>
                        </button>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Image</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Name</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Category</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Price</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Stock</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($allProducts ?? [] as $product)
                        <tr>
                            <td class="px-4 py-4">
                                @if($product->main_image)
                                    <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-lg object-cover" />
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm font-medium">{{ $product->name }}</td>
                            <td class="px-4 py-4 text-sm text-slate-500">{{ $product->category->name ?? 'No Category' }}</td>
                            <td class="px-4 py-4 text-sm">
                                &#8369;{{ number_format((float) $product->effective_price, 2) }}
                                @if($product->hasDiscount())
                                    <span class="text-red-500 line-through text-xs">&#8369;{{ number_format((float) $product->price, 2) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm">
                                <span class="{{ $product->stock_quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $product->stock_quantity }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs
                                    @if($product->status === 'active') bg-green-100 text-green-800
                                    @elseif($product->status === 'inactive') bg-red-100 text-red-800
                                    @elseif($product->status === 'pre_order') bg-yellow-100 text-yellow-800
                                    @endif">
                                    @if($product->status === 'active') Active
                                    @elseif($product->status === 'inactive') Inactive
                                    @elseif($product->status === 'pre_order') Pre-order
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex gap-3">
                                    <a href="{{ route('admin.products.edit', ['product' => $product->id]) }}" class="group relative text-slate-600 hover:text-slate-800 transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-900 text-white text-xs px-2 py-1 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity">Edit</span>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', ['product' => $product->id]) }}" method="POST" class="inline js-confirm-delete-form" data-confirm-title="Delete Product" data-confirm-message="Are you sure you want to delete this product?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="group relative text-red-500 hover:text-red-700 transition-colors" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-900 text-white text-xs px-2 py-1 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity">Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-box-open text-4xl mb-2"></i>
                                    <p>No products found. <a href="{{ route('admin.products.create') }}" class="text-blue-600 hover:text-blue-800">Create your first product</a></p>
                                </div>
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
                    ])->links() }}
                </div>
            @endif
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-soft">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold">Category List</h2>
                <a href="{{ route('admin.categories.create') }}" class="rounded-2xl bg-slate-100 px-4 py-2 text-sm text-slate-700 hover:bg-slate-200">Add Category</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Image</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Name</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Parent</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($allCategories ?? [] as $category)
                        <tr>
                            <td class="px-4 py-4">
                                @if($category->image)
                                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-12 h-12 rounded-lg object-cover" />
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm font-medium">{{ $category->name }}</td>
                            <td class="px-4 py-4 text-sm text-slate-500">{{ $category->parent?->name ?? 'None' }}</td>
                            <td class="px-4 py-4 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs {{ $category->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $category->status ? 'Active' : 'Inactive' }}
                                </span>
                                                        </td>
                            <td class="px-4 py-4">
                                <div class="flex gap-3">
                                    <a href="{{ route('admin.categories.edit', ['category' => $category->id]) }}" class="group relative text-slate-600 hover:text-slate-800 transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-900 text-white text-xs px-2 py-1 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity">Edit</span>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', ['category' => $category->id]) }}" method="POST" class="inline js-confirm-delete-form" data-confirm-title="Delete Category" data-confirm-message="Are you sure you want to delete this category?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="group relative text-red-500 hover:text-red-700 transition-colors" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-900 text-white text-xs px-2 py-1 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity">Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                No categories found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="delete-confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 px-4" aria-hidden="true">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-soft">
        <h3 id="delete-confirm-title" class="text-lg font-semibold text-slate-900">Confirm Delete</h3>
        <p id="delete-confirm-message" class="mt-2 text-sm text-slate-600">Are you sure you want to continue?</p>
        <div class="mt-6 flex items-center justify-end gap-3">
            <button type="button" id="delete-confirm-cancel" class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">
                Cancel
            </button>
            <button type="button" id="delete-confirm-submit" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                Delete
            </button>
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
    });
</script>
@endpush
