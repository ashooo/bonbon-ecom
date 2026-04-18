<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->string('customer_name', 100);
            $table->string('customer_email', 100);
            $table->string('customer_phone', 20);
            $table->enum('order_type', ['pickup', 'delivery'])->default('pickup');
            $table->date('fulfillment_date');
            $table->time('fulfillment_time')->nullable();
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete()->cascadeOnUpdate();
            $table->text('delivery_address')->nullable();
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->decimal('subtotal', 8, 2)->default(0);
            $table->decimal('total', 8, 2);
            $table->text('special_instructions')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'ready', 'completed', 'cancelled'])->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
