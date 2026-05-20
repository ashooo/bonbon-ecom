<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table): void {
            $table->decimal('delivery_fee', 8, 2)->default(5.99)->after('customization_pricing');
            $table->decimal('tax_rate', 5, 2)->default(10.00)->after('delivery_fee');
            $table->decimal('service_fee', 8, 2)->default(0)->after('tax_rate');
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table): void {
            $table->dropColumn(['delivery_fee', 'tax_rate', 'service_fee']);
        });
    }
};

