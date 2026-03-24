<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Change maxMarks from integer to decimal(8,2)
        // Use raw SQL to avoid requiring doctrine/dbal in environments where it's missing
        DB::statement("ALTER TABLE `exams` MODIFY COLUMN `maxMarks` DECIMAL(8,2) NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to integer
        DB::statement("ALTER TABLE `exams` MODIFY COLUMN `maxMarks` INT NULL;");
    }
};
