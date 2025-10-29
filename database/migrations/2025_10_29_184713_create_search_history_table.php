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
        Schema::create('search_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('search_query', 255);
            $table->enum('search_type', ['specialty', 'doctor_name', 'location', 'general'])->default('general');
            $table->foreignId('specialty_id')->nullable()->constrained('specialties')->nullOnDelete();
            $table->string('location_name', 255)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_saved')->default(false);
            $table->timestamp('searched_at')->useCurrent();
            $table->timestamps();

            // Indexes for better performance
            $table->index('user_id');
            $table->index(['user_id', 'searched_at']);
            $table->index(['user_id', 'is_saved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_history');
    }
};
