<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->unsignedBigInteger('student_id');
            $table->string('class');
            $table->date('date');
            $table->enum('status', ['P', 'A', 'L']);
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('set null');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['student_id', 'class', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
