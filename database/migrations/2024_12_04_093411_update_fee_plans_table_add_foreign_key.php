<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFeePlansTableAddForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fee_plans', function (Blueprint $table) {
            // Add a temporary column to store old feeHead values (optional for backup)
            $table->string('feeHead_backup')->nullable();

            // Update existing feeHead column to match the fee_heads table ID
            $table->unsignedBigInteger('feeHead_id')->nullable();

            // Add the foreign key relationship
            $table->foreign('feeHead_id')->references('id')->on('fee_heads')->onDelete('cascade');
        });

        // If required, transfer data from the old feeHead column
        DB::statement("
            UPDATE fee_plans
            SET feeHead_id = (SELECT id FROM fee_heads WHERE fee_heads.name = fee_plans.feeHead)
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fee_plans', function (Blueprint $table) {
            // Drop the foreign key and new column
            $table->dropForeign(['feeHead_id']);
            $table->dropColumn('feeHead_id');

            // Restore old feeHead column if necessary
            $table->dropColumn('feeHead_backup');
        });
    }
}
