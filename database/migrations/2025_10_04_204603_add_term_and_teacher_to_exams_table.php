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
        Schema::table('exams', function (Blueprint $table) {
            $table->unsignedBigInteger('term_id')->nullable()->after('id');
            $table->unsignedBigInteger('teacher_id')->nullable()->after('term_id');

            $table->foreign('term_id')->references('id')->on('terms')->onDelete('set null');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['term_id']);
            $table->dropForeign(['teacher_id']);
            $table->dropColumn(['term_id', 'teacher_id']);
        });
    }
};
