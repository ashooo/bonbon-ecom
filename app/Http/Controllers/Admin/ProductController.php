<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private function productManagementRedirect(string $message)
    {
        return redirect()
            ->route('admin.dashboard', ['section' => 'products'])
            ->with('success', $message);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category', 'images')->orderBy('created_at', 'desc')->get();
        return response()->json($products);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('status', true)->orderBy('name')->get();
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
            'category_id' => 'required|exists:categories,id'
        ]);

        $data = $request->only([
            'name', 'description', 'price', 'discount_price', 'stock_quantity',
            'status', 'pre_order_days', 'is_featured', 'is_best_seller', 'category_id'
        ]);
        $data['slug'] = Str::slug($request->name);

        // Handle main image upload
        if ($request->hasFile('main_image')) {
            $mainImagePath = $request->file('main_image')->store('products/main', 'public');
            $data['main_image'] = $mainImagePath;
        }

        $product = Product::create($data);

        // Handle additional images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $imagePath = $image->store('products/gallery', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'sort_order' => $index
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
        return response()->json($product->load('category', 'images'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::where('status', true)->orderBy('name')->get();
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
            'category_id' => 'required|exists:categories,id'
        ]);

        $data = $request->only([
            'name', 'description', 'price', 'discount_price', 'stock_quantity',
            'status', 'pre_order_days', 'is_featured', 'is_best_seller', 'category_id'
        ]);

        // Update slug if name changed
        if ($request->name !== $product->name) {
            $data['slug'] = Str::slug($request->name);
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

        // Handle additional images
        if ($request->hasFile('images')) {
            // Get current max sort order
            $maxSortOrder = $product->images()->max('sort_order') ?? -1;

            foreach ($request->file('images') as $index => $image) {
                $imagePath = $image->store('products/gallery', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'sort_order' => $maxSortOrder + $index + 1
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
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

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
            'images.*.sort_order' => 'required|integer'
        ]);

        foreach ($request->images as $imageData) {
            ProductImage::where('id', $imageData['id'])->update(['sort_order' => $imageData['sort_order']]);
        }

        return response()->json(['success' => true]);
    }
}
