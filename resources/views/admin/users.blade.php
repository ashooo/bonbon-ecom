<!-- Users Section -->
<div id="users-section" class="admin-section hidden">
    <div class="space-y-6">
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold tracking-tight text-[#4B2E38]">User management</h1>
            <p class="text-sm text-[#8A6A76]">Monitor accounts, status, and customer activity</p>
        </div>

        <div class="grid grid-cols-2 gap-4 xl:grid-cols-4">
            <a href="{{ route('admin.users.index', ['user_status' => 'all', 'user_search' => $userFilters['search'] ?? null]) }}" class="rounded-2xl border p-4 shadow-sm {{ ($userFilters['status'] ?? 'all') === 'all' ? 'border-[#D9B2C2] bg-[#F8E9F0]' : 'border-[#ECD8E0] bg-white hover:bg-[#FFFAFC]' }}">
                <p class="text-xs uppercase tracking-[0.16em] text-[#8A6070]">All users</p>
                <p class="mt-2 text-3xl font-semibold text-[#4D2E38]">{{ $userCounts['all'] ?? 0 }}</p>
            </a>
            <a href="{{ route('admin.users.index', ['user_status' => 'active', 'user_search' => $userFilters['search'] ?? null]) }}" class="rounded-2xl border p-4 shadow-sm {{ ($userFilters['status'] ?? '') === 'active' ? 'border-emerald-300 bg-emerald-100' : 'border-emerald-200 bg-emerald-50 hover:bg-emerald-100' }}">
                <p class="text-xs uppercase tracking-[0.16em] text-emerald-700">Active</p>
                <p class="mt-2 text-3xl font-semibold text-emerald-800">{{ $userCounts['active'] ?? 0 }}</p>
            </a>
            <a href="{{ route('admin.users.index', ['user_status' => 'inactive', 'user_search' => $userFilters['search'] ?? null]) }}" class="rounded-2xl border p-4 shadow-sm {{ ($userFilters['status'] ?? '') === 'inactive' ? 'border-amber-300 bg-amber-100' : 'border-amber-200 bg-amber-50 hover:bg-amber-100' }}">
                <p class="text-xs uppercase tracking-[0.16em] text-amber-700">Inactive</p>
                <p class="mt-2 text-3xl font-semibold text-amber-800">{{ $userCounts['inactive'] ?? 0 }}</p>
            </a>
            <a href="{{ route('admin.users.index', ['user_status' => 'deleted', 'user_search' => $userFilters['search'] ?? null]) }}" class="rounded-2xl border p-4 shadow-sm {{ ($userFilters['status'] ?? '') === 'deleted' ? 'border-rose-300 bg-rose-100' : 'border-rose-200 bg-rose-50 hover:bg-rose-100' }}">
                <p class="text-xs uppercase tracking-[0.16em] text-rose-700">Deleted</p>
                <p class="mt-2 text-3xl font-semibold text-rose-800">{{ $userCounts['deleted'] ?? 0 }}</p>
            </a>
        </div>

        <div class="rounded-3xl border border-[#ECD8E0] bg-white p-6 shadow-sm">
            <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4 grid grid-cols-1 gap-2 rounded-2xl border border-[#ECD8E0] bg-[#FFF8FB] p-3 sm:grid-cols-[1fr_auto]">
                <input type="hidden" name="user_status" value="{{ $userFilters['status'] ?? 'all' }}">
                <input type="text" name="user_search" value="{{ $userFilters['search'] ?? '' }}" placeholder="Search by name, email, or phone..." class="w-full rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843] focus:border-[#C98A9B] focus:ring-2 focus:ring-[#F5DDE6] focus:outline-none">
                <button type="submit" class="rounded-xl bg-[#C47A90] px-4 py-2 text-sm font-semibold text-white hover:bg-[#B66880]">Search</button>
            </form>

            <div class="overflow-x-auto rounded-2xl border border-[#F1E2E8]">
                <table class="w-full min-w-[980px]">
                    <thead class="bg-[#FBF2F6]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Contact</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Orders</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Joined</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F4E5EB] bg-white">
                        @forelse(($users ?? collect()) as $user)
                            @php
                                $statusLabel = $user->trashed() ? 'Deleted' : ($user->is_active ? 'Active' : 'Inactive');
                                $statusClass = $user->trashed()
                                    ? 'bg-rose-50 text-rose-700'
                                    : ($user->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700');
                            @endphp
                            <tr class="hover:bg-[#FFFCFD]">
                                <td class="px-4 py-3 text-sm">
                                    <p class="font-semibold text-[#4E303A]">{{ $user->name }}</p>
                                    <p class="text-xs text-[#8A6A76]">ID #{{ $user->id }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm text-[#6B4A57]">
                                    <p>{{ $user->email }}</p>
                                    <p class="text-xs text-[#8A6A76]">{{ $user->phone ?: 'No phone' }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-[#4E303A]">{{ $user->orders_count }}</td>
                                <td class="px-4 py-3 text-sm text-[#6B4A57]">{{ $user->created_at?->format('M d, Y') }}</td>
                                <td class="px-4 py-3"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.users.show', ['user' => $user, 'user_status' => $userFilters['status'] ?? 'all', 'user_search' => $userFilters['search'] ?? '', 'user_page' => method_exists($users, 'currentPage') ? $users->currentPage() : 1]) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#E3CDD7] text-[#6B4A57] hover:bg-[#F7EBF0]" title="View profile" aria-label="View profile">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>

                                        @if (! $user->trashed())
                                            <form method="POST" action="{{ route('admin.users.status.update', $user) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="user_status" value="{{ $userFilters['status'] ?? 'all' }}">
                                                <input type="hidden" name="user_search" value="{{ $userFilters['search'] ?? '' }}">
                                                <input type="hidden" name="user_page" value="{{ method_exists($users, 'currentPage') ? $users->currentPage() : 1 }}">
                                                <input type="hidden" name="is_active" value="{{ $user->is_active ? 0 : 1 }}">
                                                <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-[#E3CDD7] text-[#6B4A57] hover:bg-[#F7EBF0]" title="{{ $user->is_active ? 'Deactivate user' : 'Activate user' }}" aria-label="{{ $user->is_active ? 'Deactivate user' : 'Activate user' }}">
                                                    @if($user->is_active)
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v10"/><path d="M18.4 5.6a9 9 0 1 1-12.8 0"/></svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
                                                    @endif
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="js-user-delete-form" data-user-name="{{ $user->name }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="user_status" value="{{ $userFilters['status'] ?? 'all' }}">
                                                <input type="hidden" name="user_search" value="{{ $userFilters['search'] ?? '' }}">
                                                <input type="hidden" name="user_page" value="{{ method_exists($users, 'currentPage') ? $users->currentPage() : 1 }}">
                                                <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50" title="Delete user" aria-label="Delete user">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.restore', $user) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="user_status" value="{{ $userFilters['status'] ?? 'deleted' }}">
                                                <input type="hidden" name="user_search" value="{{ $userFilters['search'] ?? '' }}">
                                                <input type="hidden" name="user_page" value="{{ method_exists($users, 'currentPage') ? $users->currentPage() : 1 }}">
                                                <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-200 text-emerald-700 hover:bg-emerald-50" title="Restore user" aria-label="Restore user">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 3v6h6"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-[#8A6A76]">No users found for the selected filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (isset($users) && method_exists($users, 'links'))
                <div class="mt-6">{{ $users->appends(['section' => 'users', 'user_status' => $userFilters['status'] ?? 'all', 'user_search' => $userFilters['search'] ?? null])->links() }}</div>
            @endif
        </div>
    </div>
</div>

<div id="user-delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 px-4" aria-hidden="true">
    <div class="w-full max-w-md rounded-2xl border border-[#ECD8E0] bg-white p-6 shadow-lg">
        <h3 class="text-lg font-semibold text-[#4B2E38]">Delete User</h3>
        <p id="user-delete-message" class="mt-2 text-sm text-[#7E5E6A]">Are you sure you want to delete this user?</p>
        <div class="mt-6 flex items-center justify-end gap-3">
            <button type="button" id="user-delete-cancel" class="rounded-xl border border-[#E5D2DA] bg-white px-4 py-2 text-sm font-semibold text-[#6B4A57] hover:bg-[#F8EFF3]">Cancel</button>
            <button type="button" id="user-delete-confirm" class="rounded-xl bg-[#C47A90] px-4 py-2 text-sm font-semibold text-white hover:bg-[#B66880]">Delete</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('user-delete-modal');
        const msg = document.getElementById('user-delete-message');
        const cancel = document.getElementById('user-delete-cancel');
        const confirm = document.getElementById('user-delete-confirm');
        let pendingForm = null;

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            pendingForm = null;
        };

        document.querySelectorAll('.js-user-delete-form').forEach((form) => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                pendingForm = form;
                const name = form.dataset.userName || 'this user';
                msg.textContent = `Are you sure you want to delete ${name}?`;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            });
        });

        cancel?.addEventListener('click', closeModal);
        modal?.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
        confirm?.addEventListener('click', () => { if (pendingForm) pendingForm.submit(); });
    });
</script>
@endpush
