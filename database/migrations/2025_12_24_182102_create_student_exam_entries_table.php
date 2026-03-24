<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentExamEntriesTable extends Migration
{
    public function up()
    {
        Schema::create('student_exam_entries', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('student_id');

            $table->unsignedBigInteger('result_performa_item_id');
            $table->unsignedBigInteger('component_id');
            $table->unsignedBigInteger('term_id');

            $table->decimal('marks', 6, 2)->nullable();
            $table->string('grade', 5)->nullable();

            $table->unsignedBigInteger('entered_by_id')->nullable();
            $table->enum('entered_by_role', ['admin','teacher'])->nullable();

            $table->timestamps();

            // ✅ CORRECT UNIQUE RULE
            $table->unique(
                ['student_id', 'result_performa_item_id', 'component_id', 'term_id'],
                'uniq_student_item_component_term'
            );

            // ✅ FOREIGN KEYS
            $table->foreign('student_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('result_performa_item_id')
                  ->references('id')->on('result_performa_items')
                  ->onDelete('cascade');

            $table->foreign('component_id')
                  ->references('id')->on('result_components')
                  ->onDelete('cascade');

            $table->foreign('term_id')
                  ->references('id')->on('result_terms')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_exam_entries');
    }
}
