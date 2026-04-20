@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Product: {{ $product->name }}</h1>
        <a href="{{ route('admin.dashboard', ['section' => 'products']) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i>Back to Products
        </a>
    </div>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                <select id="category_id" name="category_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('category_id') border-red-500 @enderror"
                        required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Price -->
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('price') border-red-500 @enderror"
                       required>
                @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Discount Price -->
            <div>
                <label for="discount_price" class="block text-sm font-medium text-gray-700 mb-2">Discount Price</label>
                <input type="number" id="discount_price" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}" step="0.01" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('discount_price') border-red-500 @enderror">
                <p class="mt-1 text-sm text-gray-500">Leave empty if no discount</p>
                @error('discount_price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Stock Quantity -->
            <div>
                <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity *</label>
                <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('stock_quantity') border-red-500 @enderror"
                       required>
                @error('stock_quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pre-order Days -->
            <div>
                <label for="pre_order_days" class="block text-sm font-medium text-gray-700 mb-2">Pre-order Days</label>
                <input type="number" id="pre_order_days" name="pre_order_days" value="{{ old('pre_order_days', $product->pre_order_days) }}" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('pre_order_days') border-red-500 @enderror">
                <p class="mt-1 text-sm text-gray-500">Days needed for production (0 for immediate availability)</p>
                @error('pre_order_days')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea id="description" name="description" rows="4"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Current Main Image -->
        @if($product->main_image)
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Current Main Image</label>
            <div class="flex items-center space-x-4">
                <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}" class="w-20 h-20 object-cover rounded-lg">
                <div>
                    <p class="text-sm text-gray-500">Leave empty to keep current image</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Image -->
        <div>
            <label for="main_image" class="block text-sm font-medium text-gray-700 mb-2">Change Main Image</label>
            <input type="file" id="main_image" name="main_image" accept="image/*"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('main_image') border-red-500 @enderror">
            <p class="mt-1 text-sm text-gray-500">Accepted formats: JPEG, PNG, JPG, GIF. Max size: 2MB</p>
            @error('main_image')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Current Additional Images -->
        @if($product->images->count() > 0)
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Current Additional Images</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($product->images->sortBy('sort_order') as $image)
                <div class="relative">
                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="Product image" class="w-full h-20 object-cover rounded-lg">
                    <button type="button" onclick="deleteImage({{ $image->id }})"
                            class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                        ×
                    </button>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Additional Images -->
        <div>
            <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Add More Images</label>
            <input type="file" id="images" name="images[]" accept="image/*" multiple
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('images.*') border-red-500 @enderror">
            <p class="mt-1 text-sm text-gray-500">Select multiple images to add. Accepted formats: JPEG, PNG, JPG, GIF. Max size: 2MB each</p>
            @error('images.*')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Variants -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-700">Variants *</label>
                <button type="button" id="add-variant-btn" class="rounded-lg bg-slate-700 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                    Add Variant
                </button>
            </div>
            <p class="mb-3 text-xs text-gray-500">You can edit, add, or remove variants. One variant will remain default.</p>

            @error('variants')
                <p class="mb-3 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div id="variants-container" class="space-y-3"></div>
            <template id="variant-template">
                <div class="variant-row rounded-lg border border-gray-200 p-3">
                    <input type="hidden" data-name="id" value="">
                    <input type="hidden" data-name="remove" value="0">
                    <input type="hidden" data-name="is_default" value="0">
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Name</label>
                            <input type="text" data-name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">SKU</label>
                            <input type="text" data-name="sku" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Price Adj.</label>
                            <input type="number" step="0.01" data-name="price_adjustment" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Stock</label>
                            <input type="number" min="0" data-name="stock_quantity" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="inline-flex items-center gap-2 text-xs">
                                <input type="radio" data-default-radio class="h-4 w-4">
                                Default
                            </label>
                            <label class="inline-flex items-center gap-2 text-xs">
                                <input type="hidden" data-name="is_active" value="0">
                                <input type="checkbox" data-active-checkbox class="h-4 w-4" checked>
                                Active
                            </label>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="button" class="remove-variant text-xs text-red-600 hover:text-red-700">Remove</button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Status and Flags -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                <select id="status" name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror"
                        required>
                    <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active (visible to customers)</option>
                    <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive (hidden from customers)</option>
                    <option value="pre_order" {{ old('status', $product->status) == 'pre_order' ? 'selected' : '' }}>Pre-order (available for pre-order)</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_featured" class="ml-2 block text-sm text-gray-900">
                    Featured Product
                </label>
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="is_best_seller" name="is_best_seller" value="1" {{ old('is_best_seller', $product->is_best_seller) ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_best_seller" class="ml-2 block text-sm text-gray-900">
                    Best Seller
                </label>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.dashboard', ['section' => 'products']) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-save mr-2"></i>Update Product
            </button>
        </div>
    </form>
</div>

@php
    $existingVariantsPayload = $product->variants->map(function ($variant) {
        return [
            'id' => $variant->id,
            'name' => $variant->name,
            'sku' => $variant->sku,
            'price_adjustment' => $variant->price_adjustment,
            'stock_quantity' => $variant->stock_quantity,
            'is_default' => (bool) $variant->is_default,
            'is_active' => (bool) $variant->is_active,
        ];
    })->values()->all();
@endphp

<script>
(() => {
    const container = document.getElementById('variants-container');
    const addBtn = document.getElementById('add-variant-btn');
    const template = document.getElementById('variant-template');
    const oldVariants = @json(old('variants'));
    const existingVariants = @json($existingVariantsPayload);
    const initialVariants = Array.isArray(oldVariants) ? oldVariants : existingVariants;

    const renumber = () => {
        const rows = container.querySelectorAll('.variant-row');
        rows.forEach((row, index) => {
            row.querySelectorAll('[data-name]').forEach((input) => {
                input.name = `variants[${index}][${input.dataset.name}]`;
            });

            const radio = row.querySelector('[data-default-radio]');
            if (radio) {
                radio.name = 'variant_default_selector';
                radio.addEventListener('change', () => {
                    if (radio.checked) {
                        rows.forEach((otherRow) => {
                            const defaultInput = otherRow.querySelector('[data-name="is_default"]');
                            const removeInput = otherRow.querySelector('[data-name="remove"]');
                            if (!defaultInput || !removeInput || removeInput.value === '1') return;
                            defaultInput.value = otherRow === row ? '1' : '0';
                        });
                    }
                });
            }

            const activeCheckbox = row.querySelector('[data-active-checkbox]');
            if (activeCheckbox) {
                activeCheckbox.addEventListener('change', () => {
                    const activeInput = row.querySelector('[data-name="is_active"]');
                    if (activeInput) activeInput.value = activeCheckbox.checked ? '1' : '0';
                });
            }
        });

        const activeRows = [...rows].filter((row) => row.querySelector('[data-name="remove"]').value !== '1');
        const defaultInputs = activeRows.map((row) => row.querySelector('[data-name="is_default"]'));
        if (defaultInputs.length > 0 && defaultInputs.every((input) => input.value !== '1')) {
            defaultInputs[0].value = '1';
            const radio = activeRows[0].querySelector('[data-default-radio]');
            if (radio) radio.checked = true;
        }
    };

    const addRow = (data = {}) => {
        const clone = template.content.firstElementChild.cloneNode(true);
        clone.querySelector('[data-name="id"]').value = data.id ?? '';
        clone.querySelector('[data-name="name"]').value = data.name ?? '';
        clone.querySelector('[data-name="sku"]').value = data.sku ?? '';
        clone.querySelector('[data-name="price_adjustment"]').value = data.price_adjustment ?? 0;
        clone.querySelector('[data-name="stock_quantity"]').value = data.stock_quantity ?? 0;
        clone.querySelector('[data-name="is_default"]').value = data.is_default ? '1' : '0';
        clone.querySelector('[data-name="is_active"]').value = (data.is_active ?? 1) ? '1' : '0';
        clone.querySelector('[data-default-radio]').checked = !!data.is_default;
        clone.querySelector('[data-active-checkbox]').checked = (data.is_active ?? 1) ? true : false;

        clone.querySelector('.remove-variant').addEventListener('click', () => {
            const idValue = clone.querySelector('[data-name="id"]').value;
            if (idValue) {
                clone.querySelector('[data-name="remove"]').value = '1';
                clone.classList.add('hidden');
            } else {
                clone.remove();
            }
            renumber();
        });

        container.appendChild(clone);
        renumber();
    };

    addBtn.addEventListener('click', () => addRow());
    if (initialVariants.length === 0) {
        addRow({ is_default: true, is_active: true });
    } else {
        initialVariants.forEach((variant) => addRow(variant));
    }
})();

function deleteImage(imageId) {
    if (confirm('Are you sure you want to delete this image?')) {
        fetch(`/admin/products/images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to delete image');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete image');
        });
    }
}
</script>
@endsection
