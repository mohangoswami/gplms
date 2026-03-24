<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('result_performas', function (Blueprint $table) {
            $table->string('academic_year')->after('class');
        });
    }

    public function down()
    {
        Schema::table('result_performas', function (Blueprint $table) {
            $table->dropColumn('academic_year');
        });
    }
};
