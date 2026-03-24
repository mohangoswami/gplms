<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFeeHeadColumnFromFeePlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fee_plans', function (Blueprint $table) {
            $table->dropColumn('feeHead');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fee_plans', function (Blueprint $table) {
            $table->string('feeHead')->nullable();
        });
    }
}
