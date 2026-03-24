<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReasonToConcessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('concessions', function (Blueprint $table) {
            $table->string('reason')->nullable()->after('concession_fee'); // Add the new column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('concessions', function (Blueprint $table) {
            $table->dropColumn('reason'); // Rollback the change
        });
    }
}
