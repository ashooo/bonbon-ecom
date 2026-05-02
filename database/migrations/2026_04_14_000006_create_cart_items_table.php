<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 8, 2);
            $table->string('special_instructions', 255)->nullable();
            $table->timestamps();

            $table->index('product_id');
            $table->index('variant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
