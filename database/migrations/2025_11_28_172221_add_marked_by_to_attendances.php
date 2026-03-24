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
    Schema::table('attendances', function (Blueprint $table) {
        $table->unsignedBigInteger('marked_by')->nullable()->after('teacher_id');
        $table->foreign('marked_by')->references('id')->on('users')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('attendances', function (Blueprint $table) {
        $table->dropForeign(['marked_by']);
        $table->dropColumn('marked_by');
    });
}

};
