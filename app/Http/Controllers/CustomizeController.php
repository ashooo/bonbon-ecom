<?php

namespace App\Http\Controllers;

use App\Models\Product;

class CustomizeController extends Controller
{
    public function index()
    {
        $products = Product::query()
            ->with(['variants' => fn ($query) => $query->where('is_active', true)->orderBy('display_order')])
            ->where('is_active', true)
            ->where('allows_customization', true)
            ->orderBy('name')
            ->get();

        if ($products->isEmpty()) {
            $products = Product::query()
                ->with(['variants' => fn ($query) => $query->where('is_active', true)->orderBy('display_order')])
                ->where('is_active', true)
                ->orderBy('name')
                ->take(12)
                ->get();
        }

        return view('pages.customize', compact('products'));
    }
}
