<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Variant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function getCart(): Cart
    {
        $user = Auth::user();

        return Cart::firstOrCreate(
            ['user_id' => $user->id],
            ['expires_at' => now()->addDays(30)]
        )->load('items.product', 'items.variant');
    }

    public function index()
    {
        $cart = $this->getCart();

        return response()->json([
            'success' => true,
            'data' => [
                'cart' => $cart,
                'total' => $cart->items->sum(fn ($item) => $item->quantity * $item->unit_price),
                'item_count' => $cart->items->sum('quantity'),
            ],
            'message' => 'Cart retrieved successfully',
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'variant_id' => 'nullable|exists:product_variants,id',
            'special_instructions' => 'nullable|string|max:255',
        ]);

        $cart = $this->getCart();
        $product = Product::findOrFail($request->integer('product_id'));
        $variant = $request->filled('variant_id') ? Variant::findOrFail($request->integer('variant_id')) : null;

        $unitPrice = (float) ($product->sale_price ?? $product->price);
        if ($variant) {
            $unitPrice += (float) $variant->price_adjustment;
        }

        $cartItem = CartItem::query()
            ->where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('variant_id', $variant?->id)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $request->integer('quantity'));
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'quantity' => $request->integer('quantity'),
                'unit_price' => $unitPrice,
                'special_instructions' => $request->string('special_instructions')->value(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully',
        ]);
    }

    public function update(Request $request, string $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->getCart();
        $cartItem = CartItem::findOrFail($item);

        if ($cartItem->cart_id !== $cart->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $cartItem->update(['quantity' => $request->integer('quantity')]);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
        ]);
    }

    public function remove(string $item)
    {
        $cart = $this->getCart();
        $cartItem = CartItem::findOrFail($item);

        if ($cartItem->cart_id !== $cart->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart successfully',
        ]);
    }

    public function clear()
    {
        $cart = $this->getCart();
        $cart->items()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully',
        ]);
    }
}
