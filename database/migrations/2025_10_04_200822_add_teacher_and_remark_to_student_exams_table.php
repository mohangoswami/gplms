<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeacherAndRemarkToStudentExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_exams', function (Blueprint $table) {
            if (!Schema::hasColumn('student_exams', 'teacherId')) {
                $table->unsignedBigInteger('teacherId')->nullable()->after('marksObtain');
            }
            if (!Schema::hasColumn('student_exams', 'remark')) {
                $table->string('remark')->nullable()->after('teacherId');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_exams', function (Blueprint $table) {
            if (Schema::hasColumn('student_exams', 'teacherId')) {
                $table->dropColumn('teacherId');
            }
            if (Schema::hasColumn('student_exams', 'remark')) {
                $table->dropColumn('remark');
            }
        });
    }
}
