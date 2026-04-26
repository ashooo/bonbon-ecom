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
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_session_id', 120)->nullable()->index();
            $table->string('guest_name', 120)->nullable();
            $table->string('guest_email')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamp('last_message_at')->nullable()->index();
            $table->timestamp('customer_last_read_at')->nullable();
            $table->timestamp('admin_last_read_at')->nullable();
            $table->timestamp('customer_typing_at')->nullable();
            $table->timestamp('admin_typing_at')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chat_conversations')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('sender_type', ['customer', 'admin', 'system']);
            $table->text('body')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->string('attachment_mime', 120)->nullable();
            $table->unsignedBigInteger('attachment_size')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('seen_at')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_auto_replies', function (Blueprint $table) {
            $table->id();
            $table->enum('trigger_type', ['offline', 'keyword']);
            $table->string('keyword', 120)->nullable();
            $table->text('reply_text');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('chat_presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->nullable()->constrained('chat_conversations')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 120)->nullable()->index();
            $table->enum('role', ['customer', 'admin']);
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->timestamps();

            $table->index(['conversation_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_presences');
        Schema::dropIfExists('chat_auto_replies');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_conversations');
    }
};
