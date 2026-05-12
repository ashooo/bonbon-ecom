<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\Variant;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private function bulkTemplateHeaders(): array
    {
        return [
            'name',
            'price',
            'category',
            'description',
            'discount_price',
            'stock_quantity',
            'status',
            'pre_order_days',
            'is_featured',
            'is_best_seller',
            'variant_name',
            'variant_sku',
            'variant_stock_quantity',
            'variant_price_adjustment',
            'variant_is_default',
            'variant_is_active',
            'image_url',
        ];
    }

    private function bulkTemplateRows(): array
    {
        return [
            [
                'Chocolate Cake',
                '599',
                'Cakes',
                'Rich chocolate sponge with ganache',
                '549',
                '20',
                'active',
                '0',
                'true',
                'false',
                'Whole',
                'CHOCO-CAKE-WHOLE',
                '12',
                '0',
                'true',
                'true',
                'https://example.com/images/chocolate-cake-main.jpg',
            ],
            [
                'Chocolate Cake',
                '599',
                'Cakes',
                'Rich chocolate sponge with ganache',
                '549',
                '20',
                'active',
                '0',
                'true',
                'false',
                'Slice',
                'CHOCO-CAKE-SLICE',
                '24',
                '-300',
                'false',
                'true',
                'https://example.com/images/chocolate-cake-slice.jpg',
            ],
            [
                'Ube Cupcake Box',
                '299',
                'Cupcakes',
                '6-piece ube cupcake box',
                '',
                '15',
                'pre_order',
                '2',
                'false',
                'true',
                'Box of 6',
                'UBE-CUPCAKE-BOX-6',
                '15',
                '0',
                'true',
                'true',
                '',
            ],
        ];
    }

    private function productManagementRedirect(string $message)
    {
        return redirect()
            ->route('admin.dashboard', ['section' => 'products'])
            ->with('success', $message);
    }

    /**
     * Display a listing of the resource.
     */
    private function buildProductRedirectQuery(Request $request): array
    {
        return array_filter([
            'section' => 'products',
            'product_search' => $request->string('product_search')->trim()->value(),
            'product_status' => $request->string('product_status')->value(),
            'product_category' => $request->integer('product_category') ?: null,
            'product_page' => $request->integer('product_page') ?: null,
        ], fn ($value) => $value !== null && $value !== '');
    }

    private function generateUniqueSlug(string $name, ?int $ignoreProductId = null): string
    {
        $base = Str::slug($name);
        $seed = $base !== '' ? $base : 'product';
        $slug = $seed;
        $suffix = 1;

        while (
            Product::query()
                ->where('slug', $slug)
                ->when($ignoreProductId, fn ($query) => $query->where('id', '!=', $ignoreProductId))
                ->exists()
        ) {
            $slug = $seed . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    private function generateUniqueVariantSku(string $seed): string
    {
        $base = Str::upper(Str::slug($seed, '-'));
        $base = $base !== '' ? $base : 'VARIANT';
        $sku = $base;
        $suffix = 1;

        while (Variant::query()->where('sku', $sku)->exists()) {
            $sku = $base . '-' . $suffix;
            $suffix++;
        }

        return $sku;
    }

    private function parseCsvBoolean(mixed $value, bool $default = false): bool
    {
        if ($value === null || $value === '') {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    private function resolveBulkUploadCategory(string $categoryValue): Category
    {
        $normalizedName = trim($categoryValue);
        $slug = Str::slug($normalizedName);

        if ($normalizedName === '') {
            throw new \RuntimeException('Category is required.');
        }

        $category = Category::query()
            ->where('name', $normalizedName)
            ->orWhere('slug', $slug)
            ->first();

        if ($category) {
            if (! $category->is_active) {
                $category->update(['is_active' => true]);
            }

            return $category;
        }

        return Category::query()->create([
            'name' => $normalizedName,
            'slug' => $slug !== '' ? $slug : 'category',
            'is_active' => true,
        ]);
    }

    private function validateAndNormalizeVariants(Request $request, ?Product $product = null): array
    {
        $variants = $request->input('variants', []);
        if (! is_array($variants)) {
            return [];
        }

        $normalized = [];
        $seenSkus = [];

        foreach ($variants as $index => $variant) {
            if (! is_array($variant)) {
                continue;
            }

            $remove = filter_var($variant['remove'] ?? false, FILTER_VALIDATE_BOOLEAN);

            $id = isset($variant['id']) && $variant['id'] !== '' ? (int) $variant['id'] : null;
            $name = trim((string) ($variant['name'] ?? ''));
            $sku = trim((string) ($variant['sku'] ?? ''));

            if ($remove) {
                $normalized[] = [
                    'id' => $id,
                    'remove' => true,
                ];
                continue;
            }

            if ($name === '') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "variants.$index.name" => 'Variant name is required.',
                ]);
            }

            if ($sku === '') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "variants.$index.sku" => 'Variant SKU is required.',
                ]);
            }

            $skuKey = strtoupper($sku);
            if (in_array($skuKey, $seenSkus, true)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "variants.$index.sku" => 'Variant SKU must be unique within this product.',
                ]);
            }
            $seenSkus[] = $skuKey;

            $query = Variant::query()->where('sku', $sku);
            if ($id) {
                $query->where('id', '!=', $id);
            }
            if ($query->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "variants.$index.sku" => 'Variant SKU already exists.',
                ]);
            }

            if ($id && $product && ! $product->variants()->whereKey($id)->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "variants.$index.id" => 'Invalid variant selected.',
                ]);
            }

            $normalized[] = [
                'id' => $id,
                'remove' => false,
                'name' => $name,
                'sku' => $sku,
                'price_adjustment' => (float) ($variant['price_adjustment'] ?? 0),
                'stock_quantity' => max(0, (int) ($variant['stock_quantity'] ?? 0)),
                'image' => $request->file("variants.$index.image"),
                'is_default' => filter_var($variant['is_default'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'is_active' => ! isset($variant['is_active']) || filter_var($variant['is_active'], FILTER_VALIDATE_BOOLEAN),
            ];
        }

        if (collect($normalized)->where('remove', false)->count() === 0) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'variants' => 'At least one active variant is required.',
            ]);
        }

        return $normalized;
    }

    private function syncVariants(Product $product, array $variants): void
    {
        $keptVariantIds = [];
        $defaultVariantId = null;
        $order = 0;

        foreach ($variants as $variant) {
            if (($variant['remove'] ?? false) === true) {
                if (! empty($variant['id'])) {
                    $existing = $product->variants()->whereKey($variant['id'])->first();
                    if ($existing) {
                        $hasDependencies = $existing->orderItems()->exists() || $existing->cartItems()->exists();
                        if ($hasDependencies) {
                            $existing->update(['is_active' => false, 'is_default' => false]);
                            $keptVariantIds[] = $existing->id;
                        } else {
                            $existing->delete();
                        }
                    }
                }
                continue;
            }

            $payload = [
                'name' => $variant['name'],
                'sku' => $variant['sku'],
                'price_adjustment' => $variant['price_adjustment'],
                'stock_quantity' => $variant['stock_quantity'],
                'display_order' => $order,
                'is_active' => $variant['is_active'],
            ];

            if (! empty($variant['image'])) {
                $payload['image_path'] = $variant['image']->store('products/variants', 'public');
            }

            if (! empty($variant['id'])) {
                $variantModel = $product->variants()->whereKey($variant['id'])->first();
                if ($variantModel) {
                    $variantModel->update($payload);
                } else {
                    $variantModel = $product->variants()->create($payload + ['is_default' => false]);
                }
            } else {
                $variantModel = $product->variants()->create($payload + ['is_default' => false]);
            }

            $keptVariantIds[] = $variantModel->id;
            if ($variant['is_default'] && ! $defaultVariantId) {
                $defaultVariantId = $variantModel->id;
            }

            $order++;
        }

        $remaining = $product->variants()->whereIn('id', $keptVariantIds)->orderBy('display_order')->get();
        if ($remaining->isEmpty()) {
            return;
        }

        $defaultVariantId = $defaultVariantId ?: $remaining->first()->id;

        $product->variants()->whereIn('id', $keptVariantIds)->update(['is_default' => false]);
        $product->variants()->whereKey($defaultVariantId)->update(['is_default' => true, 'is_active' => true]);
    }

    public function index(Request $request)
    {
        return redirect()->route('admin.dashboard', $this->buildProductRedirectQuery($request));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive,pre_order',
            'pre_order_days' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'is_best_seller' => 'boolean',
            'category_id' => 'required|exists:categories,id',
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|integer',
            'variants.*.name' => 'nullable|string|max:100',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.price_adjustment' => 'nullable|numeric',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'variants.*.is_default' => 'nullable|boolean',
            'variants.*.is_active' => 'nullable|boolean',
            'variants.*.remove' => 'nullable|boolean',
        ]);

        $normalizedVariants = $this->validateAndNormalizeVariants($request);

        $data = $request->only([
            'name', 'description', 'price', 'stock_quantity', 'category_id',
        ]);
        $data['sale_price'] = $request->input('discount_price');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_best_seller'] = $request->boolean('is_best_seller');
        $data['is_active'] = $request->input('status') !== 'inactive';
        $data['is_preorder'] = $request->input('status') === 'pre_order';
        $data['preorder_days'] = $data['is_preorder'] ? $request->integer('pre_order_days') : 0;
        $data['slug'] = $this->generateUniqueSlug($request->string('name')->value());

        // Handle main image upload
        if ($request->hasFile('main_image')) {
            $mainImagePath = $request->file('main_image')->store('products/main', 'public');
            $data['main_image'] = $mainImagePath;
        }

        $product = Product::create($data);
        $this->syncVariants($product, $normalizedVariants);

        // Handle additional images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $imagePath = $image->store('products/gallery', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $imagePath,
                    'display_order' => $index,
                ]);
            }
        }

        return $this->productManagementRedirect('Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json($product->load('category', 'images', 'variants'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive,pre_order',
            'pre_order_days' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'is_best_seller' => 'boolean',
            'category_id' => 'required|exists:categories,id',
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|integer',
            'variants.*.name' => 'nullable|string|max:100',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.price_adjustment' => 'nullable|numeric',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'variants.*.is_default' => 'nullable|boolean',
            'variants.*.is_active' => 'nullable|boolean',
            'variants.*.remove' => 'nullable|boolean',
        ]);

        $normalizedVariants = $this->validateAndNormalizeVariants($request, $product);

        $data = $request->only([
            'name', 'description', 'price', 'stock_quantity', 'category_id',
        ]);
        $data['sale_price'] = $request->input('discount_price');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_best_seller'] = $request->boolean('is_best_seller');
        $data['is_active'] = $request->input('status') !== 'inactive';
        $data['is_preorder'] = $request->input('status') === 'pre_order';
        $data['preorder_days'] = $data['is_preorder'] ? $request->integer('pre_order_days') : 0;

        // Update slug if name changed
        if ($request->name !== $product->name) {
            $data['slug'] = $this->generateUniqueSlug($request->string('name')->value(), $product->id);
        }

        // Handle main image upload
        if ($request->hasFile('main_image')) {
            // Delete old main image if exists
            if ($product->main_image) {
                Storage::disk('public')->delete($product->main_image);
            }

            $mainImagePath = $request->file('main_image')->store('products/main', 'public');
            $data['main_image'] = $mainImagePath;
        }

        $product->update($data);
        $this->syncVariants($product, $normalizedVariants);

        // Handle additional images
        if ($request->hasFile('images')) {
            // Get current max sort order
            $maxSortOrder = $product->images()->max('display_order') ?? -1;

            foreach ($request->file('images') as $index => $image) {
                $imagePath = $image->store('products/gallery', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $imagePath,
                    'display_order' => $maxSortOrder + $index + 1,
                ]);
            }
        }

        return $this->productManagementRedirect('Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Delete associated images
        if ($product->main_image) {
            Storage::disk('public')->delete($product->main_image);
        }

        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        $product->delete();

        return $this->productManagementRedirect('Product deleted successfully.');
    }

    /**
     * Delete a specific product image
     */
    public function deleteImage(ProductImage $image)
    {
        // Ensure the image belongs to a product (authorization check)
        $productId = $image->product_id;
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        ProductImage::query()
            ->where('product_id', $productId)
            ->orderBy('display_order')
            ->get()
            ->values()
            ->each(function ($imageModel, $index) {
                $imageModel->update(['display_order' => $index]);
            });

        return response()->json(['success' => true]);
    }

    /**
     * Update image sort order
     */
    public function updateImageOrder(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*.id' => 'required|integer|exists:product_images,id',
            'images.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($request->images as $imageData) {
            ProductImage::where('id', $imageData['id'])->update(['display_order' => $imageData['sort_order']]);
        }

        return response()->json(['success' => true]);
    }

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
            'mode' => 'nullable|in:create,upsert',
        ]);

        $mode = $request->string('mode')->value() ?: 'create';
        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        if (! $handle) {
            return $this->productManagementRedirect('Unable to read CSV file.');
        }

        $header = fgetcsv($handle);
        if (! is_array($header)) {
            fclose($handle);
            return $this->productManagementRedirect('CSV file is empty.');
        }

        $headerMap = collect($header)
            ->map(fn ($value) => Str::snake(trim((string) $value)))
            ->values()
            ->all();

        $requiredColumns = ['name', 'price', 'category'];
        foreach ($requiredColumns as $column) {
            if (! in_array($column, $headerMap, true)) {
                fclose($handle);
                return $this->productManagementRedirect('CSV missing required column: ' . $column);
            }
        }

        $createdCount = 0;
        $updatedCount = 0;
        $failedCount = 0;
        $errors = [];
        $rowNumber = 1;
        $records = [];

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }

            $record = [];
            foreach ($headerMap as $index => $column) {
                $record[$column] = isset($row[$index]) ? trim((string) $row[$index]) : null;
            }
            $record['_row_number'] = $rowNumber;
            $records[] = $record;
        }

        fclose($handle);

        $groupedRecords = collect($records)->groupBy(function (array $record): string {
            $nameKey = Str::lower(trim((string) ($record['name'] ?? '')));
            $categoryKey = Str::lower(trim((string) ($record['category'] ?? '')));

            return $nameKey . '|' . $categoryKey;
        });

        foreach ($groupedRecords as $groupRecords) {
            try {
                DB::transaction(function () use ($groupRecords, $mode, &$createdCount, &$updatedCount): void {
                    $first = $groupRecords->first();
                    $categoryValue = (string) ($first['category'] ?? '');
                    $category = $this->resolveBulkUploadCategory($categoryValue);

                    $name = (string) ($first['name'] ?? '');
                    $price = (float) ($first['price'] ?? 0);
                    if ($name === '' || $price < 0) {
                        throw new \RuntimeException('Invalid name or price.');
                    }

                    $status = strtolower((string) ($first['status'] ?? 'active'));
                    if (! in_array($status, ['active', 'inactive', 'pre_order'], true)) {
                        $status = 'active';
                    }

                    $existingProduct = Product::query()
                        ->where('name', $name)
                        ->where('category_id', $category->id)
                        ->first();

                    if ($mode === 'create' && $existingProduct) {
                        throw new \RuntimeException('Product already exists for create mode.');
                    }

                    $payload = [
                        'name' => $name,
                        'description' => ($first['description'] ?? '') !== '' ? $first['description'] : null,
                        'price' => $price,
                        'sale_price' => ($first['discount_price'] ?? '') !== '' ? (float) $first['discount_price'] : null,
                        'stock_quantity' => max(0, (int) ($first['stock_quantity'] ?? 0)),
                        'category_id' => $category->id,
                        'is_active' => $status !== 'inactive',
                        'is_preorder' => $status === 'pre_order',
                        'preorder_days' => $status === 'pre_order' ? max(0, (int) ($first['pre_order_days'] ?? 0)) : 0,
                        'is_featured' => $this->parseCsvBoolean($first['is_featured'] ?? null),
                        'is_best_seller' => $this->parseCsvBoolean($first['is_best_seller'] ?? null),
                    ];

                    if ($existingProduct) {
                        $existingProduct->update($payload);
                        $product = $existingProduct;
                        $updatedCount++;
                    } else {
                        $payload['slug'] = $this->generateUniqueSlug($name);
                        $product = Product::query()->create($payload);
                        $createdCount++;
                    }

                    $processedVariantIds = [];
                    $defaultVariantId = null;

                    foreach ($groupRecords as $record) {
                        $variantName = (string) ($record['variant_name'] ?? '');
                        $variantSku = (string) ($record['variant_sku'] ?? '');
                        $variantStock = ($record['variant_stock_quantity'] ?? '') !== '' ? max(0, (int) $record['variant_stock_quantity']) : (int) $payload['stock_quantity'];
                        $variantPriceAdjustment = ($record['variant_price_adjustment'] ?? '') !== '' ? (float) $record['variant_price_adjustment'] : 0.0;
                        $variantDefault = $this->parseCsvBoolean($record['variant_is_default'] ?? null, false);
                        $variantActive = $this->parseCsvBoolean($record['variant_is_active'] ?? null, true);

                        if ($variantName === '') {
                            $variantName = 'Default';
                        }
                        if ($variantSku === '') {
                            $variantSku = $this->generateUniqueVariantSku($name . '-' . $variantName);
                        }

                        $variant = Variant::query()->where('sku', $variantSku)->first();
                        if ($variant && (int) $variant->product_id !== (int) $product->id) {
                            throw new \RuntimeException('Variant SKU already belongs to another product: ' . $variantSku);
                        }

                        if (! $variant) {
                            $variant = $product->variants()->create([
                                'name' => $variantName,
                                'sku' => $variantSku,
                                'price_adjustment' => $variantPriceAdjustment,
                                'stock_quantity' => $variantStock,
                                'display_order' => count($processedVariantIds),
                                'is_default' => false,
                                'is_active' => $variantActive,
                            ]);
                        } else {
                            $variant->update([
                                'name' => $variantName,
                                'price_adjustment' => $variantPriceAdjustment,
                                'stock_quantity' => $variantStock,
                                'is_active' => $variantActive,
                            ]);
                        }

                        $processedVariantIds[] = $variant->id;
                        if ($variantDefault && ! $defaultVariantId) {
                            $defaultVariantId = $variant->id;
                        }
                    }

                    if (count($processedVariantIds) === 0) {
                        $defaultVariant = $product->variants()->create([
                            'name' => 'Default',
                            'sku' => $this->generateUniqueVariantSku($name . '-default'),
                            'price_adjustment' => 0,
                            'stock_quantity' => (int) $payload['stock_quantity'],
                            'display_order' => 0,
                            'is_default' => true,
                            'is_active' => true,
                        ]);
                        $processedVariantIds[] = $defaultVariant->id;
                        $defaultVariantId = $defaultVariant->id;
                    }

                    $defaultVariantId = $defaultVariantId ?: $processedVariantIds[0];
                    $product->variants()->whereIn('id', $processedVariantIds)->update(['is_default' => false]);
                    $product->variants()->whereKey($defaultVariantId)->update(['is_default' => true, 'is_active' => true]);

                    $imageUrls = $groupRecords
                        ->map(fn ($row) => trim((string) ($row['image_url'] ?? '')))
                        ->filter(fn ($value) => $value !== '')
                        ->unique()
                        ->values();

                    if ($imageUrls->isNotEmpty()) {
                        $primaryImage = (string) $imageUrls->first();
                        $product->update(['main_image' => $primaryImage]);

                        foreach ($imageUrls as $index => $imageUrl) {
                            ProductImage::query()->updateOrCreate(
                                ['product_id' => $product->id, 'image_url' => $imageUrl],
                                ['is_primary' => $index === 0, 'display_order' => $index]
                            );
                        }
                    }
                });
            } catch (\Throwable $e) {
                $failedCount++;
                if (count($errors) < 10) {
                    $firstRow = (int) ($groupRecords->first()['_row_number'] ?? 0);
                    $errors[] = 'Product group starting row ' . $firstRow . ': ' . $e->getMessage();
                }
            }
        }

        $summary = "Bulk upload done. Created: {$createdCount}, Updated: {$updatedCount}, Failed: {$failedCount}.";
        if (! empty($errors)) {
            return redirect()
                ->route('admin.dashboard', ['section' => 'products'])
                ->with('success', $summary)
                ->withErrors($errors);
        }

        return $this->productManagementRedirect($summary);
    }

    public function showBulkUploadForm()
    {
        return view('admin.products.bulk-upload');
    }

    public function downloadBulkTemplateCsv()
    {
        $lines = [];
        $lines[] = implode(',', $this->bulkTemplateHeaders());

        foreach ($this->bulkTemplateRows() as $row) {
            $escaped = array_map(function ($value) {
                $escapedValue = str_replace('"', '""', (string) $value);
                return '"' . $escapedValue . '"';
            }, $row);
            $lines[] = implode(',', $escaped);
        }

        $content = implode("\n", $lines) . "\n";

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="product-bulk-upload-template.csv"',
        ]);
    }

    public function downloadBulkTemplateExcel()
    {
        $separator = "\t";
        $lines = [];
        $lines[] = implode($separator, $this->bulkTemplateHeaders());

        foreach ($this->bulkTemplateRows() as $row) {
            $safeRow = array_map(fn ($value) => str_replace(["\r", "\n", "\t"], ' ', (string) $value), $row);
            $lines[] = implode($separator, $safeRow);
        }

        $content = implode("\n", $lines) . "\n";

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="product-bulk-upload-template.xls"',
        ]);
    }
}
