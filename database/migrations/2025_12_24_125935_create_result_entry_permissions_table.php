<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('result_entry_permissions', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('teacher_id');
            $table->string('class'); // e.g. 1ST, 2ND, 10A
            $table->unsignedBigInteger('component_id');
            // e.g. Theory, Practical, Viva, Co-Scholastic

            $table->timestamps();

            // ✅ UNIQUE: same permission dobara na aa sake
            $table->unique(
                ['teacher_id', 'class', 'component_id'],
                'uniq_teacher_class_component'
            );

            // ✅ Foreign Key (Teacher)
            $table->foreign('teacher_id')
                ->references('id')
                ->on('teachers')
                ->onDelete('cascade');

            // (optional but recommended)
            // $table->foreign('component_id')
            //     ->references('id')
            //     ->on('result_components')
            //     ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('result_entry_permissions');
    }
};
