<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('acted_by_user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->string('type', 32);
            $table->integer('quantity_change');
            $table->unsignedInteger('previous_stock');
            $table->unsignedInteger('new_stock');
            $table->string('reason', 255)->nullable();
            $table->timestamps();

            $table->index(['variant_id', 'created_at']);
            $table->index(['product_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
