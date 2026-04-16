@extends('layouts.admin')

@section('content')
    <!-- Dashboard Section -->
    <div id="dashboard-section" class="admin-section">
        <div class="space-y-6">
            <div class="grid grid-cols-1 xl:grid-cols-[2fr_1fr] gap-6">
                <section class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="rounded-3xl bg-white p-6 shadow-soft">
                            <div class="flex items-center justify-between text-slate-500">
                                <p class="text-sm uppercase tracking-[0.24em]">Daily Sales</p>
                                <span class="text-green-600">+12%</span>
                            </div>
                            <h2 class="mt-4 text-3xl font-semibold">$4,860</h2>
                            <p class="mt-2 text-sm text-slate-500">Revenue generated today</p>
                        </div>
                        <div class="rounded-3xl bg-white p-6 shadow-soft">
                            <div class="flex items-center justify-between text-slate-500">
                                <p class="text-sm uppercase tracking-[0.24em]">New Orders</p>
                                <span class="text-pink-600">18</span>
                            </div>
                            <h2 class="mt-4 text-3xl font-semibold">18</h2>
                            <p class="mt-2 text-sm text-slate-500">Orders received in the last 24h</p>
                        </div>
                        <div class="rounded-3xl bg-white p-6 shadow-soft">
                            <div class="flex items-center justify-between text-slate-500">
                                <p class="text-sm uppercase tracking-[0.24em]">Low Stock</p>
                                <span class="text-yellow-600">4 alerts</span>
                            </div>
                            <h2 class="mt-4 text-3xl font-semibold">4</h2>
                            <p class="mt-2 text-sm text-slate-500">Products need restock</p>
                        </div>
                    </div>

                    <div class="rounded-3xl bg-white p-6 shadow-soft">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Sales overview</p>
                                <h3 class="mt-2 text-2xl font-semibold">Weekly performance</h3>
                            </div>
                            <div class="text-sm text-slate-500">Growth compared to last week</div>
                        </div>

                        <div class="mt-6 space-y-4">
                            <div class="h-2 rounded-full bg-slate-100">
                                <div class="h-2 w-[72%] rounded-full bg-pink-600"></div>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100">
                                <div class="h-2 w-[56%] rounded-full bg-indigo-500"></div>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100">
                                <div class="h-2 w-[84%] rounded-full bg-emerald-500"></div>
                            </div>
                        </div>
                    </div>
                </section>

                    <div class="rounded-3xl bg-white p-6 shadow-soft">
                        <div class="flex items-center justify-between">
                            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Notifications</p>
                            <span class="inline-flex rounded-full bg-pink-600 px-3 py-1 text-xs font-semibold text-white">3 new</span>
                        </div>
                        <ul class="mt-6 space-y-4">
                            <li class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-sm text-slate-600">New order placed: #12467</p>
                                <p class="mt-1 text-xs text-slate-400">2 minutes ago</p>
                            </li>
                            <li class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-sm text-slate-600">Low stock alert: Strawberry Cake</p>
                                <p class="mt-1 text-xs text-slate-400">22 minutes ago</p>
                            </li>
                            <li class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-sm text-slate-600">New support message received</p>
                                <p class="mt-1 text-xs text-slate-400">45 minutes ago</p>
                            </li>
                        </ul>
                    </div>
                </aside>
            </div>

            <section class="grid grid-cols-1 xl:grid-cols-[1fr_1fr] gap-6">
                <div class="rounded-3xl bg-white p-6 shadow-soft">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Dashboard</p>
                            <h2 class="mt-2 text-2xl font-semibold">Sales and order activity</h2>
                        </div>
                        <button class="rounded-2xl bg-pink-600 px-4 py-2 text-sm font-semibold text-white hover:bg-pink-700">View report</button>
                    </div>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="rounded-3xl bg-slate-50 p-5">
                            <p class="text-sm text-slate-500">Sales</p>
                            <h3 class="mt-2 text-3xl font-semibold">$32,400</h3>
                            <p class="text-sm text-slate-500">This month</p>
                        </div>
                        <div class="rounded-3xl bg-slate-50 p-5">
                            <p class="text-sm text-slate-500">Orders</p>
                            <h3 class="mt-2 text-3xl font-semibold">421</h3>
                            <p class="text-sm text-slate-500">Completed orders</p>
                        </div>
                        <div class="rounded-3xl bg-slate-50 p-5">
                            <p class="text-sm text-slate-500">Revenue</p>
                            <h3 class="mt-2 text-3xl font-semibold">$18,210</h3>
                            <p class="text-sm text-slate-500">Net</p>
                        </div>
                    </div>
                    <div class="mt-8 h-72 rounded-[2rem] bg-gradient-to-br from-pink-100 to-slate-100 p-6 shadow-inner">
                        <div class="h-full rounded-[1.5rem] bg-white p-4 shadow-sm">
                            <div class="h-full flex flex-col justify-between">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-lg font-semibold">Revenue trend</h4>
                                        <p class="text-sm text-slate-500">Last 30 days</p>
                                    </div>
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-sm text-green-700">+8% vs last month</span>
                                </div>
                                <div class="flex h-full items-end gap-3 mt-8">
                                    <div class="flex-1 rounded-3xl bg-slate-100 p-4">
                                        <div class="h-40 rounded-full bg-pink-600"></div>
                                        <p class="mt-4 text-sm text-slate-600">Mon</p>
                                    </div>
                                    <div class="flex-1 rounded-3xl bg-slate-100 p-4">
                                        <div class="h-52 rounded-full bg-indigo-500"></div>
                                        <p class="mt-4 text-sm text-slate-600">Tue</p>
                                    </div>
                                    <div class="flex-1 rounded-3xl bg-slate-100 p-4">
                                        <div class="h-36 rounded-full bg-emerald-500"></div>
                                        <p class="mt-4 text-sm text-slate-600">Wed</p>
                                    </div>
                                    <div class="flex-1 rounded-3xl bg-slate-100 p-4">
                                        <div class="h-44 rounded-full bg-pink-400"></div>
                                        <p class="mt-4 text-sm text-slate-600">Thu</p>
                                    </div>
                                    <div class="flex-1 rounded-3xl bg-slate-100 p-4">
                                        <div class="h-56 rounded-full bg-purple-500"></div>
                                        <p class="mt-4 text-sm text-slate-600">Fri</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-white p-6 shadow-soft">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Discount Codes</p>
                            <h2 class="mt-2 text-2xl font-semibold">Generate QR Codes</h2>
                        </div>
                        <button class="rounded-2xl bg-pink-600 px-4 py-2 text-sm font-semibold text-white hover:bg-pink-700">Generate Code</button>
                    </div>

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Discount Type</label>
                                <select class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100">
                                    <option>Percentage (%)</option>
                                    <option>Fixed Amount ($)</option>
                                    <option>Free Shipping</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Discount Value</label>
                                <input type="number" placeholder="10" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Minimum Order</label>
                                <input type="number" placeholder="25.00" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Expiry Date</label>
                                <input type="date" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                            <input type="text" placeholder="Spring Sale Discount" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100" />
                        </div>

                        <div class="bg-slate-50 rounded-2xl p-6 text-center">
                            <div class="mb-4">
                                <div class="w-32 h-32 bg-white rounded-2xl mx-auto flex items-center justify-center border-2 border-dashed border-slate-300">
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-slate-200 rounded-lg mx-auto mb-2 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M12 15h4.01M12 21h9.01M12 3h9.01"></path>
                                            </svg>
                                        </div>
                                        <p class="text-xs text-slate-500">QR Code Preview</p>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-slate-600 mb-4">Generated QR Code will appear here</p>
                            <div class="flex gap-2 justify-center">
                                <button class="rounded-2xl bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-300">Download PNG</button>
                                <button class="rounded-2xl bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-300">Copy Link</button>
                            </div>
                        </div>

                        <div class="bg-slate-50 rounded-2xl p-4">
                            <h4 class="text-sm font-semibold text-slate-700 mb-3">Recent Codes</h4>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between py-2 border-b border-slate-200">
                                    <div>
                                        <p class="text-sm font-medium">SPRING10</p>
                                        <p class="text-xs text-slate-500">10% off orders over $25</p>
                                    </div>
                                    <span class="text-xs text-slate-400">Expires: 2026-04-30</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-slate-200">
                                    <div>
                                        <p class="text-sm font-medium">FREESHIP</p>
                                        <p class="text-xs text-slate-500">Free shipping on all orders</p>
                                    </div>
                                    <span class="text-xs text-slate-400">Expires: 2026-05-15</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @include('admin.products', ['allProducts' => $allProducts])
    @include('admin.inventory')
    @include('admin.orders')
    @include('admin.users')
    @include('admin.support')
    @include('admin.settings')
@endsection