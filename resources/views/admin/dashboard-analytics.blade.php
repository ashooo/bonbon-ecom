<!-- Dashboard Section -->
<div id="dashboard-section" class="admin-section hidden">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
            <div>
                <h1 class="text-3xl font-bold">Analytics Dashboard</h1>
                <p class="mt-1 text-sm text-slate-500">Last 7 days + live totals</p>
            </div>
            <form method="GET" action="{{ route('admin.reports.export') }}" class="grid grid-cols-1 gap-2 rounded-3xl bg-white p-4 shadow-soft sm:grid-cols-[1fr_1fr_auto_auto]">
                <div>
                    <label for="report_start_date" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Start Date</label>
                    <input id="report_start_date" type="date" name="start_date" value="{{ now()->startOfMonth()->toDateString() }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100" required>
                </div>
                <div>
                    <label for="report_end_date" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">End Date</label>
                    <input id="report_end_date" type="date" name="end_date" value="{{ now()->toDateString() }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100" required>
                </div>
                <div>
                    <label for="report_group_by" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Group By</label>
                    <select id="report_group_by" name="group_by" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-pink-400 focus:ring-2 focus:ring-pink-100">
                        <option value="day">Day</option>
                        <option value="month">Month</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full rounded-xl bg-pink-600 px-4 py-2 text-sm font-semibold text-white hover:bg-pink-700">
                        Export Reports
                    </button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl bg-white p-6 shadow-soft">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Today Revenue</p>
                <p class="mt-3 text-3xl font-semibold">&#8369;{{ number_format((float) ($dashboardStats['today_revenue'] ?? 0), 2) }}</p>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow-soft">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Month Revenue</p>
                <p class="mt-3 text-3xl font-semibold">&#8369;{{ number_format((float) ($dashboardStats['month_revenue'] ?? 0), 2) }}</p>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow-soft">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Today Orders</p>
                <p class="mt-3 text-3xl font-semibold">{{ (int) ($dashboardStats['today_orders_count'] ?? 0) }}</p>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow-soft">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Active Customers</p>
                <p class="mt-3 text-3xl font-semibold">{{ (int) ($dashboardStats['active_customers_count'] ?? 0) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[2fr_1fr]">
            <div class="rounded-3xl bg-white p-6 shadow-soft">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-semibold">Revenue Trend (7 Days)</h2>
                    <span class="text-sm text-slate-500">Real order totals</span>
                </div>

                <div class="space-y-4">
                    @forelse(($revenueTrend ?? collect()) as $point)
                        @php
                            $maxRevenue = (float) ($dashboardStats['max_revenue_point'] ?? 0);
                            $percent = $maxRevenue > 0 ? max(8, (int) round(($point['revenue'] / $maxRevenue) * 100)) : 8;
                        @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="font-medium">{{ $point['label'] }}</span>
                                <span class="text-slate-500">{{ $point['orders_count'] }} order(s) • &#8369;{{ number_format((float) $point['revenue'], 2) }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100">
                                <div class="h-2 rounded-full bg-pink-600" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No revenue data yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-soft">
                <h2 class="mb-4 text-xl font-semibold">Order Pipeline</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between rounded-2xl bg-yellow-50 px-4 py-3">
                        <span class="font-medium text-yellow-800">Pending</span>
                        <span class="font-semibold text-yellow-800">{{ (int) ($dashboardStats['pending_orders_count'] ?? 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl bg-indigo-50 px-4 py-3">
                        <span class="font-medium text-indigo-800">Ready</span>
                        <span class="font-semibold text-indigo-800">{{ (int) ($dashboardStats['ready_orders_count'] ?? 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl bg-green-50 px-4 py-3">
                        <span class="font-medium text-green-800">Completed</span>
                        <span class="font-semibold text-green-800">{{ (int) ($dashboardStats['completed_orders_count'] ?? 0) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-soft">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-semibold">Top Products</h2>
                <span class="text-sm text-slate-500">By units sold</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Product</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Units Sold</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse(($topProducts ?? collect()) as $product)
                            <tr>
                                <td class="px-4 py-4 text-sm font-medium">{{ $product->name }}</td>
                                <td class="px-4 py-4 text-sm">{{ (int) $product->units_sold }}</td>
                                <td class="px-4 py-4 text-sm font-semibold">&#8369;{{ number_format((float) $product->sales_total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-500">No sales data available yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
