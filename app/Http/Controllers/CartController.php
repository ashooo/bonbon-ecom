<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Cart;
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
        $token = trim((string) ($request->cookie('cart_token') ?? ''));

        return $token !== '' ? $token : Str::random(40);
    }

    private function getCart(Request $request): Cart
    {
        if (Auth::check()) {
            $this->guestCartToken = null;

            return Auth::user()->getOrCreateCart()->load('items.product', 'items.variant');
        }

        $this->guestCartToken = $this->resolveGuestCartToken($request);

        return Cart::firstOrCreate(
            ['session_id' => $this->guestCartToken],
            ['user_id' => null, 'expires_at' => now()->addDays(30)]
        )->load('items.product', 'items.variant');
    }

    private function redirectWithCartToken(Request $request, string $routeName, string $message): \Illuminate\Http\RedirectResponse
    {
        $response = redirect()->route($routeName)->with('success', $message);

        if (! Auth::check()) {
            $token = $this->guestCartToken ?: $this->resolveGuestCartToken($request);
            $response->cookie('cart_token', $token, 60 * 24 * 30);
        }

        return $response;
    }

    private function ensureItemBelongsToCart(Request $request, CartItem $item): void
    {
        $cart = $this->getCart($request);

        if ((int) $item->cart_id !== (int) $cart->id) {
            abort(403);
        }
    }

    public function index()
    {
        $request = request();
        $cart = $this->getCart($request);
        $items = $cart->items;
        $subtotal = $cart->subtotal;
        $delivery = 5.99;
        $tax = $subtotal * 0.1;
        $total = $subtotal + $delivery + $tax;

        $response = response()->view('pages.cart', compact('cart', 'items', 'subtotal', 'delivery', 'tax', 'total'));

        if ($this->guestCartToken) {
            $response->cookie('cart_token', $this->guestCartToken, 60 * 24 * 30);
        }

        return $response;
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1',
            'special_instructions' => 'nullable|string|max:255',
        ]);

        $cart = $this->getCart($request);
        $quantity = $request->input('quantity', 1);
        $product = Product::findOrFail($request->integer('product_id'));
        $variant = $request->filled('variant_id') ? Variant::findOrFail($request->integer('variant_id')) : null;

        if ($variant && (int) $variant->product_id !== (int) $product->id) {
            return back()->withErrors([
                'variant_id' => 'Selected variant does not belong to the selected product.',
            ]);
        }

        $unitPrice = (float) $product->price;

        $existingItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('variant_id', $variant?->id)
            ->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'special_instructions' => $request->input('special_instructions'),
            ]);
        }

        return $this->redirectWithCartToken($request, 'cart.index', 'Item added to cart!');
    }

    public function updateQuantity(CartItem $item, Request $request)
    {
        $this->ensureItemBelongsToCart($request, $item);

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item->update(['quantity' => $request->quantity]);

        return $this->redirectWithCartToken($request, 'cart.index', 'Quantity updated!');
    }

    public function remove(Request $request, CartItem $item)
    {
        $this->ensureItemBelongsToCart($request, $item);

        $item->delete();

        return $this->redirectWithCartToken($request, 'cart.index', 'Item removed from cart!');
    }

    public function clear(Request $request)
    {
        $cart = $this->getCart($request);
        $cart->items()->delete();

        return $this->redirectWithCartToken($request, 'cart.index', 'Cart cleared!');
    }

    public function increment(Request $request, CartItem $item)
    {
        $this->ensureItemBelongsToCart($request, $item);

        $item->increment('quantity');

        return $this->redirectWithCartToken($request, 'cart.index', 'Quantity updated!');
    }

    public function decrement(Request $request, CartItem $item)
    {
        $this->ensureItemBelongsToCart($request, $item);

        if ($item->quantity > 1) {
            $item->decrement('quantity');
        } else {
            $item->delete();
        }

        return $this->redirectWithCartToken($request, 'cart.index', 'Quantity updated!');
    }
}
