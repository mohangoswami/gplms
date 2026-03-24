<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultPerformasTable extends Migration
{
    public function up()
    {
        Schema::create('result_performas', function (Blueprint $table) {
            $table->id();

            // Class for which this result performa applies
            $table->string('class', 20);   // Nursery, UKG, 1ST, 5TH, 6TH

            // Human readable name
            $table->string('name', 100);   // "Default CBSE Performa"

            // One default performa per class
            $table->boolean('is_default')->default(true);

            $table->timestamps();

            // Safety: only one default performa per class
            $table->unique(['class', 'is_default'], 'uniq_class_default_performa');
        });
    }

    public function down()
    {
        Schema::dropIfExists('result_performas');
    }
}
