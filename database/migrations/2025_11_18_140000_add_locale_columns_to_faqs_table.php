<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->string('question_en')->nullable()->after('id');
            $table->text('answer_en')->nullable()->after('question_en');
            $table->string('question_ar')->nullable()->after('answer_en');
            $table->text('answer_ar')->nullable()->after('question_ar');
            $table->string('locale', 5)->default('ar')->after('display_order');
        });

        DB::table('faqs')->update([
            'question_en' => DB::raw('question'),
            'answer_en' => DB::raw('answer'),
            'question_ar' => DB::raw('question'),
            'answer_ar' => DB::raw('answer'),
            'locale' => 'ar',
        ]);
    }

    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropColumn(['question_en', 'answer_en', 'question_ar', 'answer_ar', 'locale']);
        });
    }
};

