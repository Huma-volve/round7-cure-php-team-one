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
           Schema::create('chat_user_meta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained('chats')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // per-user flags
            $table->boolean('favorite')->default(false);
            $table->boolean('archived')->default(false);
            $table->boolean('muted')->default(false);

            // حذف جانبي (soft-delete per user) — يتيح للمستخدم أن "يمسح" الشات من واجهته
            $table->timestamp('deleted_at')->nullable();

            // آخر رسالة قرأها هذا المستخدم (نستخدمه لحساب unread count بسرعة إذا أردت لاحقاً)
            $table->unsignedBigInteger('last_read_message_id')->nullable();

            $table->timestamps();

            $table->unique(['chat_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_user_meta');
    }
};
