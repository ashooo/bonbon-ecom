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
    private const CUSTOMIZATION_PRICE_ADJUSTMENTS = [
        'size' => [
            '6' => 0,
            '8' => 250,
            '10' => 500,
            '12' => 850,
        ],
        'layers' => [
            '1' => 0,
            '2' => 180,
            '3' => 320,
            '4' => 480,
        ],
        'frosting' => [
            'buttercream' => 0,
            'whipped' => 80,
            'fondant' => 220,
            'ganache' => 160,
        ],
        'topper' => [
            'none' => 0,
            'name' => 120,
            'acrylic' => 200,
            'edible_print' => 180,
        ],
        'rush' => [
            'no' => 0,
            'yes' => 350,
        ],
    ];

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
            'special_instructions' => 'nullable|string|max:2000',
            'customization' => 'nullable|array',
            'customization.sponge' => 'nullable|string|max:100',
            'customization.filling' => 'nullable|string|max:100',
            'customization.frosting' => 'nullable|string|max:100',
            'customization.layers' => 'nullable|in:1,2,3,4',
            'customization.shape' => 'nullable|string|max:100',
            'customization.size' => 'nullable|in:6,8,10,12',
            'customization.theme' => 'nullable|string|max:100',
            'customization.message' => 'nullable|string|max:120',
            'customization.topper' => 'nullable|in:none,name,acrylic,edible_print',
            'customization.rush' => 'nullable|in:no,yes',
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

        $basePrice = (float) $product->effective_price;
        $customizationPayload = $this->sanitizeCustomizationPayload((array) $request->input('customization', []));
        $customizationAdjustment = $this->calculateCustomizationAdjustment($customizationPayload);
        $unitPrice = $basePrice + (float) $variant?->price_adjustment + $customizationAdjustment;

        $existingItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('variant_id', $variant?->id)
            ->where('customization_payload', json_encode($customizationPayload))
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
                'customization_payload' => $customizationPayload,
            ]);
        }

        return $this->redirectWithCartToken($request, 'cart.index', 'Item added to cart!');
    }

    private function sanitizeCustomizationPayload(array $raw): array
    {
        $allowedKeys = ['sponge', 'filling', 'frosting', 'layers', 'shape', 'size', 'theme', 'message', 'topper', 'rush'];
        $payload = [];

        foreach ($allowedKeys as $key) {
            $value = $raw[$key] ?? null;
            if (! is_string($value)) {
                continue;
            }

            $trimmed = trim($value);
            if ($trimmed === '') {
                continue;
            }

            $payload[$key] = $trimmed;
        }

        ksort($payload);

        return $payload;
    }

    private function calculateCustomizationAdjustment(array $payload): float
    {
        $adjustment = 0.0;

        foreach (self::CUSTOMIZATION_PRICE_ADJUSTMENTS as $key => $options) {
            $selected = $payload[$key] ?? null;
            if (! is_string($selected)) {
                continue;
            }

            $adjustment += (float) ($options[$selected] ?? 0);
        }

        return $adjustment;
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
