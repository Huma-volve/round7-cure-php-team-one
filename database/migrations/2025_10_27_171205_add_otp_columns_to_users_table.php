<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        
        $table->string('email_otp')->nullable();
        $table->timestamp('email_otp_expires_at')->nullable();
        $table->string('phone_otp')->nullable();
        $table->timestamp('phone_otp_expires_at')->nullable();
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['email_otp', 'email_otp_expires_at', 'phone_otp', 'phone_otp_expires_at']);
    });
}

};
