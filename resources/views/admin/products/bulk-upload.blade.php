@extends('layouts.admin')

@section('content')
<div class="rounded-3xl bg-white p-6 shadow-soft">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Bulk Product Upload</h1>
            <p class="mt-1 text-sm text-slate-600">Upload one row per variant. Repeat product fields for products with multiple variants.</p>
        </div>
        <a href="{{ route('admin.dashboard', ['section' => 'products']) }}" class="rounded-2xl bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-300">
            Back to Products
        </a>
    </div>

    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('admin.products.bulk-upload.template.csv') }}" class="rounded-xl bg-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-300">Download CSV Template</a>
        <a href="{{ route('admin.products.bulk-upload.template.xls') }}" class="rounded-xl bg-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-300">Download Excel Template</a>
    </div>

    <form method="POST" action="{{ route('admin.products.bulk-upload') }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-4 md:grid-cols-3">
        @csrf
        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">CSV File</label>
            <input type="file" name="csv_file" accept=".csv,.txt" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Mode</label>
            <select name="mode" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                <option value="create">Create only</option>
                <option value="upsert">Create or update (name + category)</option>
            </select>
        </div>
        <div class="md:col-span-3">
            <button type="submit" class="rounded-xl bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Upload CSV</button>
        </div>
    </form>

    <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-4">
        <h2 class="mb-3 text-lg font-semibold">Template Guide</h2>
        <p class="text-xs text-slate-600">
            Required columns: <code>name</code>, <code>price</code>, <code>category</code>.
            Optional: <code>description</code>, <code>discount_price</code>, <code>stock_quantity</code>, <code>status</code>, <code>pre_order_days</code>, <code>is_featured</code>, <code>is_best_seller</code>, <code>variant_name</code>, <code>variant_sku</code>, <code>variant_stock_quantity</code>, <code>variant_price_adjustment</code> (actual variant price), <code>variant_is_default</code>, <code>variant_is_active</code>, <code>image_url</code>.
        </p>
        <ul class="mt-3 list-disc space-y-1 pl-5 text-xs text-slate-600">
            <li>One row = one variant</li>
            <li>Repeat product fields for extra variants of the same product</li>
            <li>New category names in the file are created automatically and will appear in the category list</li>
            <li><code>status</code>: <code>active</code>, <code>inactive</code>, or <code>pre_order</code></li>
            <li>Boolean fields: use <code>true</code> or <code>false</code></li>
            <li><code>image_url</code> values are attached to product images (first becomes main image)</li>
        </ul>
    </div>
</div>
@endsection
