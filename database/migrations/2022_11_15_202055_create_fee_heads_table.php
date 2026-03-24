<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeeHeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fee_heads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('accountName');
            $table->string('frequency');
            $table->boolean('jan')->default(0);
            $table->boolean('feb')->default(0);
            $table->boolean('mar')->default(0);
            $table->boolean('apr')->default(0);
            $table->boolean('may')->default(0);
            $table->boolean('jun')->default(0);
            $table->boolean('jul')->default(0);
            $table->boolean('aug')->default(0);
            $table->boolean('sep')->default(0);
            $table->boolean('oct')->default(0);
            $table->boolean('nov')->default(0);
            $table->boolean('dec')->default(0);
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
        Schema::dropIfExists('fee_heads');
    }
}
