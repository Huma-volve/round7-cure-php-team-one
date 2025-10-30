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
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();

            // نوع الرسالة: text, image, video, file, audio
            $table->enum('type', ['text','image','video','file','audio'])->default('text');

            // نص الرسالة إن وجد
            $table->text('body')->nullable();

            // attachment info (لو في ملف)
            $table->string('attachment_path')->nullable(); // path in storage (disk 'public')
            $table->string('attachment_mime')->nullable();
            $table->unsignedBigInteger('attachment_size')->nullable(); // bytes

            // حالة الرسالة العامة (نستخدم message_reads لقراءة per-user)
            $table->enum('status', ['sent','delivered','read'])->default('sent');

            // مفيد للترتيب والبحث
            $table->timestamps();

            // index لتسريع استرجاع أحدث الرسائل للشات
            $table->index(['chat_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
