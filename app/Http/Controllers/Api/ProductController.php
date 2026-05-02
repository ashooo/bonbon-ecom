<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants', 'images'])->active();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->boolean('on_sale')) {
            $query->whereNotNull('sale_price')
                ->whereColumn('sale_price', '<', 'price');
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->string('search')->value() . '%');
        }

        $allowedSortFields = ['created_at', 'name', 'price'];
        $sortField = $request->string('sort_by', 'created_at')->value();
        $sortOrder = strtolower($request->string('sort_order', 'desc')->value()) === 'asc' ? 'asc' : 'desc';

        if (! in_array($sortField, $allowedSortFields, true)) {
            $sortField = 'created_at';
        }

        $products = $query->orderBy($sortField, $sortOrder)->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Products retrieved successfully',
        ]);
    }

    public function show(string $slug)
    {
        $product = Product::with(['category', 'variants', 'images'])
            ->where('slug', $slug)
            ->active()
            ->first();

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $related = Product::query()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->limit(4)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'product' => $product,
                'related' => $related,
            ],
            'message' => 'Product retrieved successfully',
        ]);
    }
}
