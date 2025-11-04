<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispute_notes', function (Blueprint $table) {
            $table->id();
            $table->string('dispute_type'); // 'payment' or 'booking'
            $table->unsignedBigInteger('dispute_id');
            $table->unsignedBigInteger('user_id');
            $table->text('note');
            $table->timestamps();
            
            $table->index(['dispute_type', 'dispute_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispute_notes');
    }
};
