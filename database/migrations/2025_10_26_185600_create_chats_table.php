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
         {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            // 1:1 chat between two users. استخدم sender_id و receiver_id (أنت استخدمت هذا سابقاً)
            $table->foreignId('user_one_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_two_id')->constrained('users')->cascadeOnDelete();

            // آخر رسالة مختصرة (index لسرعة ترتيب القوائم)
            $table->text('last_message')->nullable();
            $table->unsignedBigInteger('last_message_id')->nullable();
            $table->timestamp('last_message_at')->nullable();

            $table->timestamps();

            // ضمان عدم تكرار نفس الزوجين في اتجاهين مختلفين
            $table->unique(['user_one_id', 'user_two_id']);
        });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
