<!-- Products Section -->
<div id="products-section" class="admin-section hidden">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold">Products Management</h1>
            <div class="flex gap-4">
                <a href="{{ route('admin.products.create') }}" class="rounded-2xl bg-pink-600 px-6 py-3 text-sm font-semibold text-white hover:bg-pink-700">Add Product</a>
                <a href="{{ route('admin.categories.create') }}" class="rounded-2xl bg-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-300">Add Categories</a>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        <div class="rounded-3xl bg-white p-6 shadow-soft">
            <div class="mb-6 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="mb-3 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <h2 class="text-lg font-semibold">Bulk Upload (CSV)</h2>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('admin.products.bulk-upload.template.csv') }}" class="rounded-xl bg-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-300">Download CSV Template</a>
                        <a href="{{ route('admin.products.bulk-upload.template.xls') }}" class="rounded-xl bg-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-300">Download Excel Template</a>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.products.bulk-upload') }}" enctype="multipart/form-data" class="flex flex-col gap-3 lg:flex-row lg:items-end">
                    @csrf
                    <div class="flex-1">
                        <label class="mb-1 block text-sm font-medium text-slate-700">CSV File</label>
                        <input type="file" name="csv_file" accept=".csv,.txt" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Mode</label>
                        <select name="mode" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                            <option value="create">Create only</option>
                            <option value="upsert">Create or update (by exact product name)</option>
                        </select>
                    </div>
                    <button type="submit" class="rounded-xl bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Upload CSV</button>
                </form>
                <p class="mt-3 text-xs text-slate-600">
                    Required columns: <code>name</code>, <code>price</code>, <code>category</code>.
                    Optional: <code>description</code>, <code>discount_price</code>, <code>stock_quantity</code>, <code>status</code>, <code>pre_order_days</code>, <code>is_featured</code>, <code>is_best_seller</code>, <code>variant_name</code>, <code>variant_sku</code>, <code>variant_stock_quantity</code>, <code>variant_price_adjustment</code>, <code>variant_is_default</code>, <code>variant_is_active</code>.
                </p>
                <p class="mt-1 text-xs text-slate-500">If variant fields are omitted, a default variant is auto-created.</p>
                <ul class="mt-3 list-disc space-y-1 pl-5 text-xs text-slate-600">
                    <li><code>status</code>: <code>active</code>, <code>inactive</code>, or <code>pre_order</code></li>
                    <li><code>is_featured</code>, <code>is_best_seller</code>, <code>variant_is_default</code>, <code>variant_is_active</code>: use <code>true</code> or <code>false</code></li>
                    <li>In <code>upsert</code> mode, matching is by exact product <code>name</code></li>
                    <li>Category matches by category <code>name</code> or generated <code>slug</code></li>
                </ul>
            </div>

            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold">Product List</h2>
                <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap gap-2">
                    <input
                        type="text"
                        name="product_search"
                        value="{{ $productFilters['search'] ?? '' }}"
                        placeholder="Search products..."
                        class="rounded-2xl border border-slate-200 px-4 py-2 text-sm"
                    />
                    <select name="product_status" class="rounded-2xl border border-slate-200 px-4 py-2 text-sm">
                        <option value="all" {{ ($productFilters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="active" {{ ($productFilters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pre_order" {{ ($productFilters['status'] ?? '') === 'pre_order' ? 'selected' : '' }}>Pre-order</option>
                        <option value="inactive" {{ ($productFilters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <select name="product_category" class="rounded-2xl border border-slate-200 px-4 py-2 text-sm">
                        <option value="">All Categories</option>
                        @foreach($allCategories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ (string) ($productFilters['category'] ?? '') === (string) $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <button class="rounded-2xl bg-slate-700 px-4 py-2 text-sm text-white hover:bg-slate-800">Apply</button>
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
                                    <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-lg object-cover" />
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
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.products.edit', ['product' => $product->id]) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">Edit</a>
                                    <form action="{{ route('admin.products.destroy', ['product' => $product->id]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
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
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.categories.edit', ['category' => $category->id]) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">Edit</a>
                                    <form action="{{ route('admin.categories.destroy', ['category' => $category->id]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
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
