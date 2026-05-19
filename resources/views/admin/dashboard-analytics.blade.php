<!-- Dashboard Section -->
<div id="dashboard-section" class="admin-section hidden">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
            <div>
                <h1 class="text-3xl font-bold">Analytics Dashboard</h1>
                <p class="mt-1 text-sm text-slate-500">Last 7 days + live totals</p>
            </div>
                <form method="GET"
                    action="{{ route('admin.reports.export') }}"
                    class="grid w-full xl:w-[950px] grid-cols-1 gap-3 rounded-2xl bg-white p-4 shadow-md sm:grid-cols-[1fr_1fr_1fr_auto_auto_auto] items-end">

                    <div>
                        <label for="report_type" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Report Type</label>
                        <select id="report_type" name="report_type"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm
                                focus:border-pink-500 focus:ring-2 focus:ring-pink-200 focus:outline-none">
                            <option value="sales">Sales Report</option>
                            <option value="product">Product Sales Report</option>
                        </select>
                    </div>

                    <div>
                        <label for="report_start_date" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Start Date</label>
                        <input id="report_start_date" type="date" name="start_date"
                            value="{{ now()->startOfMonth()->toDateString() }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm
                                focus:border-pink-500 focus:ring-2 focus:ring-pink-200 focus:outline-none"
                            required>
                    </div>

                    <div>
                        <label for="report_end_date" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">End Date</label>
                        <input id="report_end_date" type="date" name="end_date"
                            value="{{ now()->toDateString() }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm
                                focus:border-pink-500 focus:ring-2 focus:ring-pink-200 focus:outline-none"
                            required>
                    </div>

                    <div>
                        <label for="report_group_by" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Group By</label>
                        <select id="report_group_by" name="group_by"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm
                                focus:border-pink-500 focus:ring-2 focus:ring-pink-200 focus:outline-none">
                            <option value="day">Day</option>
                            <option value="week">Week</option>
                            <option value="month">Month</option>
                            <option value="year">Year</option>
                        </select>
                    </div>

                    <div>
                        <label for="report_export_format" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Export Format</label>
                        <select id="report_export_format" name="format"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm
                                focus:border-pink-500 focus:ring-2 focus:ring-pink-200 focus:outline-none">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit"
                            class="relative group w-full flex items-center justify-center gap-2 rounded-xl bg-pink-600 px-4 py-2 text-sm font-semibold text-white hover:bg-pink-700 transition" aria-label="Export report">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-900 text-white text-xs px-2 py-1 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity">Export</span>
                        </button>
                    </div>

                </form>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">

            <div class="rounded-3xl bg-gradient-to-br from-white to-pink-50 border border-white/40 p-6 shadow-lg transform hover:-translate-y-1 transition">
                <p class="text-xs uppercase tracking-[0.2em] text-black/60 font-medium">Today Revenue</p>
                <p class="mt-3 text-3xl font-bold text-black">
                    <span class="text-sm align-top text-slate-600">&#8369;</span>
                    <span class="count-up" data-format="currency" data-target="{{ (float) ($dashboardStats['today_revenue'] ?? 0) }}">0.00</span>
                </p>
                <p class="mt-2 text-xs text-slate-500">Compared to yesterday: {{ $dashboardStats['today_revenue_change'] ?? 'N/A' }}</p>
            </div>

            <div class="rounded-3xl bg-gradient-to-br from-white to-yellow-50 border border-white/40 p-6 shadow-lg transform hover:-translate-y-1 transition">
                <p class="text-xs uppercase tracking-[0.2em] text-black/60 font-medium">Month Revenue</p>
                <p class="mt-3 text-3xl font-bold text-black">
                    <span class="text-sm align-top text-slate-600">&#8369;</span>
                    <span class="count-up" data-format="currency" data-target="{{ (float) ($dashboardStats['month_revenue'] ?? 0) }}">0.00</span>
                </p>
                <p class="mt-2 text-xs text-slate-500">This month total</p>
            </div>

            <div class="rounded-3xl bg-gradient-to-br from-white to-indigo-50 border border-white/40 p-6 shadow-lg transform hover:-translate-y-1 transition">
                <p class="text-xs uppercase tracking-[0.2em] text-black/60 font-medium">Today Orders</p>
                <p class="mt-3 text-3xl font-bold text-black">
                    <span class="count-up" data-target="{{ (int) ($dashboardStats['today_orders_count'] ?? 0) }}">0</span>
                </p>
                <p class="mt-2 text-xs text-slate-500">Orders placed today</p>
            </div>

            <div class="rounded-3xl bg-gradient-to-br from-white to-green-50 border border-white/40 p-6 shadow-lg transform hover:-translate-y-1 transition">
                <p class="text-xs uppercase tracking-[0.2em] text-black/60 font-medium">Active Customers</p>
                <p class="mt-3 text-3xl font-bold text-black">
                    <span class="count-up" data-target="{{ (int) ($dashboardStats['active_customers_count'] ?? 0) }}">0</span>
                </p>
                <p class="mt-2 text-xs text-slate-500">Customers active in the last 7 days</p>
            </div>

        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[2fr_1fr]">

            <div class="rounded-3xl bg-white p-6 shadow-lg shadow-pink-200/50">

                <div class="mb-4 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">Revenue Trend</h2>
                        <span class="text-sm text-slate-500">Order & Revenue total</span>
                    </div>

                    <form id="revenue-trend-form" class="flex items-center gap-2">
                        <label class="text-xs text-slate-500">From</label>
                        <input type="date" name="trend_start" id="trend_start"
                            value="{{ request('trend_start', now()->subDays(6)->toDateString()) }}"
                            class="rounded-xl border border-slate-200 px-2 py-1 text-sm">

                        <label class="text-xs text-slate-500">To</label>
                        <input type="date" name="trend_end" id="trend_end"
                            value="{{ request('trend_end', now()->toDateString()) }}"
                            class="rounded-xl border border-slate-200 px-2 py-1 text-sm">

                        <button type="button" id="apply-trend" class="relative group ml-2 rounded-xl bg-pink-600 p-2 text-white" aria-label="Apply">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12l5 5L20 7" />
                            </svg>
                            <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-900 text-white text-xs px-2 py-1 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity">Apply</span>
                        </button>
                    </form>
                </div>

                <div id="revenue-trend-list" class="space-y-3">

                    @forelse(($revenueTrend ?? collect()) as $point)
                        @php
                            $maxRevenue = (float) ($dashboardStats['max_revenue_point'] ?? 0);
                            $percent = $maxRevenue > 0
                                ? max(6, (int) round(($point['revenue'] / $maxRevenue) * 100))
                                : 6;
                        @endphp

                        <div class="flex flex-col">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-sm font-medium truncate">{{ $point['label'] }}</div>
                                    <div class="text-xs text-slate-500 truncate">{{ \Carbon\Carbon::parse($point['date'] ?? now())->format('M j, Y') }}</div>
                                </div>

                                <div class="flex items-center gap-4">
                                    <div class="text-sm text-slate-600">Orders <span class="ml-1 inline-block rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">{{ $point['orders_count'] }}</span></div>
                                    <div class="text-sm font-semibold text-black">&#8369;<span class="count-up" data-format="currency" data-target="{{ (float) $point['revenue'] }}">{{ number_format((float) $point['revenue'],2) }}</span></div>
                                </div>
                            </div>

                            <div class="mt-2 h-3 w-full rounded-full bg-slate-100 overflow-hidden ring-1 ring-slate-50">
                                <div class="revenue-bar h-3 rounded-full shadow-sm" data-percent="{{ $percent }}" style="width:0%; background: #dda6bf; box-shadow: 0 4px 10px rgba(219,39,119,0.16);"></div>
                            </div>
                        </div>

                    @empty
                        <p class="text-sm text-slate-500">No revenue data yet.</p>
                    @endforelse

                </div>


        </div>

