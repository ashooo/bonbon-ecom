<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
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
            'product_name' => 'required|string',
            'product_size' => 'nullable|string',
            'product_image' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0.01',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $cart = Auth::user()->getOrCreateCart();
        $quantity = $request->input('quantity', 1);

        $existingItem = $cart->items()
            ->where('product_name', $request->product_name)
            ->where('product_size', $request->product_size)
            ->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'product_name' => $request->product_name,
                'product_size' => $request->product_size,
                'product_image' => $request->product_image,
                'unit_price' => $request->unit_price,
                'quantity' => $quantity,
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
