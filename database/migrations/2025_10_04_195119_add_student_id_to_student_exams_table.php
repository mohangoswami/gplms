<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStudentIdToStudentExamsTable extends Migration
{
    public function up()
    {
        Schema::table('student_exams', function (Blueprint $table) {
            $table->unsignedBigInteger('studentId')->nullable()->after('titleId'); // allow null if needed
            $table->unique(['titleId', 'studentId'], 'studentexams_title_student_unique'); // make pair unique
        });
    }

    public function down()
    {
        Schema::table('student_exams', function (Blueprint $table) {
            $table->dropUnique('studentexams_title_student_unique');
            $table->dropColumn('studentId');
        });
    }
}
