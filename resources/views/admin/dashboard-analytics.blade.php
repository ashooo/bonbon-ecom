<!-- Dashboard Section -->
<div id="dashboard-section" class="admin-section hidden">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
            <div class="space-y-1">
                <h1 class="text-2xl font-semibold tracking-tight text-[#4B2E38]">Dashboard overview</h1>
                <p class="text-sm text-[#8A6A76]">Core business metrics and trend snapshots</p>
            </div>

            <details class="w-full xl:w-[760px] rounded-2xl border border-[#EBCFD9] bg-white shadow-sm">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 text-sm font-semibold text-[#6E4A57]">
                    <span>Export reports</span>
                    <span class="rounded-full bg-[#F9E8EF] px-3 py-1 text-xs font-medium text-[#8E5F71]">Open panel</span>
                </summary>

                <form method="GET" action="{{ route('admin.reports.export') }}" class="grid grid-cols-1 gap-3 border-t border-[#F2DFE6] p-4 sm:grid-cols-2 xl:grid-cols-[1fr_1fr_1fr_1fr_1fr_auto] xl:items-end">
                    <div>
                        <label for="report_type" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-[#8A6A76]">Report Type</label>
                        <select id="report_type" name="report_type" class="w-full rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843] focus:border-[#C98A9B] focus:ring-2 focus:ring-[#F5DDE6] focus:outline-none">
                            <option value="sales">Sales Report</option>
                            <option value="product">Product Sales Report</option>
                        </select>
                    </div>

                    <div>
                        <label for="report_start_date" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-[#8A6A76]">Start Date</label>
                        <input id="report_start_date" type="date" name="start_date" value="{{ now()->startOfMonth()->toDateString() }}" class="w-full rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843] focus:border-[#C98A9B] focus:ring-2 focus:ring-[#F5DDE6] focus:outline-none" required>
                    </div>

                    <div>
                        <label for="report_end_date" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-[#8A6A76]">End Date</label>
                        <input id="report_end_date" type="date" name="end_date" value="{{ now()->toDateString() }}" class="w-full rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843] focus:border-[#C98A9B] focus:ring-2 focus:ring-[#F5DDE6] focus:outline-none" required>
                    </div>

                    <div>
                        <label for="report_group_by" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-[#8A6A76]">Group By</label>
                        <select id="report_group_by" name="group_by" class="w-full rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843] focus:border-[#C98A9B] focus:ring-2 focus:ring-[#F5DDE6] focus:outline-none">
                            <option value="day">Day</option>
                            <option value="week">Week</option>
                            <option value="month">Month</option>
                            <option value="year">Year</option>
                        </select>
                    </div>

                    <div>
                        <label for="report_export_format" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-[#8A6A76]">Format</label>
                        <select id="report_export_format" name="format" class="w-full rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843] focus:border-[#C98A9B] focus:ring-2 focus:ring-[#F5DDE6] focus:outline-none">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>

                    <button type="submit" class="rounded-xl bg-[#C47A90] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#B66880]">Export</button>
                </form>
            </details>
        </div>

        @php
            $todayRevenue = (float) ($dashboardStats['today_revenue'] ?? 0);
            $monthRevenue = (float) ($dashboardStats['month_revenue'] ?? 0);
            $todayOrders = (int) ($dashboardStats['today_orders_count'] ?? 0);
            $activeCustomers = (int) ($dashboardStats['active_customers_count'] ?? 0);
            $trendStart = request('trend_start', now()->subDays(6)->toDateString());
            $trendEnd = request('trend_end', now()->toDateString());
        @endphp

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-[1.4fr_1fr_1fr_1fr]">
            <div class="rounded-3xl border border-[#E9C7D4] bg-gradient-to-br from-[#FFF7FA] via-[#FBEAF1] to-[#F4D8E5] p-6 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-[#8F6172]">Today Revenue</p>
                <p class="mt-3 text-4xl font-bold text-[#4D2E38]">
                    <span class="text-lg align-top">&#8369;</span>
                    <span class="count-up" data-format="currency" data-target="{{ $todayRevenue }}">0.00</span>
                </p>
                <p class="mt-2 text-sm text-[#7F5B68]">{{ $dashboardStats['today_revenue_change'] ?? 'No comparison data yet' }}</p>
            </div>

            <div class="rounded-3xl border border-[#ECD8E0] bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.18em] text-[#90707A]">Month Revenue</p>
                <p class="mt-2 text-2xl font-semibold text-[#553845]">
                    <span class="text-sm align-top">&#8369;</span>
                    <span class="count-up" data-format="currency" data-target="{{ $monthRevenue }}">0.00</span>
                </p>
                <p class="mt-2 text-xs text-[#8A6A76]">Current calendar month</p>
            </div>

            <div class="rounded-3xl border border-[#ECE3E8] bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.18em] text-[#90707A]">Today Orders</p>
                <p class="mt-2 text-2xl font-semibold text-[#553845]"><span class="count-up" data-target="{{ $todayOrders }}">0</span></p>
                <p class="mt-2 text-xs text-[#8A6A76]">Placed today</p>
            </div>

            <div class="rounded-3xl border border-[#ECE3E8] bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.18em] text-[#90707A]">Active Customers</p>
                <p class="mt-2 text-2xl font-semibold text-[#553845]"><span class="count-up" data-target="{{ $activeCustomers }}">0</span></p>
                <p class="mt-2 text-xs text-[#8A6A76]">7-day window</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[2fr_1fr]">
            <div class="rounded-3xl border border-[#ECD8E0] bg-white p-6 shadow-sm">
                <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-[#4B2E38]">Revenue Trend</h2>
                        <p class="text-sm text-[#8A6A76]">Revenue and order count by selected date range</p>
                    </div>

                    <form id="revenue-trend-form" class="grid grid-cols-1 gap-2 sm:grid-cols-[auto_1fr_auto_1fr_auto] sm:items-end">
                        <label for="trend_start" class="text-xs font-medium text-[#8A6A76]">From</label>
                        <input type="date" name="trend_start" id="trend_start" value="{{ $trendStart }}" class="rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843]">
                        <label for="trend_end" class="text-xs font-medium text-[#8A6A76]">To</label>
                        <input type="date" name="trend_end" id="trend_end" value="{{ $trendEnd }}" class="rounded-xl border border-[#E7D2DA] px-3 py-2 text-sm text-[#533843]">
                        <button type="button" id="apply-trend" class="rounded-xl bg-[#C47A90] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#B66880]">Apply</button>
                    </form>
                </div>

                <div id="revenue-trend-list" class="space-y-3">
                    @forelse(($revenueTrend ?? collect()) as $point)
                        @php
                            $maxRevenue = (float) ($dashboardStats['max_revenue_point'] ?? 0);
                            $percent = $maxRevenue > 0 ? max(4, (int) round(($point['revenue'] / $maxRevenue) * 100)) : 4;
                        @endphp

                        <div class="rounded-2xl border border-[#F2E1E8] bg-[#FFFCFD] p-3">
                            <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <div class="text-sm font-semibold text-[#553845]">{{ $point['label'] }}</div>
                                    <div class="text-xs text-[#8A6A76]">{{ \Carbon\Carbon::parse($point['date'] ?? now())->format('M j, Y') }}</div>
                                </div>
                                <div class="flex items-center gap-3 text-sm">
                                    <span class="rounded-full bg-[#F5E9EE] px-2 py-1 text-xs font-semibold text-[#755361]">{{ (int) $point['orders_count'] }} orders</span>
                                    <span class="font-semibold text-[#4D2E38]">&#8369;<span class="count-up" data-format="currency" data-target="{{ (float) $point['revenue'] }}">{{ number_format((float) $point['revenue'], 2) }}</span></span>
                                </div>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-[#F3E5EA]">
                                <div class="revenue-bar h-2 rounded-full bg-[#C47A90]" data-percent="{{ $percent }}" style="width:0%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-[#8A6A76]">No revenue data yet.</p>
                    @endforelse
                </div>
            </div>

            @php
                $pending = (int) ($dashboardStats['pending_orders_count'] ?? 0);
                $confirmed = (int) ($dashboardStats['confirmed_orders_count'] ?? 0);
                $ready = (int) ($dashboardStats['ready_orders_count'] ?? 0);
                $completed = (int) ($dashboardStats['completed_orders_count'] ?? 0);
                $cancelled = (int) ($dashboardStats['cancelled_orders_count'] ?? 0);
                $pipelineTotal = max(1, $pending + $confirmed + $ready + $completed + $cancelled);
                $pipeline = [
                    ['label' => 'Pending', 'value' => $pending, 'tone' => 'bg-amber-400', 'text' => 'text-amber-700'],
                    ['label' => 'Confirmed', 'value' => $confirmed, 'tone' => 'bg-sky-400', 'text' => 'text-sky-700'],
                    ['label' => 'Ready', 'value' => $ready, 'tone' => 'bg-fuchsia-400', 'text' => 'text-fuchsia-700'],
                    ['label' => 'Completed', 'value' => $completed, 'tone' => 'bg-emerald-400', 'text' => 'text-emerald-700'],
                    ['label' => 'Cancelled', 'value' => $cancelled, 'tone' => 'bg-rose-400', 'text' => 'text-rose-700'],
                ];
            @endphp

            <div class="rounded-3xl border border-[#ECD8E0] bg-white p-6 shadow-sm">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-[#4B2E38]">Order Pipeline</h2>
                    <p class="text-sm text-[#8A6A76]">Status mix for all tracked orders</p>
                </div>

                <div class="mb-4 flex h-3 overflow-hidden rounded-full bg-[#F3E5EA]">
                    @foreach($pipeline as $stage)
                        @php $width = $stage['value'] > 0 ? (($stage['value'] / $pipelineTotal) * 100) : 0; @endphp
                        <div class="{{ $stage['tone'] }}" style="width: {{ $width }}%"></div>
                    @endforeach
                </div>

                <div class="space-y-2 text-sm">
                    @foreach($pipeline as $stage)
                        <div class="flex items-center justify-between rounded-xl border border-[#F2E5EA] px-3 py-2">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full {{ $stage['tone'] }}"></span>
                                <span class="font-medium text-[#5A3C47]">{{ $stage['label'] }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-semibold {{ $stage['text'] }}">{{ $stage['value'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-[#ECD8E0] bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-[#4B2E38]">Top Products</h2>
                    <p class="text-sm text-[#8A6A76]">Best-selling products this month</p>
                </div>
                <span class="rounded-full bg-[#F6EAF0] px-3 py-1 text-xs font-semibold text-[#7D5A67]">{{ count(($topProducts ?? collect())->take(5)) }} shown</span>
            </div>

            @if(($topProducts ?? collect())->count() <= 1)
                @php $best = ($topProducts ?? collect())->first(); @endphp
                @if($best)
                    <div class="rounded-2xl border border-[#F0E0E7] bg-[#FFFCFD] p-4">
                        <p class="text-xs uppercase tracking-[0.16em] text-[#8F6C79]">Best seller</p>
                        <p class="mt-2 text-xl font-semibold text-[#4D2E38]">{{ $best->name }}</p>
                        <div class="mt-3 flex items-center gap-3 text-sm text-[#6B4A57]">
                            <span class="rounded-full bg-[#F5E9EE] px-3 py-1 font-semibold">{{ (int) $best->units_sold }} units</span>
                            <span class="font-semibold">&#8369;{{ number_format((float) $best->sales_total, 2) }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-[#8A6A76]">No sales data available for this month yet.</p>
                @endif
            @else
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-[#FBF2F6]">
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Rank</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Units</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-[#7D5A67]">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(($topProducts ?? collect())->take(5) as $product)
                                @php
                                    $monthRevenueTotal = (float) ($dashboardStats['month_revenue'] ?? 0);
                                    $revenueShare = $monthRevenueTotal > 0 ? ((float) $product->sales_total / $monthRevenueTotal) * 100 : 0;
                                @endphp
                                <tr class="border-b border-[#F4E5EB] last:border-b-0">
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-[#F6EAF0] text-xs font-bold text-[#8A6070]">#{{ $loop->iteration }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-[#4E303A]">{{ $product->name }}</td>
                                    <td class="px-4 py-3 text-sm text-[#6B4A57]">{{ (int) $product->units_sold }}</td>
                                    <td class="px-4 py-3 text-right text-sm">
                                        <div class="font-semibold text-[#4E303A]">&#8369;{{ number_format((float) $product->sales_total, 2) }}</div>
                                        <div class="text-xs text-[#8A6A76]">{{ number_format($revenueShare, 1) }}% of month</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
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
            document.querySelectorAll('.count-up').forEach(function(el){
                var raw = el.getAttribute('data-target') || el.textContent || '0';
                var target = parseFloat(raw) || 0;
                var isCurrency = (el.getAttribute('data-format') || '') === 'currency';
                setTimeout(function(){ animate(el, target, 1200, isCurrency); }, Math.random() * 200);
            });

            document.querySelectorAll('.revenue-bar').forEach(function(bar, idx){
                var percent = parseFloat(bar.getAttribute('data-percent')) || 0;
                bar.style.transition = 'width 850ms cubic-bezier(0.2,0.9,0.2,1)';
                setTimeout(function(){ bar.style.width = percent + '%'; }, 140 + (idx * 70));
            });

            try {
                var list = document.getElementById('revenue-trend-list');
                var startInput = document.getElementById('trend_start');
                var endInput = document.getElementById('trend_end');
                if (list && startInput && endInput) {
                    var s = new Date(startInput.value + 'T00:00:00');
                    var e = new Date(endInput.value + 'T00:00:00');
                    var days = Math.floor((e - s) / (1000 * 60 * 60 * 24)) + 1;
                    if (days > 7) {
                        list.style.maxHeight = '380px';
                        list.style.overflowY = 'auto';
                        list.style.paddingRight = '6px';
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
                if (window.BonbonNotify) { window.BonbonNotify('warning', 'Please select both start and end dates.'); } else { alert('Please select both start and end dates.'); }
                return;
            }

            var params = new URLSearchParams(window.location.search);
            params.set('trend_start', start);
            params.set('trend_end', end);
            window.location.search = params.toString();
        });
    })();
</script>

