<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['variant_id']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->unsignedBigInteger('variant_id')->nullable()->change();
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('variant_id')
                ->references('id')
                ->on('product_variants')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['variant_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('variant_id')->nullable()->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('variant_id')
                ->references('id')
                ->on('product_variants')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['variant_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('variant_id')->nullable(false)->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('variant_id')
                ->references('id')
                ->on('product_variants')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['variant_id']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
            $table->unsignedBigInteger('variant_id')->nullable()->change();
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('variant_id')
                ->references('id')
                ->on('product_variants')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }
};

