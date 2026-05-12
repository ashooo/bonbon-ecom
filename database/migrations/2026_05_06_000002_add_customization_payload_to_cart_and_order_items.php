<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->json('customization_payload')->nullable()->after('special_instructions');
            $table->text('special_instructions')->nullable()->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->json('customization_payload')->nullable()->after('special_instructions');
            $table->text('special_instructions')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('customization_payload');
            $table->string('special_instructions', 255)->nullable()->change();
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('customization_payload');
            $table->string('special_instructions', 255)->nullable()->change();
        });
    }
};
