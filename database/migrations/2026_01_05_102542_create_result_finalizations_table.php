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
         Schema::create('result_finalizations', function (Blueprint $table) {
        $table->id();

        $table->unsignedBigInteger('student_id');
        $table->unsignedBigInteger('performa_id');


        $table->enum('status', ['DRAFT', 'FINAL'])->default('DRAFT');

        $table->unsignedBigInteger('finalized_by_id')->nullable();
        $table->enum('finalized_by_role', ['admin', 'teacher'])->nullable();
        $table->timestamp('finalized_at')->nullable();

        $table->timestamps();

        // 🔒 One result per student per term
        $table->unique(['student_id']);

        // ⚡ Performance
        $table->index(['student_id']);
        $table->index('status');

        // 🔗 Relations
        $table->foreign('student_id')
            ->references('id')->on('users')
            ->onDelete('cascade');

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
        Schema::dropIfExists('result_finalizations');
    }
};
