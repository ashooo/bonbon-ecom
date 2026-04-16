@extends('layouts.admin')

@section('title', 'Add Product')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Add New Product</h1>
        <a href="{{ route('admin.dashboard', ['section' => 'products']) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i>Back to Products
        </a>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
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
                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('price') border-red-500 @enderror"
                       required>
                @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Discount Price -->
            <div>
                <label for="discount_price" class="block text-sm font-medium text-gray-700 mb-2">Discount Price</label>
                <input type="number" id="discount_price" name="discount_price" value="{{ old('discount_price') }}" step="0.01" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('discount_price') border-red-500 @enderror">
                <p class="mt-1 text-sm text-gray-500">Leave empty if no discount</p>
                @error('discount_price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Stock Quantity -->
            <div>
                <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity *</label>
                <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('stock_quantity') border-red-500 @enderror"
                       required>
                @error('stock_quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pre-order Days -->
            <div>
                <label for="pre_order_days" class="block text-sm font-medium text-gray-700 mb-2">Pre-order Days</label>
                <input type="number" id="pre_order_days" name="pre_order_days" value="{{ old('pre_order_days') }}" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('pre_order_days') border-red-500 @enderror">
                <p class="mt-1 text-sm text-gray-500">Days needed for production (0 for immediate availability)</p>
                @error('pre_order_days')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                Description <span class="text-red-500">*</span>
            </label>

            <textarea id="description" name="description" rows="4"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>

            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Main Image -->
        <div>
            <label for="main_image" class="block text-sm font-medium text-gray-700 mb-2">Main Product Image</label>
            <input type="file" id="main_image" name="main_image" accept="image/*"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('main_image') border-red-500 @enderror">
            <p class="mt-1 text-sm text-gray-500">This will be the primary image. Accepted formats: JPEG, PNG, JPG, GIF. Max size: 2MB</p>
            @error('main_image')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Additional Images -->
        <div>
            <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Additional Images</label>
            <input type="file" id="images" name="images[]" accept="image/*" multiple
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('images.*') border-red-500 @enderror">
            <p class="mt-1 text-sm text-gray-500">Select multiple images. Accepted formats: JPEG, PNG, JPG, GIF. Max size: 2MB each</p>
            @error('images.*')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Status and Flags -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                <select id="status" name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror"
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
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_featured" class="ml-2 block text-sm text-gray-900">
                    Featured Product
                </label>
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="is_best_seller" name="is_best_seller" value="1" {{ old('is_best_seller') ? 'checked' : '' }}
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
                <i class="fas fa-save mr-2"></i>Create Product
            </button>
        </div>
    </form>
</div>
@endsection