<div class="rounded-3xl bg-white p-6 shadow-lg shadow-pink-200/50">

    <h2 class="mb-4 text-xl font-semibold">Order Pipeline</h2>

    <div class="space-y-3 text-sm">

        <div class="flex items-center justify-between rounded-2xl bg-yellow-50 px-4 py-3">
            <div class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                <span class="font-medium text-yellow-800">Pending</span>
            </div>
            <span class="font-semibold text-yellow-800">{{ (int) ($dashboardStats['pending_orders_count'] ?? 0) }}</span>
        </div>

        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
            <div class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                <span class="font-medium text-slate-700">Confirmed</span>
            </div>
            <span class="font-semibold text-slate-700">{{ (int) ($dashboardStats['confirmed_orders_count'] ?? 0) }}</span>
        </div>

        <div class="flex items-center justify-between rounded-2xl bg-indigo-50 px-4 py-3">
            <div class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-indigo-800"></span>
                <span class="font-medium text-indigo-800">Ready</span>
            </div>
            <span class="font-semibold text-indigo-800">{{ (int) ($dashboardStats['ready_orders_count'] ?? 0) }}</span>
        </div>

        <div class="flex items-center justify-between rounded-2xl bg-green-50 px-4 py-3">
            <div class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-green-800"></span>
                <span class="font-medium text-green-800">Completed</span>
            </div>
            <span class="font-semibold text-green-800">{{ (int) ($dashboardStats['completed_orders_count'] ?? 0) }}</span>
        </div>

        <div class="flex items-center justify-between rounded-2xl bg-red-50 px-4 py-3">
            <div class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                <span class="font-medium text-red-800">Cancelled</span>
            </div>
            <span class="font-semibold text-red-800">{{ (int) ($dashboardStats['cancelled_orders_count'] ?? 0) }}</span>
        </div>

    </div>

