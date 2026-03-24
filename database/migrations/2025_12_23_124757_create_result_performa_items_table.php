<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultPerformaItemsTable extends Migration
{
    public function up()
    {
        Schema::create('result_performa_items', function (Blueprint $table) {
            $table->id();

            // 🔑 Relations
            $table->unsignedBigInteger('performa_id');
            $table->unsignedBigInteger('sub_code_id');

            // Term sequence
            $table->enum('term', ['P1', 'HY', 'P2', 'AN']);

            // MARKS or GRADE
            $table->enum('evaluation_type', ['MARKS', 'GRADE']);

            // Component like PT / Notebook / SE / Written
            $table->string('component', 50)->nullable();

            // Max marks (NULL for grade-only subjects)
            $table->decimal('max_marks', 5, 2)->nullable();

            // Ordering
            $table->integer('subject_order')->default(0);
            $table->integer('component_order')->default(0);

            // Include / Exclude subject from result
            $table->boolean('is_included')->default(true);

            $table->timestamps();

            /* ==========================
             * Foreign Keys
             * ========================== */
            $table->foreign('performa_id')
                  ->references('id')
                  ->on('result_performas')
                  ->onDelete('cascade');

            $table->foreign('sub_code_id')
                  ->references('id')
                  ->on('sub_codes')
                  ->onDelete('cascade');

            /* ==========================
             * Prevent duplicate definition
             * ========================== */
            $table->unique(
                ['performa_id', 'sub_code_id', 'term', 'component'],
                'uniq_performa_subject_term_component'
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists('result_performa_items');
    }
}
