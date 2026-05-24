<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    private ?string $guestCartToken = null;

    private function resolveGuestCartToken(Request $request): string
    {
        $token = trim((string) ($request->header('X-Cart-Token')
            ?? $request->query('cart_token')
            ?? $request->cookie('cart_token')
            ?? ''));

        return $token !== '' ? $token : Str::random(40);
    }

    private function getCart(Request $request): Cart
    {
        if (Auth::check()) {
            $this->guestCartToken = null;

            return Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['expires_at' => now()->addDays(30)]
            )->load('items.product', 'items.variant');
        }

        $this->guestCartToken = $this->resolveGuestCartToken($request);

        return Cart::firstOrCreate(
            ['session_id' => $this->guestCartToken],
            ['user_id' => null, 'expires_at' => now()->addDays(30)]
        )->load('items.product', 'items.variant');
    }

    private function responseWithCartToken(array $payload)
    {
        if ($this->guestCartToken) {
            $payload['cart_token'] = $this->guestCartToken;
        }

        $response = response()->json($payload);

        if ($this->guestCartToken) {
            $response->cookie('cart_token', $this->guestCartToken, 60 * 24 * 30);
        }

        return $response;
    }

    public function index(Request $request)
    {
        $cart = $this->getCart($request);

        return $this->responseWithCartToken([
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

        $cart = $this->getCart($request);
        $product = Product::findOrFail($request->integer('product_id'));
        $variant = $request->filled('variant_id') ? Variant::findOrFail($request->integer('variant_id')) : null;

        if ($variant && (int) $variant->product_id !== (int) $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Selected variant does not belong to the selected product.',
            ], 422);
        }

        $unitPrice = $variant ? (float) $variant->price_adjustment : (float) $product->effective_price;

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

        return $this->responseWithCartToken([
            'success' => true,
            'message' => 'Product added to cart successfully',
        ]);
    }

    public function update(Request $request, string $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->getCart($request);
        $cartItem = CartItem::findOrFail($item);

        if ((int) $cartItem->cart_id !== (int) $cart->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $cartItem->update(['quantity' => $request->integer('quantity')]);

        return $this->responseWithCartToken([
            'success' => true,
            'message' => 'Cart updated successfully',
        ]);
    }

    public function remove(Request $request, string $item)
    {
        $cart = $this->getCart($request);
        $cartItem = CartItem::findOrFail($item);

        if ((int) $cartItem->cart_id !== (int) $cart->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $cartItem->delete();

        return $this->responseWithCartToken([
            'success' => true,
            'message' => 'Item removed from cart successfully',
        ]);
    }

    public function clear(Request $request)
    {
        $cart = $this->getCart($request);
        $cart->items()->delete();

        return $this->responseWithCartToken([
            'success' => true,
            'message' => 'Cart cleared successfully',
        ]);
    }
}
