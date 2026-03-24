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
        Schema::create('result_terms', function (Blueprint $table) {
        $table->id();
        $table->foreignId('performa_id')
            ->constrained('result_performas')
            ->cascadeOnDelete();
        $table->string('name');
        $table->integer('order_no')->default(1);
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
        Schema::dropIfExists('result_terms');
    }
};
