<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->string('channel_key', 64)->nullable()->after('guest_email');
        });

        DB::table('chat_conversations')->whereNull('channel_key')->orderBy('id')->eachById(function (object $conversation) {
            DB::table('chat_conversations')
                ->where('id', $conversation->id)
                ->update(['channel_key' => Str::random(32)]);
        });

        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->unique('channel_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropUnique(['channel_key']);
            $table->dropColumn('channel_key');
        });
    }
};
