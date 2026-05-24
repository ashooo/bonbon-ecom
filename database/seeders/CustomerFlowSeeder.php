<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerFlowSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()
            ->where('is_admin', false)
            ->orderBy('id')
            ->get();

        if ($users->isEmpty()) {
            return;
        }

        $products = Product::query()->with('variants')->take(2)->get();
        $variants = Variant::query()->with('product')->take(2)->get();

        foreach ($users as $user) {
            $address = Address::updateOrCreate(
                ['user_id' => $user->id, 'label' => 'Home'],
                [
                    'line1' => '123 Jupiter Street',
                    'line2' => 'Unit 5B',
                    'city' => 'Makati',
                    'state' => 'Metro Manila',
                    'postal_code' => '1209',
                    'country' => 'Philippines',
                    'is_default' => true,
                ]
            );

            PaymentMethod::updateOrCreate(
                ['user_id' => $user->id, 'last_four' => '4242'],
                [
                    'card_brand' => 'Visa',
                    'card_holder_name' => $user->name,
                    'expiry_month' => 12,
                    'expiry_year' => now()->year + 2,
                    'is_default' => true,
                ]
            );

            $cart = Cart::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'session_id' => Str::random(40),
                    'expires_at' => now()->addDays(30),
                ]
            );

            foreach ($products as $index => $product) {
                $variant = $product->variants->first();
                if (! $variant) {
                    continue;
                }

                CartItem::updateOrCreate(
                    [
                        'cart_id' => $cart->id,
                        'product_id' => $product->id,
                        'variant_id' => $variant->id,
                    ],
                    [
                        'quantity' => $index + 1,
                        'unit_price' => ($product->sale_price ?? $product->price) + $variant->price_adjustment,
                        'special_instructions' => $index === 0 ? 'Less sweet frosting, please.' : null,
                    ]
                );
            }

            $orderNumber = 'ORD-DEMO-' . str_pad((string) $user->id, 4, '0', STR_PAD_LEFT);

            $order = Order::updateOrCreate(
                ['order_number' => $orderNumber],
                [
                    'user_id' => $user->id,
                    'customer_name' => $user->name,
                    'customer_email' => $user->email,
                    'customer_phone' => $user->phone ?? '09171234567',
                    'order_type' => 'delivery',
                    'fulfillment_date' => now()->addDay()->toDateString(),
                    'fulfillment_time' => '14:00:00',
                    'address_id' => $address->id,
                    'delivery_address' => '123 Jupiter Street, Unit 5B, Makati City',
                    'delivery_fee' => 80,
                    'subtotal' => 0,
                    'total' => 0,
                    'special_instructions' => 'Please ring the bell once.',
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                ]
            );

            $order->items()->delete();

            $subtotal = 0;
            foreach ($variants as $index => $variant) {
                if (! $variant->product) {
                    continue;
                }

                $quantity = $index + 1;
                $unitPrice = ($variant->product->sale_price ?? $variant->product->price) + $variant->price_adjustment;
                $lineSubtotal = $unitPrice * $quantity;
                $subtotal += $lineSubtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $lineSubtotal,
                    'special_instructions' => $index === 0 ? 'Birthday dedication on top.' : null,
                ]);
            }

            $order->update([
                'subtotal' => $subtotal,
                'total' => $subtotal + (float) $order->delivery_fee,
            ]);
        }
    }
}