</div>

<script>
    (function(){
        function formatNumber(value, isCurrency){
            if(isCurrency){
                return Number(value).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
            }
            return Number(value).toLocaleString();
        }

        function animate(el, target, duration, isCurrency){
            var start = 0;
            var startTime = null;
            function step(timestamp){
                if(!startTime) startTime = timestamp;
                var progress = Math.min((timestamp - startTime) / duration, 1);
                var current = start + (target - start) * progress;
                el.textContent = isCurrency ? formatNumber(current, true) : Math.round(current).toLocaleString();
                if(progress < 1){
                    window.requestAnimationFrame(step);
                }
            }
            window.requestAnimationFrame(step);
        }

        document.addEventListener('DOMContentLoaded', function(){
            var els = document.querySelectorAll('.count-up');
            els.forEach(function(el){
                var raw = el.getAttribute('data-target') || el.textContent || '0';
                var target = parseFloat(raw) || 0;
                var format = el.getAttribute('data-format') || '';
                var isCurrency = format === 'currency';
                var duration = 1400; 
                setTimeout(function(){ animate(el, target, duration, isCurrency); }, Math.random()*300);
            });

            var bars = document.querySelectorAll('.revenue-bar');
            bars.forEach(function(bar, idx){
                var percent = parseFloat(bar.getAttribute('data-percent')) || 0;
                var min = Math.max(6, percent);
                bar.style.width = '0%';
                bar.style.minWidth = '0%';
                bar.style.transition = 'width 900ms cubic-bezier(0.2,0.9,0.2,1), min-width 300ms ease';
                setTimeout(function(){
                    bar.style.width = percent + '%';
                    setTimeout(function(){ bar.style.minWidth = min + '%'; }, 50);
                }, 200 + idx * 80);
            });
            try {
                var list = document.getElementById('revenue-trend-list');
                if (list) {
                    var params = new URLSearchParams(window.location.search);
                    var start = params.get('trend_start') || document.getElementById('trend_start').value;
                    var end = params.get('trend_end') || document.getElementById('trend_end').value;
                    if (start && end) {
                        var s = new Date(start + 'T00:00:00');
                        var e = new Date(end + 'T00:00:00');
                        var days = Math.floor((e - s) / (1000 * 60 * 60 * 24)) + 1;
                        if (days > 7) {
                            list.style.maxHeight = '360px';
                            list.style.overflowY = 'auto';
                            list.style.paddingRight = '8px';
                        } else {
                            list.style.maxHeight = '';
                            list.style.overflowY = '';
                        }
                    }
                }
            } catch (err) {}
        });
        document.addEventListener('click', function(e){
            var applyButton = e.target ? e.target.closest('#apply-trend') : null;
            if(!applyButton){
                return;
            }

            var startInput = document.getElementById('trend_start');
            var endInput = document.getElementById('trend_end');
            var start = startInput ? startInput.value : '';
            var end = endInput ? endInput.value : '';

            if(!start || !end){
                alert('Please select both start and end dates.');
                return;
            }

            var params = new URLSearchParams(window.location.search);
            params.set('trend_start', start);
            params.set('trend_end', end);
            window.location.search = params.toString();
        });
    })();
</script>
    </div>
        <div class="rounded-3xl bg-white p-6 shadow-soft">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-semibold">Monthly Top Products</h2>
                <span class="text-sm text-slate-500">By units sold</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Rank</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Product</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Units Sold</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase">Revenue</th>
                        </tr>
                    </thead>
                        <tbody>
                            @forelse(($topProducts ?? collect()) as $product)
                                @php
                                    $monthRevenueTotal = (float) ($dashboardStats['month_revenue'] ?? 0);
                                    $revenueShare = $monthRevenueTotal > 0
                                        ? ((float) $product->sales_total / $monthRevenueTotal) * 100
                                        : 0;
                                @endphp
                                <tr class="odd:bg-white even:bg-slate-50">
                                    <td class="px-4 py-4 text-sm">
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-pink-50 text-xs font-bold text-pink-700">#{{ $loop->iteration }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium max-w-[380px] truncate">{{ $product->name }}</td>
                                    <td class="px-4 py-4 text-sm">
                                        <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ (int) $product->units_sold }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <div class="text-sm font-semibold text-black">&#8369;{{ number_format((float) $product->sales_total, 2) }}</div>
                                        <div class="text-xs font-medium text-slate-500">{{ number_format($revenueShare, 1) }}% of month revenue</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">No sales data available for this month yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
