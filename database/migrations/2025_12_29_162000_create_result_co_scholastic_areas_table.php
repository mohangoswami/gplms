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
        Schema::create('result_co_scholastic_areas', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('performa_id');
        $table->string('class', 20);
        $table->string('area_name');
        $table->integer('display_order')->default(0);
        $table->boolean('is_active')->default(1);
        $table->timestamps();

        $table->foreign('performa_id')
            ->references('id')
            ->on('result_performas')
            ->onDelete('cascade');
    });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('result_co_scholastic_areas');
    }
};
