<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceTokensTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('device_tokens')) {
            return;
        }

        Schema::create('device_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('token', 1024);
            $table->string('platform', 20)->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->unique(['user_id', 'token']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('device_tokens');
    }
}
