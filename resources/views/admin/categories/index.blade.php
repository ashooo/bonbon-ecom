@extends('layouts.admin')

@section('title', 'Categories Management')

@section('content')
<div class="rounded-3xl border border-[#EED9DE] bg-white p-6 shadow-soft">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-[#5A3A3A]">Categories Management</h1>
        <div class="flex gap-4">
            <a href="{{ route('admin.categories.create') }}" class="bg-[#5A3A3A] hover:bg-[#7A5252] text-white px-4 py-2 rounded-xl">
                <i class="fas fa-plus mr-2"></i>Add Category
            </a>
            <a href="{{ route('admin.dashboard', ['section' => 'products']) }}" class="bg-[#E6B7BE] hover:bg-[#C88A92] text-[#5A3A3A] px-4 py-2 rounded-xl">
                <i class="fas fa-arrow-left mr-2"></i>Back to Products
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full table-auto">
            <thead class="bg-[#FFF7F8]">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-[#8C6770] uppercase tracking-wider">Image</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-[#8C6770] uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-[#8C6770] uppercase tracking-wider">Parent Category</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-[#8C6770] uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-[#8C6770] uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-[#F0D5DB]">
                @forelse($categories ?? [] as $category)
                <tr>
                    <td class="px-4 py-4 whitespace-nowrap">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-12 h-12 rounded-lg object-cover">
                        @else
                            <div class="w-12 h-12 rounded-lg bg-[#F5E6E8] flex items-center justify-center">
                                <i class="fas fa-image text-[#C88A92]"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-[#5A3A3A]">
                        {{ $category->name }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-[#8C6770]">
                        {{ $category->parent ? $category->parent->name : 'None' }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $category->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $category->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-[#5A3A3A] hover:text-[#7A5252]">Edit</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" data-confirm data-confirm-title="Delete category?" data-confirm-message="Are you sure you want to delete this category?" data-confirm-ok="Delete">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[#C88A92] hover:text-[#7A5252]">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-[#8C6770]">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-folder-open text-4xl mb-2"></i>
                            <p>No categories found. <a href="{{ route('admin.categories.create') }}" class="text-[#5A3A3A] hover:text-[#7A5252]">Create your first category</a></p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

