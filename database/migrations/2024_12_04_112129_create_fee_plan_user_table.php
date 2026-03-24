<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeePlanUserTable extends Migration
{
    public function up()
    {
        Schema::create('fee_plan_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('fee_plan_id');

            // Add foreign keys for referential integrity
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('fee_plan_id')->references('id')->on('fee_plans')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fee_plan_user');
    }
}
