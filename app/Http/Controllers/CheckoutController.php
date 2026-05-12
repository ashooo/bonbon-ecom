<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Variant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
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

    private function getMaxPreOrderDays(Cart $cart): int
    {
        return (int) $cart->items
            ->filter(fn ($item) => (bool) ($item->product?->is_preorder))
            ->max(fn ($item) => (int) ($item->product?->pre_order_days ?? 0));
    }

    private function parseGuestOrderNumbers(Request $request): array
    {
        $raw = $request->cookie('guest_orders', '[]');
        $decoded = json_decode((string) $raw, true);

        if (! is_array($decoded)) {
            return [];
        }

        $normalized = array_map(
            fn ($value) => strtoupper(trim((string) $value)),
            $decoded
        );

        $filtered = array_values(array_filter($normalized, fn ($value) => $value !== ''));

        return array_slice(array_values(array_unique($filtered)), 0, 20);
    }

    private function resolveVariantForCartItem($item): ?Variant
    {
        if (! $item->product_id && ! $item->variant_id) {
            return null;
        }

        if ($item->variant_id) {
            return Variant::query()->find($item->variant_id);
        }

        $product = $item->product;
        if (! $product) {
            return null;
        }

        return $product->variants()->where('is_default', true)->first()
            ?? $product->variants()->first();
    }

    public function index(Request $request)
    {
        $cart = $this->getCart($request);
        $items = $cart->items;
        $maxPreOrderDays = $this->getMaxPreOrderDays($cart);
        $minFulfillmentAt = now()->addDays($maxPreOrderDays);
        $minFulfillmentDate = $minFulfillmentAt->toDateString();
        $minFulfillmentTime = $minFulfillmentAt->format('H:i');
        $subtotal = $cart->subtotal;
        $delivery = 0.0;
        $tax = $subtotal * 0.1;
        $total = $subtotal + $delivery + $tax;

        $savedAddresses = Auth::check()
            ? Auth::user()->addresses()->latest()->get()
            : collect();

        $response = response()->view('pages.checkout', compact(
            'cart',
            'items',
            'subtotal',
            'delivery',
            'tax',
            'total',
            'maxPreOrderDays',
            'minFulfillmentDate',
            'minFulfillmentTime',
            'savedAddresses'
        ));

        if ($this->guestCartToken) {
            $response->cookie('cart_token', $this->guestCartToken, 60 * 24 * 30);
        }

        return $response;
    }

    public function store(Request $request)
    {
        $cart = $this->getCart($request);

        if ($cart->items->isEmpty()) {
            return redirect()->route('checkout.index')->withErrors([
                'checkout' => 'Your cart is empty.',
            ]);
        }

        $maxPreOrderDays = $this->getMaxPreOrderDays($cart);
        $minFulfillmentAt = now()->addDays($maxPreOrderDays);
        $minFulfillmentDate = $minFulfillmentAt->toDateString();

        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_email' => 'required|email|max:100',
            'customer_phone' => 'required|string|max:20',
            'order_type' => 'required|in:pickup,delivery',
            'delivery_address' => 'required_if:order_type,delivery|string',
            'fulfillment_date' => 'required|date|after_or_equal:' . $minFulfillmentDate,
            'fulfillment_time' => 'required|date_format:H:i',
            'special_instructions' => 'nullable|string',
        ]);

        $selectedFulfillmentAt = Carbon::createFromFormat(
            'Y-m-d H:i',
            $request->string('fulfillment_date')->value() . ' ' . $request->string('fulfillment_time')->value()
        );

        if ($selectedFulfillmentAt->lt($minFulfillmentAt)) {
            return redirect()->route('checkout.index')->withErrors([
                'fulfillment_date' => 'Selected fulfillment date/time is too early based on pre-order lead time.',
            ])->withInput();
        }

        foreach ($cart->items as $item) {
            $resolvedVariant = $this->resolveVariantForCartItem($item);

            if (! $item->product_id && ! $item->variant_id) {
                continue;
            }

            if (! $resolvedVariant) {
                return redirect()->route('checkout.index')->withErrors([
                    'checkout' => 'Some cart items are unavailable for checkout. Please review your cart and try again.',
                ])->withInput();
            }

            if ((int) $resolvedVariant->stock_quantity < (int) $item->quantity) {
                return redirect()->route('checkout.index')->withErrors([
                    'checkout' => 'Insufficient stock for ' . ($item->product?->name ?? 'an item') . '.',
                ])->withInput();
            }
        }

        DB::beginTransaction();

        try {
            $subtotal = (float) $cart->items->sum(fn ($item) => $item->quantity * $item->unit_price);
            $deliveryFee = $request->string('order_type')->value() === 'delivery' ? 5.99 : 0.0;
            $tax = $subtotal * 0.1;
            $total = $subtotal + $deliveryFee + $tax;

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'user_id' => Auth::id(),
                'customer_name' => $request->string('customer_name')->value(),
                'customer_email' => $request->string('customer_email')->value(),
                'customer_phone' => $request->string('customer_phone')->value(),
                'order_type' => $request->string('order_type')->value(),
                'fulfillment_date' => $request->string('fulfillment_date')->value(),
                'fulfillment_time' => $request->string('fulfillment_time')->value() . ':00',
                'address_id' => null,
                'delivery_address' => $request->string('delivery_address')->value(),
                'delivery_fee' => $deliveryFee,
                'subtotal' => $subtotal,
                'total' => $total,
                'special_instructions' => $request->string('special_instructions')->value(),
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            foreach ($cart->items as $item) {
                $variant = $this->resolveVariantForCartItem($item);
                $isCustomOnlyItem = ! $item->product_id && ! $item->variant_id;
                if (! $variant && ! $isCustomOnlyItem) {
                    throw new \RuntimeException('Unable to resolve a product variant for checkout.');
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'variant_id' => $variant?->id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->quantity * $item->unit_price,
                    'special_instructions' => $item->special_instructions,
                    'customization_payload' => $item->customization_payload,
                ]);

                if ($isCustomOnlyItem || ! $variant) {
                    continue;
                }

                $previousStock = (int) $variant->stock_quantity;
                $newStock = max(0, $previousStock - (int) $item->quantity);
                $variant->update(['stock_quantity' => $newStock]);

                InventoryMovement::create([
                    'variant_id' => $variant->id,
                    'product_id' => $variant->product_id,
                    'acted_by_user_id' => Auth::id(),
                    'type' => 'order_deduction',
                    'quantity_change' => -1 * (int) $item->quantity,
                    'previous_stock' => $previousStock,
                    'new_stock' => $newStock,
                    'reason' => 'Order ' . $order->order_number,
                ]);
            }

            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            $response = redirect()
                ->route('orders.index')
                ->with('success', 'Order placed successfully! Order number: ' . $order->order_number);

            if (! Auth::check()) {
                $guestOrders = $this->parseGuestOrderNumbers($request);
                array_unshift($guestOrders, $order->order_number);
                $guestOrders = array_slice(array_values(array_unique($guestOrders)), 0, 20);

                $response->cookie('guest_orders', json_encode($guestOrders), 60 * 24 * 180);
                $response->cookie('cart_token', '', -1);
            }

            return $response;
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->route('checkout.index')->withErrors([
                'checkout' => 'Something went wrong while placing your order. Please try again.',
            ])->withInput();
        }
    }
}
