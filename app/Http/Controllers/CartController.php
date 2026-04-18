<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to view your cart.');
        }

        $cart = Auth::user()->getOrCreateCart();
        $items = $cart->items;
        $subtotal = $cart->subtotal;
        $delivery = 5.99;
        $tax = $subtotal * 0.1;
        $total = $subtotal + $delivery + $tax;

        return view('pages.cart', compact('cart', 'items', 'subtotal', 'delivery', 'tax', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'unit_price' => 'required|numeric|min:0.01',
            'quantity' => 'nullable|integer|min:1',
            'special_instructions' => 'nullable|string|max:255',
        ]);

        $cart = Auth::user()->getOrCreateCart();
        $quantity = $request->input('quantity', 1);
        $product = Product::findOrFail($request->integer('product_id'));
        $variantId = $request->integer('variant_id') ?: null;

        $existingItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('variant_id', $variantId)
            ->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'variant_id' => $variantId,
                'unit_price' => $request->unit_price,
                'quantity' => $quantity,
                'special_instructions' => $request->input('special_instructions'),
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Item added to cart!');
    }

    public function updateQuantity(CartItem $item, Request $request)
    {
        if ($item->cart->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item->update(['quantity' => $request->quantity]);

        return redirect()->route('cart.index')->with('success', 'Quantity updated!');
    }

    public function remove(CartItem $item)
    {
        if ($item->cart->user_id !== Auth::id()) {
            abort(403);
        }

        $item->delete();

        return redirect()->route('cart.index')->with('success', 'Item removed from cart!');
    }

    public function clear()
    {
        $cart = Auth::user()->getOrCreateCart();
        $cart->items()->delete();

        return redirect()->route('cart.index')->with('success', 'Cart cleared!');
    }

    public function increment(CartItem $item)
    {
        if ($item->cart->user_id !== Auth::id()) {
            abort(403);
        }

        $item->increment('quantity');

        return back();
    }

    public function decrement(CartItem $item)
    {
        if ($item->cart->user_id !== Auth::id()) {
            abort(403);
        }

        if ($item->quantity > 1) {
            $item->decrement('quantity');
        } else {
            $item->delete();
        }

        return back();
    }
}
