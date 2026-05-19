<!-- Categories Section -->
<div id="categories-section" class="admin-section hidden">
    <div class="space-y-6">
        @php
            $categoriesCollection = collect($allCategories ?? []);
            $totalCategories = $categoriesCollection->count();
            $activeCategories = $categoriesCollection->filter(fn($category) => (bool) ($category->status ?? false))->count();
            $parentCategories = $categoriesCollection->filter(fn($category) => !is_null($category->parent_id ?? null))->count();
            $rootCategories = $categoriesCollection->filter(fn($category) => is_null($category->parent_id ?? null))->count();
        @endphp

        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
            <div class="space-y-1">
                <h1 class="text-2xl font-semibold tracking-tight text-[#4B2E38]">Categories overview</h1>
                <p class="text-sm text-[#8A6A76]">Manage category structure and visibility</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.categories.create') }}" class="rounded-xl bg-[#C47A90] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#B66880]">Add category</a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-[#E9C7D4] bg-gradient-to-br from-[#FFF7FA] to-[#F6DFE9] p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.16em] text-[#8F6172]">Total Categories</p>
                <p class="mt-2 text-3xl font-semibold text-[#4D2E38]">{{ $totalCategories }}</p>
                <p class="mt-1 text-xs text-[#8A6A76]">All records</p>
            </div>
            <div class="rounded-2xl border border-[#ECD8E0] bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.16em] text-[#90707A]">Active</p>
                <p class="mt-2 text-3xl font-semibold text-emerald-700">{{ $activeCategories }}</p>
                <p class="mt-1 text-xs text-[#8A6A76]">Visible to shoppers</p>
            </div>
            <div class="rounded-2xl border border-[#ECD8E0] bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.16em] text-[#90707A]">Parented</p>
                <p class="mt-2 text-3xl font-semibold text-[#8A6070]">{{ $parentCategories }}</p>
                <p class="mt-1 text-xs text-[#8A6A76]">Sub-categories</p>
            </div>
            <div class="rounded-2xl border border-[#ECD8E0] bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.16em] text-[#90707A]">Root</p>
                <p class="mt-2 text-3xl font-semibold text-[#4D2E38]">{{ $rootCategories }}</p>
                <p class="mt-1 text-xs text-[#8A6A76]">Top-level categories</p>
            </div>
        </div>

        <div class="rounded-3xl border border-[#ECD8E0] bg-white p-6 shadow-sm">
            <div class="mb-4 flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-[#4B2E38]">Category list</h2>
                    <p class="text-sm text-[#8A6A76]">Browse and maintain hierarchy quickly</p>
                </div>
                <div class="grid w-full gap-2 rounded-2xl border border-[#ECD8E0] bg-[#FFF8FB] p-3 sm:grid-cols-2 xl:w-auto xl:grid-cols-[280px_160px_auto]">
                    <input id="category-search" type="text" placeholder="Search categories..." class="w-full rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843] focus:border-[#C98A9B] focus:ring-2 focus:ring-[#F5DDE6] focus:outline-none" />
                    <select id="category-status-filter" class="rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843] focus:border-[#C98A9B] focus:ring-2 focus:ring-[#F5DDE6] focus:outline-none">
                        <option value="all">All statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <button type="button" id="category-compact-toggle" class="rounded-xl border border-[#E3CDD7] bg-white px-4 py-2 text-sm font-semibold text-[#6B4A57] hover:bg-[#F7EBF0]">Compact mode</button>
                </div>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-[#F1E2E8]">
                <table id="categories-table" class="w-full min-w-[860px]">
                    <thead class="bg-[#FBF2F6]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Parent</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F4E5EB] bg-white">
                        @forelse($allCategories ?? [] as $category)
                            @php
                                $isActive = (bool) ($category->status ?? false);
                                $statusClass = $isActive ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700';
                            @endphp
                            <tr class="category-row hover:bg-[#FFFCFD]" data-name="{{ strtolower($category->name) }}" data-status="{{ $isActive ? 'active' : 'inactive' }}">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($category->image)
                                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="h-12 w-12 rounded-xl border border-[#F0E0E7] object-cover" />
                                        @else
                                            <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-[#F0E0E7] bg-[#F8EFF3] text-[#9B7A86]">CAT</div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-semibold text-[#4E303A]">{{ $category->name }}</p>
                                            <p class="text-xs text-[#8A6A76]">ID #{{ $category->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-[#6B4A57]">{{ $category->parent?->name ?? 'None' }}</td>
                                <td class="px-4 py-4 text-sm">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ $isActive ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.categories.edit', ['category' => $category->id]) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#E3CDD7] text-[#6B4A57] hover:bg-[#F7EBF0]" aria-label="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', ['category' => $category->id]) }}" method="POST" class="inline js-confirm-delete-form" data-confirm-title="Delete Category" data-confirm-message="Are you sure you want to delete this category?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 text-rose-700 hover:bg-rose-50" aria-label="Delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-sm text-[#8A6A76]">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('category-search');
        const statusFilter = document.getElementById('category-status-filter');
        const compactToggle = document.getElementById('category-compact-toggle');
        const table = document.getElementById('categories-table');
        const rows = Array.from(document.querySelectorAll('.category-row'));

        const applyFilters = () => {
            const q = (searchInput?.value || '').trim().toLowerCase();
            const status = statusFilter?.value || 'all';
            rows.forEach((row) => {
                const name = row.dataset.name || '';
                const rowStatus = row.dataset.status || '';
                const nameMatch = q === '' || name.includes(q);
                const statusMatch = status === 'all' || rowStatus === status;
                row.classList.toggle('hidden', !(nameMatch && statusMatch));
            });
        };

        searchInput?.addEventListener('input', applyFilters);
        statusFilter?.addEventListener('change', applyFilters);

        compactToggle?.addEventListener('click', () => {
            table?.classList.toggle('text-xs');
            table?.querySelectorAll('td').forEach((td) => td.classList.toggle('py-2'));
            table?.querySelectorAll('td').forEach((td) => td.classList.toggle('py-4'));
            table?.querySelectorAll('img').forEach((img) => {
                img.classList.toggle('h-10');
                img.classList.toggle('w-10');
                img.classList.toggle('h-12');
                img.classList.toggle('w-12');
            });
        });
    });
</script>
@endpush
