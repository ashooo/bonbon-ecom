<!-- Orders Section -->
<div id="orders-section" class="admin-section hidden">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold">Orders Management</h1>
            <div class="flex gap-4">
                <button class="rounded-2xl bg-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-300">All Orders</button>
                <button class="rounded-2xl bg-yellow-100 px-6 py-3 text-sm font-semibold text-yellow-700 hover:bg-yellow-200">Pending</button>
                <button class="rounded-2xl bg-blue-100 px-6 py-3 text-sm font-semibold text-blue-700 hover:bg-blue-200">Processing</button>
                <button class="rounded-2xl bg-green-100 px-6 py-3 text-sm font-semibold text-green-700 hover:bg-green-200">Completed</button>
                <button class="rounded-2xl bg-red-100 px-6 py-3 text-sm font-semibold text-red-700 hover:bg-red-200">Cancelled</button>
            </div>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-soft">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Order ID</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Customer</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Product</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Total</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="px-4 py-4 text-sm font-medium">#12345</td>
                            <td class="px-4 py-4 text-sm">John Doe</td>
                            <td class="px-4 py-4 text-sm">Chocolate Cake</td>
                            <td class="px-4 py-4">
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Delivered</span>
                            </td>
                            <td class="px-4 py-4 text-sm">$25.99</td>
                            <td class="px-4 py-4">
                                <button class="text-indigo-600 hover:text-indigo-800 text-sm">View Details</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-4 text-sm font-medium">#12344</td>
                            <td class="px-4 py-4 text-sm">Jane Smith</td>
                            <td class="px-4 py-4 text-sm">Vanilla Cake</td>
                            <td class="px-4 py-4">
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Processing</span>
                            </td>
                            <td class="px-4 py-4 text-sm">$35.99</td>
                            <td class="px-4 py-4">
                                <button class="text-indigo-600 hover:text-indigo-800 text-sm">View Details</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>