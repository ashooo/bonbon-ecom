<!-- Categories Section -->
<div id="categories-section" class="admin-section hidden">
    <div class="space-y-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold">Categories Management</h1>
            <a href="{{ route('admin.categories.create') }}" class="rounded-2xl bg-pink-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-pink-700 transition-all duration-200 shadow-md">
                Add Category
            </a>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-soft">
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
