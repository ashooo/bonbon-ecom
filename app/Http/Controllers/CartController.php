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
            'product_id' => 'nullable|exists:products,id',
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
            'customization.message' => 'nullable|string|max:50',
            'customization.frosting_custom' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'customization.drip' => 'nullable|in:none,chocolate,white_chocolate,pink,caramel',
            'customization.toppings' => 'nullable|string|max:5000',
            'customization.preview_svg' => 'nullable|string|max:120000',
            'customization.topper' => 'nullable|in:none,name,acrylic,edible_print',
            'customization.rush' => 'nullable|in:no,yes',
        ]);

        $cart = $this->getCart($request);
        $quantity = $request->input('quantity', 1);
        $hasCustomization = is_array($request->input('customization')) && count((array) $request->input('customization')) > 0;
        $hasProductId = $request->filled('product_id');

        if (! $hasProductId && ! $hasCustomization) {
            return back()->withErrors([
                'product_id' => 'Please select a product or provide customization details.',
            ]);
        }

        $product = $hasProductId ? Product::findOrFail($request->integer('product_id')) : null;
        $variant = ($hasProductId && $request->filled('variant_id')) ? Variant::findOrFail($request->integer('variant_id')) : null;

        if ($product && $variant && (int) $variant->product_id !== (int) $product->id) {
            return back()->withErrors([
                'variant_id' => 'Selected variant does not belong to the selected product.',
            ]);
        }

        $basePrice = (float) ($product?->effective_price ?? 0);
        $customizationPayload = $this->sanitizeCustomizationPayload((array) $request->input('customization', []));
        $customizationAdjustment = $this->calculateCustomizationAdjustment($customizationPayload);
        $unitPrice = $basePrice + (float) ($variant?->price_adjustment ?? 0) + $customizationAdjustment;

        if (! $hasProductId && isset($customizationPayload['shape'])) {
            $customizationPayload['item_name'] = 'Custom Cake';
        }

        $existingItemQuery = $cart->items()->where('customization_payload', json_encode($customizationPayload));
        if ($product) {
            $existingItemQuery->where('product_id', $product->id);
        } else {
            $existingItemQuery->whereNull('product_id');
        }
        if ($variant) {
            $existingItemQuery->where('variant_id', $variant->id);
        } else {
            $existingItemQuery->whereNull('variant_id');
        }
        $existingItem = $existingItemQuery->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'cart_id' => $cart->id,
                'product_id' => $product?->id,
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
        $allowedKeys = ['sponge', 'filling', 'frosting', 'frosting_custom', 'layers', 'shape', 'size', 'theme', 'message', 'drip', 'toppings', 'preview_svg', 'topper', 'rush'];
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

            if ($key === 'preview_svg') {
                $sanitized = $this->sanitizePreviewSvg($trimmed);
                if ($sanitized === '') {
                    continue;
                }
                $payload[$key] = $sanitized;
                continue;
            }

            $payload[$key] = $trimmed;
        }

        ksort($payload);

        return $payload;
    }

    private function sanitizePreviewSvg(string $svg): string
    {
        $allowed = '<svg><g><path><ellipse><circle><rect><text><tspan><defs><linearGradient><stop><clipPath><line><polygon><polyline>';
        $clean = strip_tags($svg, $allowed);
        $clean = preg_replace('/on[a-zA-Z]+\s*=\s*("|\').*?("|\')/i', '', $clean) ?? '';
        $clean = preg_replace('/javascript:/i', '', $clean) ?? '';
        return trim($clean);
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
