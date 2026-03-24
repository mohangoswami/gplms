<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('srNo');
            $table->string('name');
            $table->string('fName')->nullable();
            $table->string('mName')->nullable();
            $table->date('dob')->nullable();
            $table->string('address')->nullable();
            $table->string('mobile');
            $table->string('rfid')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('grade');
            $table->integer('app_permission')->default(1)->nullable();
            $table->integer('exam_permission')->default(1)->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('route_id')->nullable(); // Add foreign key for routes
            $table->foreign('route_id')->references('id')->on('route_names')->onDelete('cascade');

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
