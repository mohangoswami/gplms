<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teachers', function($table)
        {
            $table->string('fName')->after('name');
            $table->string('mName')->after('fName');
            $table->date('dob')->after('mName')->nullable();
            $table->date('doj')->after('dob')->nullable();
            $table->string('address')->after('doj');
            $table->string('mobile')->after('address');
            $table->string('rfid')->after('mobile')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
