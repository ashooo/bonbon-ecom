<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_status_audits', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('acted_by_user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->string('action', 32);
            $table->boolean('from_is_active')->nullable();
            $table->boolean('to_is_active')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['acted_by_user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_status_audits');
    }
};
