<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    private function resolveCart(Request $request): ?Cart
    {
        if (Auth::check()) {
            return Cart::query()
                ->where('user_id', Auth::id())
                ->with('items.variant.product')
                ->first();
        }

        $token = trim((string) ($request->header('X-Cart-Token')
            ?? $request->query('cart_token')
            ?? $request->cookie('cart_token')
            ?? ''));

        if ($token === '') {
            return null;
        }

        return Cart::query()
            ->where('session_id', $token)
            ->with('items.variant.product')
            ->first();
    }

    public function index()
    {
        $orders = Order::query()
            ->where('user_id', Auth::id())
            ->with('items.variant.product')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $orders,
            'message' => 'Orders retrieved successfully',
        ]);
    }

    public function show(Order $order)
    {
        if ((int) $order->user_id !== (int) Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $order->load('items.variant.product');

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order retrieved successfully',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_email' => 'required|email|max:100',
            'customer_phone' => 'required|string|max:20',
            'order_type' => 'required|in:pickup,delivery',
            'fulfillment_date' => 'required|date',
            'fulfillment_time' => 'nullable',
            'address_id' => 'nullable|exists:addresses,id',
            'delivery_address' => 'required_if:order_type,delivery',
            'special_instructions' => 'nullable|string',
        ]);

        $cart = $this->resolveCart($request);

        if (! $cart || $cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty',
            ], 400);
        }

        DB::beginTransaction();

        try {
            $subtotal = $cart->items->sum(fn ($item) => $item->quantity * $item->unit_price);
            $deliveryFee = $request->string('order_type')->value() === 'delivery' ? 50.00 : 0;
            $total = $subtotal + $deliveryFee;

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'user_id' => Auth::id(),
                'customer_name' => $request->string('customer_name')->value(),
                'customer_email' => $request->string('customer_email')->value(),
                'customer_phone' => $request->string('customer_phone')->value(),
                'order_type' => $request->string('order_type')->value(),
                'fulfillment_date' => $request->date('fulfillment_date'),
                'fulfillment_time' => $request->input('fulfillment_time'),
                'address_id' => Auth::check() ? ($request->integer('address_id') ?: null) : null,
                'delivery_address' => $request->string('delivery_address')->value(),
                'delivery_fee' => $deliveryFee,
                'subtotal' => $subtotal,
                'total' => $total,
                'special_instructions' => $request->string('special_instructions')->value(),
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            foreach ($cart->items as $item) {
                $variantId = $item->variant_id
                    ?? $item->product?->variants()->where('is_default', true)->value('id')
                    ?? $item->product?->variants()->value('id');

                if (! $variantId) {
                    continue;
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'variant_id' => $variantId,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->quantity * $item->unit_price,
                    'special_instructions' => $item->special_instructions,
                ]);

                $variant = $item->variant ?: $item->product?->variants()->find($variantId);
                if ($variant) {
                    $variant->decrement('stock_quantity', $item->quantity);
                }
            }

            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            $response = response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Order placed successfully',
            ], 201);

            if (! Auth::check()) {
                $response->cookie('cart_token', '', -1);
            }

            return $response;
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while placing the order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function cancel(Order $order)
    {
        if ((int) $order->user_id !== (int) Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if (! in_array($order->status, ['pending', 'confirmed'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled',
            ], 400);
        }

        $order->update(['status' => 'cancelled']);

        foreach ($order->items as $item) {
            if ($item->variant) {
                $item->variant->increment('stock_quantity', $item->quantity);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
        ]);
    }
}
