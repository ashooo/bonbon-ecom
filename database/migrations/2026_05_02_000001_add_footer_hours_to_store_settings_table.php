<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table): void {
            $table->text('footer_hours')->nullable()->after('footer_address');
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table): void {
            $table->dropColumn('footer_hours');
        });
    }
};
