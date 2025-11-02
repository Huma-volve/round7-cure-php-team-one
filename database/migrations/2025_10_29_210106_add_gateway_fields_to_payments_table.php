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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_intent_id')->nullable()->after('transaction_id'); // stripe intent or paypal order id
            $table->string('client_secret')->nullable()->after('payment_intent_id');
            $table->json('metadata')->nullable()->after('client_secret');
            $table->string('failure_reason')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['payment_intent_id', 'client_secret', 'metadata', 'failure_reason']);
        });
    }
};
