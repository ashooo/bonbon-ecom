@extends('layouts.admin')

@section('title', 'Add Product')

@section('content')
<div class="rounded-3xl border border-[#ECD8E0] bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-[#4B2E38]">Add New Product</h1>
        <a href="{{ route('admin.dashboard', ['section' => 'products']) }}" class="rounded-xl border border-[#D6B7C3] bg-white px-4 py-2 text-sm font-semibold text-[#6B4957] hover:bg-[#FAF1F5] transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Products
        </a>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <section class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
            <h2 class="mb-4 text-base font-semibold text-slate-800">General information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
                <label for="name" class="mb-2 block text-sm font-medium text-[#6B4A57]">Product Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
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
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0"
                       class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F5DDE6] focus:border-[#C98A9B] @error('price') border-red-500 @enderror"
                       required>
                @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Discount Price -->
            <div>
                <label for="discount_price" class="mb-2 block text-sm font-medium text-[#6B4A57]">Discount Price</label>
                <input type="number" id="discount_price" name="discount_price" value="{{ old('discount_price') }}" step="0.01" min="0"
                       class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F5DDE6] focus:border-[#C98A9B] @error('discount_price') border-red-500 @enderror">
                <p class="mt-1 text-sm text-[#8A6A76]">Leave empty if no discount</p>
                @error('discount_price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Stock Quantity -->
            <div>
                <label for="stock_quantity" class="mb-2 block text-sm font-medium text-[#6B4A57]">Stock Quantity *</label>
                <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0"
                       class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F5DDE6] focus:border-[#C98A9B] @error('stock_quantity') border-red-500 @enderror"
                       required>
                @error('stock_quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pre-order Days -->
            <div>
                <label for="pre_order_days" class="mb-2 block text-sm font-medium text-[#6B4A57]">Pre-order Days</label>
                <input type="number" id="pre_order_days" name="pre_order_days" value="{{ old('pre_order_days') }}" min="0"
                       class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F5DDE6] focus:border-[#C98A9B] @error('pre_order_days') border-red-500 @enderror">
                <p class="mt-1 text-sm text-[#8A6A76]">Days needed for production (0 for immediate availability)</p>
                @error('pre_order_days')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        </section>

        <!-- Description -->
        <section class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
            <h2 class="mb-3 text-base font-semibold text-slate-800">Description</h2>
        <div>
            <label for="description" class="mb-2 block text-sm font-medium text-[#6B4A57]">
                Description <span class="text-red-500">*</span>
            </label>

            <textarea id="description" name="description" rows="4"
                    required
                    class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F5DDE6] focus:border-[#C98A9B] @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>

            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        </section>

        <!-- Main Image -->
        <section class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
            <h2 class="mb-3 text-base font-semibold text-slate-800">Product media</h2>
        <div>
            <label for="main_image" class="mb-2 block text-sm font-medium text-[#6B4A57]">Main Product Image</label>
            <input type="file" id="main_image" name="main_image" accept="image/*"
                   class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F5DDE6] focus:border-[#C98A9B] @error('main_image') border-red-500 @enderror">
            <p class="mt-1 text-sm text-[#8A6A76]">This will be the primary image. Accepted formats: JPEG, PNG, JPG, GIF, WEBP. Max size: 2MB</p>
            <p id="main-image-selected" class="mt-1 text-xs font-medium text-[#7A5252] hidden"></p>
            <div id="main-image-preview-wrap" class="mt-3 hidden">
                <img id="main-image-preview" src="" alt="Main image preview" class="h-24 w-24 rounded-lg border border-[#E7D2DA] object-cover">
            </div>
            @error('main_image')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Additional Images -->
        <div>
            <label for="images" class="mb-2 block text-sm font-medium text-[#6B4A57]">Additional Images</label>
            <input type="file" id="images" name="images[]" accept="image/*" multiple
                   class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F5DDE6] focus:border-[#C98A9B] @error('images.*') border-red-500 @enderror">
            <p class="mt-1 text-sm text-[#8A6A76]">Select multiple images. Accepted formats: JPEG, PNG, JPG, GIF, WEBP. Max size: 2MB each</p>
            <p id="additional-images-selected" class="mt-1 text-xs font-medium text-[#7A5252] hidden"></p>
            <div id="additional-images-preview" class="mt-3 hidden grid grid-cols-3 gap-2 md:grid-cols-6"></div>
            @error('images.*')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        </section>

        <!-- Variants -->
        <section class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-700">Variants *</label>
                <button type="button" id="add-variant-btn" class="rounded-xl bg-[#C47A90] px-3 py-2 text-xs font-semibold text-white hover:bg-[#B66880]">
                    Add Variant
                </button>
            </div>
            <p class="mb-3 text-xs text-[#8A6A76]">Define at least one variant. One variant must be default.</p>

            @error('variants')
                <p class="mb-3 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div id="variants-container" class="space-y-3"></div>
            <template id="variant-template">
                <div class="variant-row rounded-xl border border-[#ECD8E0] bg-[#FFFCFD] p-3">
                    <input type="hidden" data-name="id" value="">
                    <input type="hidden" data-name="remove" value="0">
                    <input type="hidden" data-name="is_default" value="0">
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-[#8A6A76]">Name</label>
                            <input type="text" data-name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" required>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-[#8A6A76]">SKU</label>
                            <input type="text" data-name="sku" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" required>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-[#8A6A76]">Variant Price</label>
                            <input type="number" step="0.01" data-name="price_adjustment" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-[#8A6A76]">Stock</label>
                            <input type="number" min="0" data-name="stock_quantity" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-[#8A6A76]">Variant Image</label>
                            <input type="file" data-name="image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
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
        <section class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
            <h2 class="mb-3 text-base font-semibold text-slate-800">Publishing</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="status" class="mb-2 block text-sm font-medium text-[#6B4A57]">Status *</label>
                <select id="status" name="status"
                        class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F5DDE6] focus:border-[#C98A9B] @error('status') border-red-500 @enderror"
                        required>
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active (visible to customers)</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive (hidden from customers)</option>
                    <option value="pre_order" {{ old('status') == 'pre_order' ? 'selected' : '' }}>Pre-order (available for pre-order)</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-[#D8C1CB] text-[#C47A90] focus:ring-[#F5DDE6]">
                <label for="is_featured" class="ml-2 block text-sm text-[#4E303A]">
                    Featured Product
                </label>
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="is_best_seller" name="is_best_seller" value="1" {{ old('is_best_seller') ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-[#D8C1CB] text-[#C47A90] focus:ring-[#F5DDE6]">
                <label for="is_best_seller" class="ml-2 block text-sm text-[#4E303A]">
                    Best Seller
                </label>
            </div>
        </div>
        </section>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.dashboard', ['section' => 'products']) }}" class="rounded-xl border border-[#D6B7C3] bg-white px-6 py-2 text-[#6B4957] hover:bg-[#FAF1F5]">
                Cancel
            </a>
            <button type="submit" class="rounded-xl bg-[#C47A90] px-6 py-2 text-white hover:bg-[#B66880]">
                <i class="fas fa-save mr-2"></i>Create Product
            </button>
        </div>
    </form>
</div>

<script>
    (function () {
        const mainImageInput = document.getElementById('main_image');
        const mainImageSelected = document.getElementById('main-image-selected');
        const mainImagePreviewWrap = document.getElementById('main-image-preview-wrap');
        const mainImagePreview = document.getElementById('main-image-preview');
        const additionalImagesInput = document.getElementById('images');
        const additionalImagesSelected = document.getElementById('additional-images-selected');
        const additionalImagesPreview = document.getElementById('additional-images-preview');

        mainImageInput?.addEventListener('change', () => {
            const file = mainImageInput.files?.[0];
            if (!file) {
                mainImageSelected?.classList.add('hidden');
                mainImagePreviewWrap?.classList.add('hidden');
                if (mainImagePreview) mainImagePreview.src = '';
                return;
            }

            if (mainImageSelected) {
                mainImageSelected.textContent = `Selected: ${file.name}`;
                mainImageSelected.classList.remove('hidden');
            }

            if (file.type.startsWith('image/') && mainImagePreview && mainImagePreviewWrap) {
                mainImagePreview.src = URL.createObjectURL(file);
                mainImagePreviewWrap.classList.remove('hidden');
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
        @php
            $defaultVariants = old('variants', [[
                'name' => '',
            'sku' => '',
            'price_adjustment' => 0,
            'stock_quantity' => 0,
            'image' => null,
            'is_default' => 1,
            'is_active' => 1,
        ]]);
        @endphp
        const oldVariants = @json($defaultVariants);

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
                                if (defaultInput) defaultInput.value = otherRow === row ? '1' : '0';
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

            // Ensure one default exists
            const defaultInputs = [...container.querySelectorAll('[data-name="is_default"]')];
            if (defaultInputs.length > 0 && defaultInputs.every((input) => input.value !== '1')) {
                defaultInputs[0].value = '1';
                const firstRadio = container.querySelector('[data-default-radio]');
                if (firstRadio) firstRadio.checked = true;
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
                clone.remove();
                renumber();
            });

            container.appendChild(clone);
            renumber();
        };

        addBtn.addEventListener('click', () => addRow());
        oldVariants.forEach((variant) => addRow(variant));
    })();
</script>
@endsection
