<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->whereNull('parent_id')
            ->active()
            ->with('children')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Categories retrieved successfully',
        ]);
    }

    public function show(string $slug)
    {
        $category = Category::query()
            ->where('slug', $slug)
            ->with('parent')
            ->first();

        if (! $category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        $products = $category->products()
            ->with(['variants', 'images'])
            ->active()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => [
                'category' => $category,
                'products' => $products,
            ],
            'message' => 'Category retrieved successfully',
        ]);
    }
}
