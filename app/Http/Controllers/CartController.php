<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Cart;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\Variant;
use App\Support\CustomizationPricing;
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

    private function redirectBackWithCartToken(Request $request, string $message): \Illuminate\Http\RedirectResponse
    {
        $response = redirect()->back()->with('success', $message);

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
        $settings = StoreSetting::query()->first();
        $subtotal = $cart->subtotal;
        $delivery = (float) ($settings?->delivery_fee ?? 5.99);
        $taxRate = (float) ($settings?->tax_rate ?? 10.0);
        $serviceFee = (float) ($settings?->service_fee ?? 0.0);
        $tax = $subtotal * ($taxRate / 100);
        $total = $subtotal + $delivery + $tax + $serviceFee;

        $response = response()->view('pages.cart', compact('cart', 'items', 'subtotal', 'delivery', 'tax', 'total', 'taxRate', 'serviceFee'));

        if ($this->guestCartToken) {
            $response->cookie('cart_token', $this->guestCartToken, 60 * 24 * 30);
        }

        return $response;
    }

    public function cartJson(Request $request)
    {
        $cart = $this->getCart($request);
        $items = $cart->items;
        $settings = StoreSetting::query()->first();

        $subtotal = (float) $cart->subtotal;
        $delivery = (float) ($settings?->delivery_fee ?? 5.99);
        $taxRate = (float) ($settings?->tax_rate ?? 10.0);
        $serviceFee = (float) ($settings?->service_fee ?? 0.0);
        $tax = $subtotal * ($taxRate / 100);
        $total = $subtotal + $delivery + $tax + $serviceFee;

        return response()->json([
            'count' => (int) $items->sum('quantity'),
            'items' => $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->product?->name ?? (($item->customization_payload['item_name'] ?? null) ?: 'Custom Cake'),
                    'variant' => $item->variant?->name ?? (($item->product || $item->variant) ? 'N/A' : 'Custom Design'),
                    'image' => $item->product?->main_image_url,
                    'quantity' => (int) $item->quantity,
                    'subtotal' => (float) ($item->quantity * $item->unit_price),
                ];
            })->values(),
            'subtotal' => $subtotal,
            'delivery' => $delivery,
            'tax' => $tax,
            'service_fee' => $serviceFee,
            'total' => $total,
        ]);
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
            'customization.toppings' => 'nullable|string|max:50000',
            'customization.preview_svg' => 'nullable|string|max:120000',
            'customization.preview_image' => 'nullable|string|max:2000000',
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

        if ($request->expectsJson() || $request->ajax()) {
            $cart->load('items.product', 'items.variant');
            return $this->cartJsonResponse($cart, $request);
        }

        return $this->redirectBackWithCartToken($request, 'Item added to cart!');
    }


    private function cartJsonResponse(Cart $cart, Request $request)
    {
        $items = $cart->items->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->product?->name ?? ($item->customization_payload['item_name'] ?? 'Custom Cake'),
                'variant' => $item->variant?->name ?? 'Default',
                'image' => $item->product?->main_image_url ?? null,
                'unit_price' => (float) $item->unit_price,
                'quantity' => (int) $item->quantity,
                'subtotal' => (float) ($item->unit_price * $item->quantity),
            ];
        });

        $subtotal = (float) $cart->subtotal;
        $delivery = 5.99;
        $tax = $subtotal * 0.1;
        $total = $subtotal + $delivery + $tax;

        $response = response()->json([
            'items' => $items,
            'count' => $items->sum('quantity'),
            'subtotal' => $subtotal,
            'delivery' => $delivery,
            'tax' => $tax,
            'total' => $total,
        ]);

        if ($this->guestCartToken) {
            $response->cookie('cart_token', $this->guestCartToken, 60 * 24 * 30);
        }

        return $response;
    }

    private function sanitizeCustomizationPayload(array $raw): array
    {
        $allowedKeys = ['sponge', 'filling', 'frosting', 'frosting_custom', 'layers', 'shape', 'size', 'theme', 'message', 'drip', 'toppings', 'preview_svg', 'preview_image', 'topper', 'rush'];
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

            if ($key === 'preview_image') {
                $sanitized = $this->sanitizePreviewImage($trimmed);
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

    private function sanitizePreviewImage(string $value): string
    {
        if (! preg_match('/^data:image\/(png|jpe?g|webp);base64,/i', $value)) {
            return '';
        }

        $parts = explode(',', $value, 2);
        if (count($parts) !== 2) {
            return '';
        }

        $decoded = base64_decode($parts[1], true);
        if ($decoded === false || $decoded === '') {
            return '';
        }

        if (strlen($decoded) > 1_500_000) {
            return '';
        }

        return $parts[0] . ',' . base64_encode($decoded);
    }

    private function calculateCustomizationAdjustment(array $payload): float
    {
        $settings = StoreSetting::query()->first();
        $pricing = CustomizationPricing::mergeWithDefaults($settings?->customization_pricing);
        $adjustment = 0.0;

        foreach ($pricing as $key => $options) {
            if (! is_array($options)) {
                continue;
            }
            $selected = $payload[$key] ?? null;
            if (! is_string($selected)) {
                continue;
            }

            $adjustment += (float) ($options[$selected] ?? 0);
        }

        if (isset($payload['toppings']) && is_string($payload['toppings'])) {
            $parsed = json_decode($payload['toppings'], true);
            if (is_array($parsed)) {
                $perPiece = (float) ($pricing['toppings']['per_piece'] ?? 0);
                $adjustment += count($parsed) * $perPiece;
            }
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
