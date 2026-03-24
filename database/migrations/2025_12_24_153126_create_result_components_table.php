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
        Schema::create('result_components', function (Blueprint $table) {
        $table->id();
        $table->foreignId('term_id')
            ->constrained('result_terms')
            ->cascadeOnDelete();
        $table->string('name');
        $table->enum('evaluation_type', ['marks', 'grade']);
        $table->integer('max_marks')->nullable();
        $table->integer('order_no')->default(1);
        $table->boolean('is_included')->default(true);
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
        Schema::dropIfExists('result_components');
    }
};
