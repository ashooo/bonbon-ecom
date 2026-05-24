@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
<div class="rounded-3xl border border-[#ECD8E0] bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-[#4B2E38]">Edit Category: {{ $category->name }}</h1>
        <a href="{{ route('admin.dashboard', ['section' => 'categories']) }}" class="rounded-xl border border-[#D6B7C3] bg-white px-4 py-2 text-sm font-semibold text-[#6B4957] hover:bg-[#FAF1F5] transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Categories
        </a>
    </div>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="space-y-6">

        <section class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
            <h2 class="mb-4 text-base font-semibold text-slate-800">General information</h2>
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
                <label for="name" class="mb-2 block text-sm font-medium text-[#6B4A57]">Category Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}"
                       class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F5DDE6] focus:border-[#C98A9B] @error('name') border-red-500 @enderror"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Parent Category -->
            <div>
                <label for="parent_id" class="mb-2 block text-sm font-medium text-[#6B4A57]">Parent Category</label>
                <select id="parent_id" name="parent_id"
                        class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F5DDE6] focus:border-[#C98A9B]">
                    <option value="">Select Parent Category (Optional)</option>
                    @foreach($parentCategories as $parentCategory)
                        <option value="{{ $parentCategory->id }}" {{ old('parent_id', $category->parent_id) == $parentCategory->id ? 'selected' : '' }}>
                            {{ $parentCategory->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        </section>

        <!-- Description -->
        <section class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
            <h2 class="mb-3 text-base font-semibold text-slate-800">Description</h2>
        <div>
            <label for="description" class="mb-2 block text-sm font-medium text-[#6B4A57]">Description</label>
            <textarea id="description" name="description" rows="4"
                      class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F5DDE6] focus:border-[#C98A9B] @error('description') border-red-500 @enderror">{{ old('description', $category->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        </section>

        <!-- Current Image -->
        @if($category->image)
        <div>
            <label class="mb-2 block text-sm font-medium text-[#6B4A57]">Current Image</label>
            <div class="flex items-center space-x-4">
                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-20 h-20 object-cover rounded-lg">
                <div>
                    <p class="text-sm text-[#8A6A76]">Leave empty to keep current image</p>
                </div>
            </div>
        </div>
        @endif

        <section class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
            <h2 class="mb-3 text-base font-semibold text-slate-800">Category media</h2>

        <!-- Image -->
        <div>
            <label for="image" class="mb-2 block text-sm font-medium text-[#6B4A57]">Change Image</label>
            <input type="file" id="image" name="image" accept="image/*"
                   class="w-full px-3 py-2 border border-[#E7D2DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#F5DDE6] focus:border-[#C98A9B] @error('image') border-red-500 @enderror">
            <p class="mt-1 text-sm text-[#8A6A76]">Accepted formats: JPEG, PNG, JPG, GIF. Max size: 2MB</p>
            @error('image')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        </section>

        <!-- Status -->
        <section class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
            <h2 class="mb-3 text-base font-semibold text-slate-800">Publishing</h2>
        <div class="flex items-center">
            <input type="checkbox" id="status" name="status" value="1" {{ old('status', $category->status) ? 'checked' : '' }}
                   class="h-4 w-4 rounded border-[#D8C1CB] text-[#C47A90] focus:ring-[#F5DDE6]">
            <label for="status" class="ml-2 block text-sm text-[#4E303A]">
                Active (visible to customers)
            </label>
        </div>

        </section>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.dashboard', ['section' => 'categories']) }}" class="rounded-xl border border-[#D6B7C3] bg-white px-6 py-2 text-[#6B4957] hover:bg-[#FAF1F5]">
                Cancel
            </a>
            <button type="submit" class="rounded-xl bg-[#C47A90] px-6 py-2 text-white hover:bg-[#B66880]">
                <i class="fas fa-save mr-2"></i>Update Category
            </button>
        </div>
    </form>
</div>
@endsection


