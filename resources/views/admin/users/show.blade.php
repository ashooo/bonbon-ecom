@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm text-[#8A6A76]">User Profile</p>
                <h1 class="text-3xl font-bold tracking-tight text-[#4B2E38]">{{ $user->name }}</h1>
            </div>
            <a href="{{ route('admin.dashboard', $backQuery) }}" class="inline-flex items-center rounded-2xl border border-[#D6B7C3] bg-white px-4 py-2 text-sm font-semibold text-[#6B4957] hover:bg-[#FAF1F5]">Back to Users</a>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-1">
                <div class="rounded-3xl border border-[#ECD8E0] bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-xl font-semibold text-[#4B2E38]">Profile info</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between gap-4"><span class="text-[#8A6A76]">Name</span><span class="font-medium text-[#4E303A]">{{ $user->name }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-[#8A6A76]">Email</span><span class="text-[#4E303A]">{{ $user->email }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-[#8A6A76]">Phone</span><span class="text-[#4E303A]">{{ $user->phone ?: 'N/A' }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-[#8A6A76]">DOB</span><span class="text-[#4E303A]">{{ $user->dob?->format('M d, Y') ?: 'N/A' }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-[#8A6A76]">Joined</span><span class="text-[#4E303A]">{{ $user->created_at?->format('M d, Y h:i A') }}</span></div>
                        <div class="flex justify-between gap-4">
                            <span class="text-[#8A6A76]">Status</span>
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->trashed() ? 'bg-rose-50 text-rose-700' : ($user->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700') }}">
                                {{ $user->trashed() ? 'Deleted' : ($user->is_active ? 'Active' : 'Inactive') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-[#ECD8E0] bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-xl font-semibold text-[#4B2E38]">Account actions</h2>
                    <div class="space-y-3">
                        @if (! $user->trashed())
                            <form method="POST" action="{{ route('admin.users.status.update', $user) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="user_status" value="{{ request('user_status', 'all') }}">
                                <input type="hidden" name="user_search" value="{{ request('user_search', '') }}">
                                <input type="hidden" name="user_page" value="{{ request('user_page', 1) }}">
                                <input type="hidden" name="is_active" value="{{ $user->is_active ? 0 : 1 }}">
                                <button type="submit" class="w-full rounded-xl border px-3 py-2 text-sm font-semibold {{ $user->is_active ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                    {{ $user->is_active ? 'Deactivate User' : 'Activate User' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="js-user-delete-form" data-user-name="{{ $user->name }}">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="user_status" value="{{ request('user_status', 'all') }}">
                                <input type="hidden" name="user_search" value="{{ request('user_search', '') }}">
                                <input type="hidden" name="user_page" value="{{ request('user_page', 1) }}">
                                <button type="submit" class="w-full rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100">Delete User</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.users.restore', $user) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="user_status" value="{{ request('user_status', 'deleted') }}">
                                <input type="hidden" name="user_search" value="{{ request('user_search', '') }}">
                                <input type="hidden" name="user_page" value="{{ request('user_page', 1) }}">
                                <button type="submit" class="w-full rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">Restore User</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="space-y-6 xl:col-span-2">
                <div class="rounded-3xl border border-[#ECD8E0] bg-white p-6 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-[#4B2E38]">Recent orders</h2>
                        <span class="text-sm text-[#8A6A76]">{{ $user->orders->count() }} order(s)</span>
                    </div>
                    <div class="overflow-x-auto rounded-2xl border border-[#F1E2E8]">
                        <table class="w-full min-w-[720px]">
                            <thead class="bg-[#FBF2F6]">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Order</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Placed</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Items</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Status</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F4E5EB] bg-white">
                                @forelse($user->orders as $order)
                                    @php
                                        $orderStatusClass = match ($order->status) {
                                            'completed' => 'bg-emerald-50 text-emerald-700',
                                            'cancelled' => 'bg-rose-50 text-rose-700',
                                            'confirmed' => 'bg-sky-50 text-sky-700',
                                            'ready' => 'bg-violet-50 text-violet-700',
                                            default => 'bg-amber-50 text-amber-700',
                                        };
                                    @endphp
                                    <tr class="hover:bg-[#FFFCFD]">
                                        <td class="px-3 py-3 text-sm font-semibold text-[#B66880]">
                                            <a href="{{ route('admin.orders.show', ['order' => $order, 'status' => 'all']) }}" class="hover:text-[#9E536A] hover:underline">{{ $order->order_number }}</a>
                                        </td>
                                        <td class="px-3 py-3 text-sm text-[#6B4A57]">{{ $order->created_at?->format('M d, Y h:i A') }}</td>
                                        <td class="px-3 py-3 text-sm text-[#4E303A]">{{ $order->items->sum('quantity') }}</td>
                                        <td class="px-3 py-3"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $orderStatusClass }}">{{ ucfirst((string) $order->status) }}</span></td>
                                        <td class="px-3 py-3 text-sm font-semibold text-[#4E303A]">&#8369;{{ number_format((float) $order->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-3 py-6 text-center text-sm text-[#8A6A76]">No orders found for this user.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-3xl border border-[#ECD8E0] bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-xl font-semibold text-[#4B2E38]">Status audit log</h2>
                    <div class="space-y-3">
                        @forelse($user->statusAudits as $audit)
                            <div class="rounded-2xl border border-[#F1E2E8] bg-[#FFFCFD] p-3 text-sm">
                                <p class="font-semibold text-[#4E303A]">{{ ucfirst(str_replace('_', ' ', $audit->action)) }}</p>
                                <p class="text-[#8A6A76]">By: {{ $audit->actor?->name ?? 'System' }}</p>
                                <p class="text-[#8A6A76]">At: {{ $audit->created_at?->format('M d, Y h:i A') }}</p>
                                @if ($audit->from_is_active !== null && $audit->to_is_active !== null)
                                    <p class="text-[#8A6A76]">Status: {{ $audit->from_is_active ? 'Active' : 'Inactive' }} -> {{ $audit->to_is_active ? 'Active' : 'Inactive' }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-[#8A6A76]">No audit records yet.</p>
                        @endforelse
                    </div>
                </div>
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
@endsection

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
                msg.textContent = `Are you sure you want to delete ${form.dataset.userName || 'this user'}?`;
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
