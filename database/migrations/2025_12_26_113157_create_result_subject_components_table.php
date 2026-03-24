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
        Schema::create('result_subject_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performa_item_id')
                ->constrained('result_performa_items')
                ->cascadeOnDelete();

            $table->foreignId('component_id')
                ->constrained('result_components')
                ->cascadeOnDelete();

            $table->decimal('max_marks_override', 5, 2)->nullable();
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
        Schema::dropIfExists('result_subject_components');
    }
};
