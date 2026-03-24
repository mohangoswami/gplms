<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentResultItemsTable extends Migration
{
    public function up()
    {
        Schema::create('student_result_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Student
            $table->unsignedBigInteger('student_id');

            // Blueprint item (VERY IMPORTANT)
            $table->unsignedBigInteger('performa_item_id');

            // MARKS or GRADE (one of them will be used)
            $table->decimal('marks', 6, 2)->nullable();
            $table->string('grade', 5)->nullable();

            $table->timestamps();

            /* ==========================
             * Constraints
             * ========================== */

            // One mark per student per performa item
            $table->unique(['student_id', 'performa_item_id']);

            // Optional foreign keys (recommended)
            // Comment out if legacy DB causes issues

            $table->foreign('student_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('performa_item_id')
                ->references('id')->on('result_performa_items')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_result_items');
    }
}
