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
     Schema::create('messages', function (Blueprint $table) {
   $table->id();
    $table->foreignId('chat_id')->constrained('chats')->cascadeOnDelete();
    $table->foreignId('sender_id')->constrained('users');
    $table->foreignId('receiver_id')->constrained('users');
    $table->enum('type', ['text', 'image', 'video', 'voice'])->default('text');
    $table->text('content')->nullable();
    $table->string('file_url')->nullable();
    $table->boolean('is_read')->default(false);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
 public function down(): void
{
    Schema::dropIfExists('messages'); // بدل 'massages'
}
};
