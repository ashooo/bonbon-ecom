<?php

namespace App\Http\Controllers;

use App\Mail\OrderReceiptMail;
use App\Models\Cart;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StoreSetting;
use App\Models\Variant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
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
        $settings = StoreSetting::query()->first();
        $configuredDeliveryFee = (float) ($settings?->delivery_fee ?? 5.99);
        $configuredTaxRate = (float) ($settings?->tax_rate ?? 10.0);
        $configuredServiceFee = (float) ($settings?->service_fee ?? 0.0);
        $maxPreOrderDays = $this->getMaxPreOrderDays($cart);
        $minFulfillmentAt = now()->addDays($maxPreOrderDays);
        $minFulfillmentDate = $minFulfillmentAt->toDateString();
        $minFulfillmentTime = $minFulfillmentAt->format('H:i');
        $subtotal = $cart->subtotal;
        $delivery = 0.0;
        $tax = $subtotal * ($configuredTaxRate / 100);
        $serviceFee = $configuredServiceFee;
        $total = $subtotal + $delivery + $tax + $serviceFee;

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
            'serviceFee',
            'configuredDeliveryFee',
            'configuredTaxRate',
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

        $paymentMethodRule = Auth::check()
            ? 'required|in:cod,paymongo'
            : 'required|in:paymongo';

        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_email' => 'required|email|max:100',
            'customer_phone' => 'required|string|max:20',
            'order_type' => 'required|in:pickup,delivery',
            'delivery_address' => 'nullable|string|required_if:order_type,delivery',
            'payment_method' => $paymentMethodRule,
            'fulfillment_date' => 'required|date|after_or_equal:' . $minFulfillmentDate,
            'fulfillment_time' => 'required|date_format:H:i',
            'special_instructions' => 'nullable|string',
        ]);

        if (! Auth::check() && $request->string('payment_method')->value() === 'cod') {
            return redirect()->route('checkout.index')->withErrors([
                'payment_method' => 'Cash on Delivery is not available for guest checkout. Please use QRPH Online Payment.',
            ])->withInput();
        }

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
            $orderType = $request->string('order_type')->value();
            $settings = StoreSetting::query()->first();
            $configuredDeliveryFee = (float) ($settings?->delivery_fee ?? 5.99);
            $configuredTaxRate = (float) ($settings?->tax_rate ?? 10.0);
            $configuredServiceFee = (float) ($settings?->service_fee ?? 0.0);
            $deliveryAddress = $orderType === 'pickup'
                ? Order::STORE_PICKUP_LOCATION_URL
                : trim($request->string('delivery_address')->value());
            $subtotal = (float) $cart->items->sum(fn ($item) => $item->quantity * $item->unit_price);
            $deliveryFee = $orderType === 'delivery' ? $configuredDeliveryFee : 0.0;
            $tax = $subtotal * ($configuredTaxRate / 100);
            $total = $subtotal + $deliveryFee + $tax + $configuredServiceFee;

            $paymentMethod = $request->string('payment_method')->value();
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'user_id' => Auth::id(),
                'customer_name' => $request->string('customer_name')->value(),
                'customer_email' => $request->string('customer_email')->value(),
                'customer_phone' => $request->string('customer_phone')->value(),
                'order_type' => $orderType,
                'fulfillment_date' => $request->string('fulfillment_date')->value(),
                'fulfillment_time' => $request->string('fulfillment_time')->value() . ':00',
                'address_id' => null,
                'delivery_address' => $deliveryAddress,
                'delivery_fee' => $deliveryFee,
                'subtotal' => $subtotal,
                'total' => $total,
                'special_instructions' => $request->string('special_instructions')->value(),
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $paymentMethod,
                'stock_deducted_at' => null,
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
            }

            if ($paymentMethod === 'cod') {
                $this->deductOrderStock($order, Auth::id());
            }

            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            if ($order->payment_method === 'paymongo') {
                $checkoutUrl = $this->createPaymongoCheckoutSession($order);
                if ($checkoutUrl) {
                    $redirect = redirect()->away($checkoutUrl);

                    if (! Auth::check()) {
                        $guestOrders = $this->parseGuestOrderNumbers($request);
                        array_unshift($guestOrders, $order->order_number);
                        $guestOrders = array_slice(array_values(array_unique($guestOrders)), 0, 20);
                        $redirect->cookie('guest_orders', json_encode($guestOrders), 60 * 24 * 180);
                        $redirect->cookie('cart_token', '', -1);
                    }

                    return $redirect;
                }

                return redirect()
                    ->route('orders.index')
                    ->with('error', 'Order created, but QRPH checkout could not be initialized. Please contact support or choose COD.');
            }

            $response = redirect()
                ->route('orders.index')
                ->with('success', 'Order placed successfully! Order number: ' . $order->order_number);

            $this->sendOrderReceiptEmail($order);

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
            Log::error('Checkout order placement failed.', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => Auth::id(),
                'cart_id' => $cart->id ?? null,
                'payment_method' => $request->input('payment_method'),
                'order_type' => $request->input('order_type'),
            ]);

            return redirect()->route('checkout.index')->withErrors([
                'checkout' => 'Something went wrong while placing your order. Please try again.',
            ])->withInput();
        }
    }

    public function paymongoSuccess(Request $request, Order $order)
    {
        abort_unless($request->hasValidSignature(), 403);

        $paid = false;
        $message = 'Payment not confirmed.';

        if ($order->payment_method === 'paymongo' && $order->payment_status !== 'paid') {
            try {
                DB::transaction(function () use ($order): void {
                    $this->deductOrderStock($order, null);
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => $order->status === 'pending' ? 'confirmed' : $order->status,
                    ]);
                });

                $paid = true;
                $message = 'QRPH payment completed.';
                $this->sendOrderReceiptEmail($order->fresh(['items.variant.product', 'invoice']));
            } catch (\Throwable $exception) {
                Log::error('Failed to finalize QRPH payment order.', [
                    'order_id' => $order->id,
                    'message' => $exception->getMessage(),
                ]);
                $message = 'Payment was received, but we could not finalize stock processing yet. Support will assist you.';
            }
        } elseif ($order->payment_status === 'paid') {
            $paid = true;
            $message = 'QRPH payment already confirmed.';
        }

        $response = response()->view('pages.payment-result', [
            'order' => $order->fresh(['items.variant.product']),
            'isSuccess' => $paid,
            'message' => $message,
        ]);

        if (! Auth::check()) {
            $response->cookie('guest_orders', json_encode([$order->order_number]), 60 * 24 * 180);
        }

        return $response;
    }

    public function paymongoCancel(Request $request, Order $order)
    {
        abort_unless($request->hasValidSignature(), 403);

        $response = response()->view('pages.payment-result', [
            'order' => $order->fresh(['items.variant.product']),
            'isSuccess' => false,
            'message' => 'QRPH checkout was cancelled.',
        ]);

        if (! Auth::check()) {
            $response->cookie('guest_orders', json_encode([$order->order_number]), 60 * 24 * 180);
        }

        return $response;
    }

    private function deductOrderStock(Order $order, ?int $actorUserId): void
    {
        if ($order->stock_deducted_at) {
            return;
        }

        $order->loadMissing('items.variant.product');

        foreach ($order->items as $item) {
            $variant = $item->variant;
            if (! $variant) {
                // Custom-only order items do not have inventory variants.
                continue;
            }

            $variant->refresh();
            $previousStock = (int) $variant->stock_quantity;
            $orderedQty = (int) $item->quantity;
            if ($previousStock < $orderedQty) {
                throw new \RuntimeException('Insufficient stock while finalizing order.');
            }

            $newStock = $previousStock - $orderedQty;
            $variant->update(['stock_quantity' => $newStock]);

            InventoryMovement::create([
                'variant_id' => $variant->id,
                'product_id' => $variant->product_id,
                'acted_by_user_id' => $actorUserId,
                'type' => 'order_deduction',
                'quantity_change' => -1 * $orderedQty,
                'previous_stock' => $previousStock,
                'new_stock' => $newStock,
                'reason' => 'Order ' . $order->order_number,
            ]);
        }

        $order->update(['stock_deducted_at' => now()]);
    }

    private function sendOrderReceiptEmail(Order $order): void
    {
        try {
            $order->loadMissing(['items.variant.product', 'invoice']);
            Mail::to($order->customer_email)->send(new OrderReceiptMail($order));
        } catch (\Throwable $exception) {
            Log::warning('Failed sending order receipt email.', [
                'order_id' => $order->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function createPaymongoCheckoutSession(Order $order): ?string
    {
        $secretKey = (string) config('services.paymongo.secret_key', '');
        if ($secretKey === '') {
            return null;
        }

        $amount = (int) round((float) $order->total * 100);
        $successUrl = URL::temporarySignedRoute('checkout.paymongo.success', now()->addHours(12), ['order' => $order->id]);
        $cancelUrl = URL::temporarySignedRoute('checkout.paymongo.cancel', now()->addHours(12), ['order' => $order->id]);

        $response = Http::withBasicAuth($secretKey, '')
            ->acceptJson()
            ->post('https://api.paymongo.com/v1/checkout_sessions', [
                'data' => [
                    'attributes' => [
                        'line_items' => [[
                            'currency' => 'PHP',
                            'amount' => $amount,
                            'name' => 'Order ' . $order->order_number,
                            'quantity' => 1,
                        ]],
                        'payment_method_types' => ['qrph'],
                        'description' => 'BonBons order ' . $order->order_number,
                        'success_url' => $successUrl,
                        'cancel_url' => $cancelUrl,
                        'metadata' => [
                            'order_id' => (string) $order->id,
                            'order_number' => $order->order_number,
                        ],
                    ],
                ],
            ]);

        if (! $response->successful()) {
            Log::error('PayMongo checkout session creation failed.', [
                'order_id' => $order->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return data_get($response->json(), 'data.attributes.checkout_url');
    }
}
