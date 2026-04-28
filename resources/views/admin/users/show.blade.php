@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm text-slate-500">User Profile</p>
                <h1 class="text-3xl font-bold">{{ $user->name }}</h1>
            </div>
            <a
                href="{{ route('admin.dashboard', $backQuery) }}"
                class="inline-flex items-center rounded-2xl bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800"
            >
                Back to Users
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 p-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-1">
                <div class="rounded-3xl bg-white p-6 shadow-soft">
                    <h2 class="mb-4 text-xl font-semibold">Profile Info</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between gap-4"><span class="text-slate-500">Name</span><span>{{ $user->name }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-slate-500">Email</span><span>{{ $user->email }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-slate-500">Phone</span><span>{{ $user->phone ?: 'N/A' }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-slate-500">DOB</span><span>{{ $user->dob?->format('M d, Y') ?: 'N/A' }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-slate-500">Joined</span><span>{{ $user->created_at?->format('M d, Y h:i A') }}</span></div>
                        <div class="flex justify-between gap-4"><span class="text-slate-500">Status</span><span class="rounded-full px-2 py-1 text-xs font-semibold {{ $user->trashed() ? 'bg-slate-200 text-slate-700' : ($user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">{{ $user->trashed() ? 'Deleted' : ($user->is_active ? 'Active' : 'Inactive') }}</span></div>
                    </div>
                </div>

                <div class="rounded-3xl bg-white p-6 shadow-soft">
                    <h2 class="mb-4 text-xl font-semibold">Edit User</h2>
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="user_status" value="{{ request('user_status', 'all') }}">
                        <input type="hidden" name="user_search" value="{{ request('user_search', '') }}">
                        <input type="hidden" name="user_page" value="{{ request('user_page', 1) }}">

                        <div>
                            <label class="mb-1 block text-xs font-medium text-slate-600">Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-slate-600">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-slate-600">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-slate-700 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">Save Changes</button>
                    </form>
                </div>

                <div class="rounded-3xl bg-white p-6 shadow-soft">
                    <h2 class="mb-4 text-xl font-semibold">Account Actions</h2>
                    <div class="space-y-3">
                        @if (! $user->trashed())
                            <form method="POST" action="{{ route('admin.users.status.update', $user) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="user_status" value="{{ request('user_status', 'all') }}">
                                <input type="hidden" name="user_search" value="{{ request('user_search', '') }}">
                                <input type="hidden" name="user_page" value="{{ request('user_page', 1) }}">
                                <input type="hidden" name="is_active" value="{{ $user->is_active ? 0 : 1 }}">
                                <button type="submit" class="w-full rounded-xl px-3 py-2 text-sm font-semibold {{ $user->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">{{ $user->is_active ? 'Deactivate User' : 'Activate User' }}</button>
                            </form>

                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Soft delete this user?');">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="user_status" value="{{ request('user_status', 'all') }}">
                                <input type="hidden" name="user_search" value="{{ request('user_search', '') }}">
                                <input type="hidden" name="user_page" value="{{ request('user_page', 1) }}">
                                <button type="submit" class="w-full rounded-xl bg-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-300">Soft Delete User</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.users.restore', $user) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="user_status" value="{{ request('user_status', 'deleted') }}">
                                <input type="hidden" name="user_search" value="{{ request('user_search', '') }}">
                                <input type="hidden" name="user_page" value="{{ request('user_page', 1) }}">
                                <button type="submit" class="w-full rounded-xl bg-green-100 px-3 py-2 text-sm font-semibold text-green-700 hover:bg-green-200">Restore User</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="space-y-6 xl:col-span-2">
                <div class="rounded-3xl bg-white p-6 shadow-soft">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-semibold">Recent Orders</h2>
                        <span class="text-sm text-slate-500">{{ $user->orders->count() }} order(s)</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-slate-200">
                                <tr>
                                    <th class="px-3 py-2 text-left text-sm font-medium text-slate-500">Order</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium text-slate-500">Placed</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium text-slate-500">Items</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium text-slate-500">Status</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium text-slate-500">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($user->orders as $order)
                                    <tr>
                                        <td class="px-3 py-3 text-sm font-semibold text-indigo-600">
                                            <a href="{{ route('admin.orders.show', ['order' => $order, 'status' => 'all']) }}" class="hover:underline">{{ $order->order_number }}</a>
                                        </td>
                                        <td class="px-3 py-3 text-sm">{{ $order->created_at?->format('M d, Y h:i A') }}</td>
                                        <td class="px-3 py-3 text-sm">{{ $order->items->sum('quantity') }}</td>
                                        <td class="px-3 py-3 text-sm">{{ ucfirst((string) $order->status) }}</td>
                                        <td class="px-3 py-3 text-sm font-semibold">&#8369;{{ number_format((float) $order->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-500">No orders found for this user.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-3xl bg-white p-6 shadow-soft">
                    <h2 class="mb-4 text-xl font-semibold">Status Audit Log</h2>
                    <div class="space-y-3">
                        @forelse($user->statusAudits as $audit)
                            <div class="rounded-2xl border border-slate-200 p-3 text-sm">
                                <p class="font-semibold text-slate-700">{{ ucfirst(str_replace('_', ' ', $audit->action)) }}</p>
                                <p class="text-slate-500">By: {{ $audit->actor?->name ?? 'System' }}</p>
                                <p class="text-slate-500">At: {{ $audit->created_at?->format('M d, Y h:i A') }}</p>
                                @if ($audit->from_is_active !== null && $audit->to_is_active !== null)
                                    <p class="text-slate-500">Status: {{ $audit->from_is_active ? 'Active' : 'Inactive' }} -> {{ $audit->to_is_active ? 'Active' : 'Inactive' }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No audit records yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
