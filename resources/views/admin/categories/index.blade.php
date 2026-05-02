@extends('layouts.admin')

@section('title', 'Categories Management')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Categories Management</h1>
        <div class="flex gap-4">
            <a href="{{ route('admin.categories.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-plus mr-2"></i>Add Category
            </a>
            <a href="{{ route('admin.dashboard', ['section' => 'products']) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i>Back to Products
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent Category</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($categories ?? [] as $category)
                <tr>
                    <td class="px-4 py-4 whitespace-nowrap">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-12 h-12 rounded-lg object-cover">
                        @else
                            <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $category->name }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $category->parent ? $category->parent->name : 'None' }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $category->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $category->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-folder-open text-4xl mb-2"></i>
                            <p>No categories found. <a href="{{ route('admin.categories.create') }}" class="text-blue-600 hover:text-blue-800">Create your first category</a></p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
