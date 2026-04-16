<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('brand_name')->default('BonBon PH');
            $table->string('hero_image')->nullable();
            $table->text('store_description')->nullable();
            $table->string('footer_email')->nullable();
            $table->string('footer_phone')->nullable();
            $table->string('footer_address')->nullable();
            $table->string('copyright_text')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
