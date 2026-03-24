<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
Schema::create('result_student_health_records', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('student_id');
    $table->unsignedBigInteger('term_id');
    $table->decimal('height', 5, 2)->nullable();
    $table->decimal('weight', 5, 2)->nullable();
    $table->text('remark')->nullable();
    $table->timestamps();

    $table->unique(['student_id', 'term_id']);
});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('result_student_health_records');
    }
};
