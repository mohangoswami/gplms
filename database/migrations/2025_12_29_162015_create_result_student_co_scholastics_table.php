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
        Schema::create('result_student_co_scholastics', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('term_id');

            // ✅ NEW DESIGN (FK based)
            $table->unsignedBigInteger('co_scholastic_area_id');

            $table->string('grade', 5)->nullable();
            $table->timestamps();

            // ✅ CORRECT UNIQUE CONSTRAINT
            $table->unique(
                ['student_id', 'term_id', 'co_scholastic_area_id'],
                'uniq_student_term_area'
            );

            // ✅ FOREIGN KEYS
            $table->foreign('co_scholastic_area_id')
                ->references('id')
                ->on('result_co_scholastic_areas')
                ->onDelete('cascade');

            // (optional but good)
            // $table->foreign('student_id')->references('id')->on('users');
            // $table->foreign('term_id')->references('id')->on('result_terms');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('result_student_co_scholastics');
    }
};
