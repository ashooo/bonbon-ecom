@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<style>
    .product-shell {
        border: 1px solid #dfe5ec;
        background: #f4f7fb;
        box-shadow: 0 12px 28px rgba(48, 64, 82, 0.08);
    }
    .product-section {
        border: 1px solid #d9e2ec;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(52, 68, 86, 0.04);
    }
    .product-section-title {
        color: #132a4a;
        font-weight: 700;
        letter-spacing: -0.01em;
    }
    .product-pill {
        border: 1px solid #ead3df;
        background: #fdf3f8;
        color: #b84f7d;
    }
    .editor-grid {
        display: grid;
        gap: 1rem;
        grid-template-columns: minmax(0, 1fr);
    }
    .section-core,
    .section-side {
        grid-column: 1;
    }
    .upload-zone {
        border: 1.5px dashed #d7b7c8;
        background: #fff9fc;
    }
    .sticky-savebar {
        position: sticky;
        bottom: 12px;
        z-index: 30;
        border: 1px solid #efdae3;
        background: rgba(255, 255, 255, 0.94);
        backdrop-filter: blur(6px);
    }
</style>

<div class="product-shell rounded-3xl p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#996f80]">Catalog Editor</p>
            <h1 class="mt-1 text-3xl font-semibold text-[#4B2E38]">Edit Product: {{ $product->name }}</h1>
        </div>
        <a href="{{ route('admin.dashboard', ['section' => 'products']) }}" class="rounded-xl border border-[#D6B7C3] bg-white px-4 py-2 text-sm font-semibold text-[#6B4957] hover:bg-[#FAF1F5] transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Products
        </a>
    </div>

    <form id="edit-product-form" action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        <div class="editor-grid">
        <section class="product-section section-core rounded-2xl p-5">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-[220px_1fr]">
                <div class="rounded-2xl border border-[#EAD5DD] bg-white p-2">
                    <div class="aspect-square overflow-hidden rounded-xl bg-[#F9EDF2]">
                        @if($product->main_image_url)
                            <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-sm font-semibold text-[#9A7683]">No image</div>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.16em] text-[#946979]">Product Snapshot</p>
                    <h3 class="mt-1 text-2xl font-semibold text-[#4B2E38]">{{ $product->name }}</h3>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-[#F7E8EE] px-3 py-1 text-xs font-semibold text-[#7A525F]">{{ $product->category->name ?? 'No category' }}</span>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $product->status === 'active' ? 'bg-emerald-50 text-emerald-700' : ($product->status === 'pre_order' ? 'bg-[#F6EAF0] text-[#8A6070]' : 'bg-slate-100 text-slate-700') }}">{{ ucfirst(str_replace('_', ' ', $product->status)) }}</span>
                        <span class="rounded-full bg-[#F7E8EE] px-3 py-1 text-xs font-semibold text-[#7A525F]">{{ (int) $product->stock_quantity }} in stock</span>
                    </div>
                    <div class="mt-4 flex items-end gap-3">
                        <p class="text-3xl font-bold text-[#5A3A3A]">&#8369;{{ number_format((float) $product->effective_price, 2) }}</p>
                        @if($product->hasDiscount())
                            <p class="pb-1 text-sm text-slate-400 line-through">&#8369;{{ number_format((float) $product->price, 2) }}</p>
                        @endif
                    </div>
                    <p class="mt-3 text-sm text-[#7E5D69]">{{ \Illuminate\Support\Str::limit($product->description ?: 'No description provided yet.', 140) }}</p>
                </div>
            </div>
        </section>

        <section class="product-section section-core rounded-2xl p-5">
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-pink-100 text-pink-700">
                        <i class="fas fa-tag text-xs"></i>
                    </span>
                    <div>
                        <h2 class="product-section-title text-base">General Information</h2>
                        <p class="text-xs text-[#8A6A76]">Core product details customers see first.</p>
                    </div>
                </div>
                <span class="product-pill rounded-full px-3 py-1 text-xs font-semibold">Required fields</span>
            </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
                <label for="name" class="mb-2 block text-sm font-medium text-[#6B4A57]">Product Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
                       class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F5DDE6] focus:border-[#C98A9B] @error('name') border-red-500 @enderror"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div>
                <label for="category_id" class="mb-2 block text-sm font-medium text-[#6B4A57]">Category *</label>
                <select id="category_id" name="category_id"
                        class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F5DDE6] focus:border-[#C98A9B] @error('category_id') border-red-500 @enderror"
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
                <label for="price" class="mb-2 block text-sm font-medium text-[#6B4A57]">Price *</label>
                <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0"
                       class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F5DDE6] focus:border-[#C98A9B] @error('price') border-red-500 @enderror"
                       required>
                @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Discount Price -->
            <div>
                <label for="discount_price" class="mb-2 block text-sm font-medium text-[#6B4A57]">Discount Price</label>
                <input type="number" id="discount_price" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}" step="0.01" min="0"
                       class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F5DDE6] focus:border-[#C98A9B] @error('discount_price') border-red-500 @enderror">
                <p class="mt-1 text-sm text-[#8A6A76]">Leave empty if no discount</p>
                <p id="discount-insight" class="mt-1 text-xs font-semibold text-[#8A6070]"></p>
                @error('discount_price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Stock Quantity -->
            <div>
                <label for="stock_quantity" class="mb-2 block text-sm font-medium text-[#6B4A57]">Stock Quantity *</label>
                <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0"
                       class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F5DDE6] focus:border-[#C98A9B] @error('stock_quantity') border-red-500 @enderror"
                       required>
                @error('stock_quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pre-order Days -->
            <div>
                <label for="pre_order_days" class="mb-2 block text-sm font-medium text-[#6B4A57]">Pre-order Days</label>
                <input type="number" id="pre_order_days" name="pre_order_days" value="{{ old('pre_order_days', $product->pre_order_days) }}" min="0"
                       class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F5DDE6] focus:border-[#C98A9B] @error('pre_order_days') border-red-500 @enderror">
                <p class="mt-1 text-sm text-[#8A6A76]">Days needed for production (0 for immediate availability)</p>
                @error('pre_order_days')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        </section>

        <!-- Description -->
        <section class="product-section section-core rounded-2xl p-5">
            <div class="mb-3 flex items-center gap-3">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-rose-100 text-rose-700">
                    <i class="fas fa-align-left text-xs"></i>
                </span>
                <h2 class="product-section-title text-base">Description</h2>
            </div>
        <div>
            <label for="description" class="mb-2 block text-sm font-medium text-[#6B4A57]">Description</label>
            <textarea id="description" name="description" rows="4"
                      class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F5DDE6] focus:border-[#C98A9B] @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        </section>

        <!-- Current Main Image -->
        @if($product->main_image_url)
        <section class="product-section section-side rounded-2xl p-5">
        <div>
            <label class="mb-2 block text-sm font-medium text-[#6B4A57]">Current Main Image</label>
            <div class="flex items-center space-x-4">
                <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}" class="w-20 h-20 object-cover rounded-lg">
                <div>
                    <p class="text-sm text-[#8A6A76]">Leave empty to keep current image</p>
                </div>
            </div>
        </div>
        </section>
        @endif

        <!-- Main Image -->
        <section class="product-section section-side rounded-2xl p-5">
            <div class="mb-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-fuchsia-100 text-fuchsia-700">
                        <i class="fas fa-images text-xs"></i>
                    </span>
                    <h2 class="product-section-title text-base">Product Media</h2>
                </div>
                <span class="product-pill rounded-full px-3 py-1 text-xs font-semibold">Drag to reorder</span>
            </div>
        <div>
            <label for="main_image" class="mb-2 block text-sm font-medium text-[#6B4A57]">Change Main Image</label>
            <label for="main_image" class="upload-zone block cursor-pointer rounded-xl px-4 py-5 text-center text-sm font-medium text-[#7A5252]">
                <span class="block text-base font-semibold text-[#5D3A47]">Drop image here or click to upload</span>
                <span class="mt-1 block text-xs text-[#8A6A76]">JPEG, PNG, JPG, GIF up to 2MB</span>
            </label>
            <input type="file" id="main_image" name="main_image" accept="image/*" class="sr-only @error('main_image') border-red-500 @enderror">
            <p class="mt-1 text-sm text-[#8A6A76]">Accepted formats: JPEG, PNG, JPG, GIF, WEBP. Max size: 2MB</p>
            <p id="main-image-selected" class="mt-1 text-xs font-medium text-[#7A5252] hidden"></p>
            @error('main_image')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Current Additional Images -->
        @if($product->images->count() > 0)
        <div class="mt-4">
            <div class="mb-2 flex items-center justify-between gap-3">
                <label class="block text-sm font-medium text-[#6B4A57]">Current Additional Images</label>
                <div class="flex items-center gap-3">
                    <span id="image-order-status" class="hidden text-xs text-[#8A6A76]"></span>
                    <button
                        type="button"
                        id="save-image-order-btn"
                        class="hidden rounded-xl bg-[#C47A90] px-3 py-2 text-xs font-semibold text-white hover:bg-[#B66880]"
                    >
                        Save Image Order
                    </button>
                </div>
            </div>
            <p class="mb-3 text-xs text-[#8A6A76]">Drag and drop to reorder product gallery images.</p>
            <div id="image-sortable-grid" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($product->images->sortBy('sort_order') as $image)
                <div class="relative cursor-move rounded-lg border border-transparent" draggable="true" data-image-id="{{ $image->id }}">
                    <img src="{{ $image->image_url }}" alt="Product image" class="w-full h-20 object-cover rounded-lg">
                    <button type="button" onclick="deleteImage({{ $image->id }})"
                            class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                        &times;
                    </button>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Additional Images -->
        <div>
            <label for="images" class="mb-2 block text-sm font-medium text-[#6B4A57]">Add More Images</label>
            <label for="images" class="upload-zone block cursor-pointer rounded-xl px-4 py-5 text-center text-sm font-medium text-[#7A5252]">
                <span class="block text-base font-semibold text-[#5D3A47]">Drop additional images or click to upload</span>
                <span class="mt-1 block text-xs text-[#8A6A76]">Multiple files supported</span>
            </label>
            <input type="file" id="images" name="images[]" accept="image/*" multiple class="sr-only @error('images.*') border-red-500 @enderror">
            <p class="mt-1 text-sm text-[#8A6A76]">Select multiple images to add. Accepted formats: JPEG, PNG, JPG, GIF, WEBP. Max size: 2MB each</p>
            <p id="additional-images-selected" class="mt-1 text-xs font-medium text-[#7A5252] hidden"></p>
            <div id="additional-images-preview" class="mt-3 hidden grid grid-cols-3 gap-2 md:grid-cols-6"></div>
            @error('images.*')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        </section>

        <!-- Variants -->
        <section class="product-section section-side rounded-2xl p-5">
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#F3D5E0] text-[#7A5252]">
                        <i class="fas fa-layer-group text-xs"></i>
                    </span>
                    <div>
                        <label class="block text-sm font-medium text-[#6B4A57]">Variants *</label>
                        <p class="text-xs text-[#8A6A76]">Set SKU-level stock and pricing.</p>
                    </div>
                </div>
                <button type="button" id="add-variant-btn" class="rounded-lg bg-[#C47A90] px-3 py-2 text-xs font-semibold text-white hover:bg-[#B66880]">
                    Add Variant
                </button>
            </div>
            <p class="mb-3 text-xs text-[#8A6A76]">You can edit, add, or remove variants. One variant will remain default.</p>

            @error('variants')
                <p class="mb-3 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div id="variants-container" class="space-y-3"></div>
            <template id="variant-template">
                <div class="variant-row rounded-xl border border-[#ECD8E0] bg-[#FFFCFD] p-3">
                    <div class="mb-2 flex items-center justify-between">
                        <p class="variant-title text-xs font-semibold uppercase tracking-wide text-[#8A6070]">Variant</p>
                        <span class="variant-zero-warning hidden rounded-full bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-700">Price looks 0.00</span>
                    </div>
                    <input type="hidden" data-name="id" value="">
                    <input type="hidden" data-name="remove" value="0">
                    <input type="hidden" data-name="is_default" value="0">
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-[#8A6A76]">Name</label>
                            <input type="text" data-name="name" class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl text-sm" required>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-[#8A6A76]">SKU</label>
                            <input type="text" data-name="sku" class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl text-sm" required>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-[#8A6A76]">Variant Price</label>
                            <input type="number" step="0.01" data-name="price_adjustment" value="0" class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-[#8A6A76]">Stock</label>
                            <input type="number" min="0" data-name="stock_quantity" value="0" class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-[#8A6A76]">Variant Image</label>
                            <input type="file" data-name="image" accept="image/*" class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl text-sm">
                            <div class="mt-2 hidden items-center gap-2 text-xs text-[#8A6A76]" data-image-preview-wrap>
                                <img src="" alt="Variant image" class="h-12 w-12 rounded object-cover" data-image-preview>
                                <span>Current image</span>
                            </div>
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
                        <button type="button" class="remove-variant text-xs text-rose-600 hover:text-rose-700">Remove</button>
                    </div>
                </div>
            </template>
        </div>
        </section>

        <!-- Status and Flags -->
        <section class="product-section section-core rounded-2xl p-5">
            <div class="mb-3 flex items-center gap-3">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                    <i class="fas fa-bullhorn text-xs"></i>
                </span>
                <h2 class="product-section-title text-base">Publishing</h2>
            </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="status" class="mb-2 block text-sm font-medium text-[#6B4A57]">Status *</label>
                <select id="status" name="status"
                        class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F5DDE6] focus:border-[#C98A9B] @error('status') border-red-500 @enderror"
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
                       class="h-4 w-4 rounded border-[#D8C1CB] text-[#C47A90] focus:ring-[#F5DDE6]">
                <label for="is_featured" class="ml-2 block text-sm text-[#4E303A]">
                    Featured Product
                </label>
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="is_best_seller" name="is_best_seller" value="1" {{ old('is_best_seller', $product->is_best_seller) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-[#D8C1CB] text-[#C47A90] focus:ring-[#F5DDE6]">
                <label for="is_best_seller" class="ml-2 block text-sm text-[#4E303A]">
                    Best Seller
                </label>
            </div>
        </div>
        </section>

        </div>

        <!-- Submit Buttons -->
        <div class="sticky-savebar rounded-2xl px-4 py-3">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-medium text-[#6B4A57]">Changes are saved together, including image order.</p>
                <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.dashboard', ['section' => 'products']) }}" class="rounded-xl border border-[#D6B7C3] bg-white px-6 py-2 text-[#6B4957] hover:bg-[#FAF1F5]">
                Cancel
            </a>
            <button type="submit" class="rounded-xl bg-[#C47A90] px-6 py-2 text-white hover:bg-[#B66880]">
                <i class="fas fa-save mr-2"></i>Update Product
            </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="delete-image-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#2E1D25]/55 p-4">
    <div class="w-full max-w-sm rounded-2xl border border-[#EAD5DD] bg-white p-5 shadow-2xl">
        <h3 class="text-lg font-semibold text-[#4B2E38]">Delete Image?</h3>
        <p class="mt-2 text-sm text-[#7A5A67]">This will remove the selected image from this product.</p>
        <div class="mt-5 flex justify-end gap-3">
            <button
                type="button"
                id="delete-image-cancel-btn"
                class="rounded-xl border border-[#D6B7C3] bg-white px-4 py-2 text-sm font-semibold text-[#6B4957] hover:bg-[#FAF1F5]">
                Cancel
            </button>
            <button
                type="button"
                id="delete-image-confirm-btn"
                class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">
                Delete
            </button>
        </div>
    </div>
</div>

@php
    $existingVariantsPayload = $product->variants->map(function ($variant) {
        return [
            'id' => $variant->id,
            'name' => $variant->name,
            'sku' => $variant->sku,
            'price_adjustment' => $variant->price_adjustment,
            'stock_quantity' => $variant->stock_quantity,
            'image_url' => $variant->image_url,
            'is_default' => (bool) $variant->is_default,
            'is_active' => (bool) $variant->is_active,
        ];
    })->values()->all();
@endphp

<script>
let pendingDeleteImageId = null;

function openDeleteImageModal(imageId) {
    pendingDeleteImageId = imageId;
    const modal = document.getElementById('delete-image-modal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeDeleteImageModal() {
    pendingDeleteImageId = null;
    const modal = document.getElementById('delete-image-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

(() => {
    const mainImageInput = document.getElementById('main_image');
    const mainImageSelected = document.getElementById('main-image-selected');
    const heroPreview = document.querySelector('.aspect-square img');
    const additionalImagesInput = document.getElementById('images');
    const additionalImagesSelected = document.getElementById('additional-images-selected');
    const additionalImagesPreview = document.getElementById('additional-images-preview');

    mainImageInput?.addEventListener('change', () => {
        const file = mainImageInput.files?.[0];
        if (!file) {
            mainImageSelected?.classList.add('hidden');
            return;
        }

        if (mainImageSelected) {
            mainImageSelected.textContent = `Selected: ${file.name}`;
            mainImageSelected.classList.remove('hidden');
        }

        if (heroPreview && file.type.startsWith('image/')) {
            heroPreview.src = URL.createObjectURL(file);
        }
    });

    additionalImagesInput?.addEventListener('change', () => {
        const files = [...(additionalImagesInput.files ?? [])];
        const count = files.length;
        if (!additionalImagesSelected) return;
        if (count <= 0) {
            additionalImagesSelected.classList.add('hidden');
            additionalImagesSelected.textContent = '';
            if (additionalImagesPreview) {
                additionalImagesPreview.classList.add('hidden');
                additionalImagesPreview.innerHTML = '';
            }
            return;
        }
        additionalImagesSelected.textContent = `${count} additional image${count > 1 ? 's' : ''} selected`;
        additionalImagesSelected.classList.remove('hidden');

        if (additionalImagesPreview) {
            additionalImagesPreview.innerHTML = '';
            files.forEach((file) => {
                if (!file.type.startsWith('image/')) return;
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = file.name;
                img.className = 'h-16 w-16 rounded-lg border border-[#E7D2DA] object-cover';
                additionalImagesPreview.appendChild(img);
            });
            additionalImagesPreview.classList.toggle('hidden', additionalImagesPreview.children.length === 0);
        }
    });

    const container = document.getElementById('variants-container');
    const addBtn = document.getElementById('add-variant-btn');
    const template = document.getElementById('variant-template');
    const oldVariants = @json(old('variants'));
    const existingVariants = @json($existingVariantsPayload);
    const initialVariants = Array.isArray(oldVariants) ? oldVariants : existingVariants;
    const basePriceInput = document.getElementById('price');
    const discountPriceInput = document.getElementById('discount_price');
    const discountInsight = document.getElementById('discount-insight');
    const updateDiscountInsight = () => {
        if (!basePriceInput || !discountPriceInput || !discountInsight) return;
        const price = parseFloat(basePriceInput.value || 0);
        const discount = parseFloat(discountPriceInput.value || 0);
        if (!price || !discount || discount >= price) {
            discountInsight.textContent = '';
            return;
        }
        const saveAmount = price - discount;
        const savePct = (saveAmount / price) * 100;
        discountInsight.textContent = `Save ₱${saveAmount.toFixed(2)} (${savePct.toFixed(1)}% off)`;
    };

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

            const nameInput = row.querySelector('[data-name="name"]');
            const priceInput = row.querySelector('[data-name="price_adjustment"]');
            const titleEl = row.querySelector('.variant-title');
            const warningEl = row.querySelector('.variant-zero-warning');
            const refreshCardMeta = () => {
                if (titleEl) {
                    const name = (nameInput?.value || '').trim();
                    titleEl.textContent = name ? `Variant: ${name}` : `Variant ${index + 1}`;
                }
                const price = parseFloat(priceInput?.value || 0);
                if (warningEl) {
                    warningEl.classList.toggle('hidden', price > 0);
                }
            };
            nameInput?.addEventListener('input', refreshCardMeta);
            priceInput?.addEventListener('input', refreshCardMeta);
            refreshCardMeta();

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
        const previewWrap = clone.querySelector('[data-image-preview-wrap]');
        const previewImage = clone.querySelector('[data-image-preview]');
        if (previewWrap && previewImage && data.image_url) {
            previewImage.src = data.image_url;
            previewWrap.classList.remove('hidden');
            previewWrap.classList.add('flex');
        }
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
    updateDiscountInsight();
    basePriceInput?.addEventListener('input', updateDiscountInsight);
    discountPriceInput?.addEventListener('input', updateDiscountInsight);
})();

function deleteImage(imageId) {
    openDeleteImageModal(imageId);
}

(() => {
    const imageGrid = document.getElementById('image-sortable-grid');
    if (!imageGrid) return;

    const saveBtn = document.getElementById('save-image-order-btn');
    const statusEl = document.getElementById('image-order-status');
    const orderEndpoint = @json(route('admin.products.images.order'));
    const editForm = document.getElementById('edit-product-form');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    let draggedItem = null;
    let isSaving = false;

    const setStatus = (message, tone = 'neutral') => {
        if (!statusEl) return;
        statusEl.textContent = message;
        statusEl.classList.remove('hidden', 'text-[#8A6A76]', 'text-green-600', 'text-red-600');
        if (tone === 'success') {
            statusEl.classList.add('text-green-600');
        } else if (tone === 'error') {
            statusEl.classList.add('text-red-600');
        } else {
            statusEl.classList.add('text-[#8A6A76]');
        }
    };

    const clearStatusLater = () => {
        setTimeout(() => {
            if (!statusEl) return;
            statusEl.classList.add('hidden');
            statusEl.textContent = '';
        }, 2500);
    };

    const imageItems = () => [...imageGrid.querySelectorAll('[data-image-id]')];

    const collectOrderPayload = () => ({
        images: imageItems().map((item, index) => ({
            id: Number(item.dataset.imageId),
            sort_order: index,
        })),
    });

    const persistImageOrder = async () => {
        if (isSaving) return;
        isSaving = true;
        if (saveBtn) saveBtn.disabled = true;
        setStatus('Saving order...', 'neutral');

        try {
            const response = await fetch(orderEndpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(collectOrderPayload()),
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            if (!data.success) {
                throw new Error('Reorder failed');
            }

            setStatus('Image order saved.', 'success');
            clearStatusLater();
        } catch (error) {
            console.error(error);
            setStatus('Failed to save image order.', 'error');
        } finally {
            isSaving = false;
            if (saveBtn) saveBtn.disabled = false;
        }
    };

    imageItems().forEach((item) => {
        item.addEventListener('dragstart', () => {
            draggedItem = item;
            item.classList.add('opacity-50');
        });

        item.addEventListener('dragend', () => {
            item.classList.remove('opacity-50');
            draggedItem = null;
        });

        item.addEventListener('dragover', (event) => {
            event.preventDefault();
        });

        item.addEventListener('drop', async (event) => {
            event.preventDefault();
            if (!draggedItem || draggedItem === item) return;

            const allItems = imageItems();
            const draggedIndex = allItems.indexOf(draggedItem);
            const targetIndex = allItems.indexOf(item);

            if (draggedIndex < targetIndex) {
                item.after(draggedItem);
            } else {
                item.before(draggedItem);
            }

            await persistImageOrder();
        });
    });

    saveBtn?.addEventListener('click', async () => {
        await persistImageOrder();
    });

    editForm?.addEventListener('submit', async (event) => {
        if (!imageItems().length) return;
        event.preventDefault();
        await persistImageOrder();
        editForm.submit();
    });
})();

(() => {
    const modal = document.getElementById('delete-image-modal');
    const cancelBtn = document.getElementById('delete-image-cancel-btn');
    const confirmBtn = document.getElementById('delete-image-confirm-btn');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    cancelBtn?.addEventListener('click', closeDeleteImageModal);
    modal?.addEventListener('click', (event) => {
        if (event.target === modal) closeDeleteImageModal();
    });

    confirmBtn?.addEventListener('click', async () => {
        if (!pendingDeleteImageId) return;

        try {
            const response = await fetch(`/admin/products/images/${pendingDeleteImageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();
            if (data.success) {
                location.reload();
                return;
            }
            alert('Failed to delete image');
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to delete image');
        } finally {
            closeDeleteImageModal();
        }
    });
})();
</script>
@endsection
