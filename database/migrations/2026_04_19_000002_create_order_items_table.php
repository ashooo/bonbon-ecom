<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('variant_id')->constrained('product_variants')->restrictOnDelete()->cascadeOnUpdate();
            $table->integer('quantity');
            $table->decimal('unit_price', 8, 2);
            $table->decimal('subtotal', 8, 2);
            $table->string('special_instructions', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
