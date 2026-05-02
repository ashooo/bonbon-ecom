<!-- Users Section -->
<div id="users-section" class="admin-section hidden">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <h1 class="text-3xl font-bold">Users Management</h1>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.users.index', ['user_status' => 'all', 'user_search' => $userFilters['search'] ?? null]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ ($userFilters['status'] ?? 'all') === 'all' ? 'bg-slate-700 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' }}">
                    All ({{ $userCounts['all'] ?? 0 }})
                </a>
                <a href="{{ route('admin.users.index', ['user_status' => 'active', 'user_search' => $userFilters['search'] ?? null]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ ($userFilters['status'] ?? '') === 'active' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                    Active ({{ $userCounts['active'] ?? 0 }})
                </a>
                <a href="{{ route('admin.users.index', ['user_status' => 'inactive', 'user_search' => $userFilters['search'] ?? null]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ ($userFilters['status'] ?? '') === 'inactive' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                    Inactive ({{ $userCounts['inactive'] ?? 0 }})
                </a>
                <a href="{{ route('admin.users.index', ['user_status' => 'deleted', 'user_search' => $userFilters['search'] ?? null]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ ($userFilters['status'] ?? '') === 'deleted' ? 'bg-slate-700 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' }}">
                    Deleted ({{ $userCounts['deleted'] ?? 0 }})
                </a>
            </div>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-soft">
            @if (session('success'))
                <div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6 flex flex-col gap-3 md:flex-row">
                <input type="hidden" name="user_status" value="{{ $userFilters['status'] ?? 'all' }}">
                <input
                    type="text"
                    name="user_search"
                    value="{{ $userFilters['search'] ?? '' }}"
                    placeholder="Search by name, email, or phone"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                >
                <button type="submit" class="rounded-2xl bg-slate-700 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    Search
                </button>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Name</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Contact</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Orders</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Joined</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse(($users ?? collect()) as $user)
                            <tr>
                                <td class="px-4 py-4 text-sm">
                                    <p class="font-semibold">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-500">ID #{{ $user->id }}</p>
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    <p>{{ $user->email }}</p>
                                    <p class="text-xs text-slate-500">{{ $user->phone ?: 'No phone' }}</p>
                                </td>
                                <td class="px-4 py-4 text-sm">{{ $user->orders_count }}</td>
                                <td class="px-4 py-4 text-sm">{{ $user->created_at?->format('M d, Y') }}</td>
                                <td class="px-4 py-4">
                                    <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $user->trashed() ? 'bg-slate-200 text-slate-700' : ($user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $user->trashed() ? 'Deleted' : ($user->is_active ? 'Active' : 'Inactive') }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a
                                            href="{{ route('admin.users.show', ['user' => $user, 'user_status' => $userFilters['status'] ?? 'all', 'user_search' => $userFilters['search'] ?? '', 'user_page' => method_exists($users, 'currentPage') ? $users->currentPage() : 1]) }}"
                                            class="rounded-xl bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-200"
                                        >
                                            View Profile
                                        </a>

                                        @if (! $user->trashed())
                                            <form method="POST" action="{{ route('admin.users.status.update', $user) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="user_status" value="{{ $userFilters['status'] ?? 'all' }}">
                                                <input type="hidden" name="user_search" value="{{ $userFilters['search'] ?? '' }}">
                                                <input type="hidden" name="user_page" value="{{ method_exists($users, 'currentPage') ? $users->currentPage() : 1 }}">
                                                <input type="hidden" name="is_active" value="{{ $user->is_active ? 0 : 1 }}">
                                                <button type="submit" class="rounded-xl px-3 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Soft delete this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="user_status" value="{{ $userFilters['status'] ?? 'all' }}">
                                                <input type="hidden" name="user_search" value="{{ $userFilters['search'] ?? '' }}">
                                                <input type="hidden" name="user_page" value="{{ method_exists($users, 'currentPage') ? $users->currentPage() : 1 }}">
                                                <button type="submit" class="rounded-xl bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-300">
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.restore', $user) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="user_status" value="{{ $userFilters['status'] ?? 'deleted' }}">
                                                <input type="hidden" name="user_search" value="{{ $userFilters['search'] ?? '' }}">
                                                <input type="hidden" name="user_page" value="{{ method_exists($users, 'currentPage') ? $users->currentPage() : 1 }}">
                                                <button type="submit" class="rounded-xl bg-green-100 px-3 py-1 text-xs font-semibold text-green-700 hover:bg-green-200">
                                                    Restore
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">
                                    No users found for the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (isset($users) && method_exists($users, 'links'))
                <div class="mt-6">
                    {{ $users->appends(['section' => 'users'])->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
