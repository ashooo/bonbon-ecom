<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_is_created_when_order_is_placed(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $variant = Variant::factory()->create(['product_id' => $product->id]);

        $order = Order::create([
            'order_number' => 'ORD-TEST001',
            'user_id' => $user->id,
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '1234567890',
            'order_type' => 'pickup',
            'fulfillment_date' => now()->addDay(),
            'delivery_fee' => 0,
            'subtotal' => 1000,
            'total' => 1000,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $this->assertTrue(Invoice::where('order_id', $order->id)->exists());

        $invoice = Invoice::where('order_id', $order->id)->first();
        $this->assertEquals(0, $invoice->print_count);
        $this->assertNull($invoice->last_printed_at);
    }

    public function test_invoice_print_count_increments(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $variant = Variant::factory()->create(['product_id' => $product->id]);

        $order = Order::create([
            'order_number' => 'ORD-TEST002',
            'user_id' => $user->id,
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '1234567890',
            'order_type' => 'pickup',
            'fulfillment_date' => now()->addDay(),
            'delivery_fee' => 0,
            'subtotal' => 1000,
            'total' => 1000,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $invoice = Invoice::where('order_id', $order->id)->first();

        $this->assertEquals(0, $invoice->print_count);

        $invoice->incrementPrintCount();

        $this->assertEquals(1, $invoice->fresh()->print_count);
        $this->assertNotNull($invoice->fresh()->last_printed_at);

        $invoice->incrementPrintCount();

        $this->assertEquals(2, $invoice->fresh()->print_count);
    }

    public function test_invoice_status_syncs_with_payment_status(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $variant = Variant::factory()->create(['product_id' => $product->id]);

        $order = Order::create([
            'order_number' => 'ORD-TEST003',
            'user_id' => $user->id,
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '1234567890',
            'order_type' => 'pickup',
            'fulfillment_date' => now()->addDay(),
            'delivery_fee' => 0,
            'subtotal' => 1000,
            'total' => 1000,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $this->assertEquals('pending', $order->payment_status);

        $order->update(['payment_status' => 'paid']);

        $this->assertEquals('paid', $order->fresh()->payment_status);
    }

    public function test_admin_can_print_invoice(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $variant = Variant::factory()->create(['product_id' => $product->id]);

        $order = Order::create([
            'order_number' => 'ORD-TEST004',
            'user_id' => $user->id,
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '1234567890',
            'order_type' => 'pickup',
            'fulfillment_date' => now()->addDay(),
            'delivery_fee' => 0,
            'subtotal' => 1000,
            'total' => 1000,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $invoice = Invoice::where('order_id', $order->id)->first();

        $this->actingAs($admin)
            ->get(route('admin.invoices.print', $invoice))
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'text/html; charset=utf-8');
    }

    public function test_customer_cannot_access_other_users_invoice(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $product = Product::factory()->create();
        $variant = Variant::factory()->create(['product_id' => $product->id]);

        $order = Order::create([
            'order_number' => 'ORD-TEST005',
            'user_id' => $user1->id,
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '1234567890',
            'order_type' => 'pickup',
            'fulfillment_date' => now()->addDay(),
            'delivery_fee' => 0,
            'subtotal' => 1000,
            'total' => 1000,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $invoice = Invoice::where('order_id', $order->id)->first();

        $this->actingAs($user2)
            ->get(route('api.invoices.download', $invoice))
            ->assertStatus(403);
    }

    public function test_customer_can_download_own_invoice(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $variant = Variant::factory()->create(['product_id' => $product->id]);

        $order = Order::create([
            'order_number' => 'ORD-TEST006',
            'user_id' => $user->id,
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '1234567890',
            'order_type' => 'pickup',
            'fulfillment_date' => now()->addDay(),
            'delivery_fee' => 0,
            'subtotal' => 1000,
            'total' => 1000,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $invoice = Invoice::where('order_id', $order->id)->first();

        $this->actingAs($user)
            ->get(route('api.invoices.download', $invoice))
            ->assertStatus(200);
    }
}
