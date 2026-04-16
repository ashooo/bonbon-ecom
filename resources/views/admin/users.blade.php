<!-- Users Section -->
<div id="users-section" class="admin-section hidden">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold">Users Management</h1>
            <div class="flex gap-4">
                <button class="rounded-2xl bg-pink-600 px-6 py-3 text-sm font-semibold text-white hover:bg-pink-700">Customer List</button>
                <button class="rounded-2xl bg-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-300">Activity Logs</button>
            </div>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-soft">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Name</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Email</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Orders</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Joined</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="px-4 py-4 text-sm font-medium">John Doe</td>
                            <td class="px-4 py-4 text-sm">john@example.com</td>
                            <td class="px-4 py-4 text-sm">5</td>
                            <td class="px-4 py-4 text-sm">Jan 15, 2024</td>
                            <td class="px-4 py-4">
                                <button class="text-indigo-600 hover:text-indigo-800 text-sm">View Profile</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-4 text-sm font-medium">Jane Smith</td>
                            <td class="px-4 py-4 text-sm">jane@example.com</td>
                            <td class="px-4 py-4 text-sm">3</td>
                            <td class="px-4 py-4 text-sm">Feb 20, 2024</td>
                            <td class="px-4 py-4">
                                <button class="text-indigo-600 hover:text-indigo-800 text-sm">View Profile</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>