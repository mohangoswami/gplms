<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_attendances', function (Blueprint $table) {
            $table->id();
            $table->string('srNo')->nullable();
            $table->string('rfid');
            $table->string('name');
            $table->string('fName');
            $table->string('class')->nullable();
            $table->string('email');
            $table->string('mobile');
            $table->datetime('att0')->nullable();
            $table->datetime('att1')->nullable();
            $table->datetime('att2')->nullable();
            $table->datetime('att3')->nullable();
            $table->datetime('att4')->nullable();
            $table->datetime('att5')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teacher_attendances');
    }
}
