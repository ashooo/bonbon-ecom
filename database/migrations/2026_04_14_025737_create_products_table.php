<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->decimal('sale_price', 8, 2)->nullable();
            $table->string('unit_size', 30)->nullable();
            $table->boolean('is_preorder')->default(false);
            $table->integer('preorder_days')->default(0);
            $table->boolean('allows_customization')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);

            // Compatibility fields used by current front-end/admin pages.
            $table->integer('stock_quantity')->default(0);
            $table->string('main_image')->nullable();
            $table->boolean('is_best_seller')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
