<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('contact_name')->nullable()->after('user_id');
            $table->string('contact_email')->nullable()->after('contact_name');
            $table->string('contact_phone')->nullable()->after('contact_email');
            $table->string('source')->default('contact_form')->after('contact_phone');
            $table->timestamp('last_reply_at')->nullable()->after('status');
            $table->timestamp('closed_at')->nullable()->after('last_reply_at');
        });

        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->foreignId('sender_id')
                ->nullable()
                ->after('sender_type')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'contact_name',
                'contact_email',
                'contact_phone',
                'source',
                'last_reply_at',
                'closed_at',
            ]);
        });

        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sender_id');
        });
    }
};

