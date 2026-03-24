<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateConcessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('concessions', function (Blueprint $table) {
            // Drop the existing concession_transport column
            $table->dropColumn('concession_transport');

            // Add new fields
            $table->unsignedBigInteger('fee_plan_id')->nullable()->after('student_id');
            $table->string('fee_type')->nullable()->after('fee_plan_id');

            // Add a foreign key for fee_plan_id (if necessary)
            $table->foreign('fee_plan_id')->references('id')->on('fee_plans')->onDelete('cascade');
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
            // Rollback changes
            $table->dropForeign(['fee_plan_id']);
            $table->dropColumn('fee_plan_id');
            $table->dropColumn('fee_type');

            // Re-add the dropped field
            $table->decimal('concession_transport', 10, 2)->nullable();
        });
    }
}
