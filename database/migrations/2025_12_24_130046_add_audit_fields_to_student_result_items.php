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
Schema::table('student_result_items', function (Blueprint $table) {

    $table->unsignedBigInteger('entered_by')->nullable();
    $table->unsignedBigInteger('updated_by')->nullable();

    $table->timestamp('entered_at')->nullable();

    $table->foreign('entered_by')
        ->references('id')->on('teachers');

    $table->foreign('updated_by')
        ->references('id')->on('teachers');
});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_result_items', function (Blueprint $table) {
            //
        });
    }
};
