<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddLeaveToAttendances extends Migration
{
    public function up()
    {
        // Alter the enum column to include 'L' for Leave. Use raw statement because
        // Laravel's schema builder has limited enum alteration support across DB engines.
        DB::statement("ALTER TABLE `attendances` MODIFY `status` ENUM('P','A','L') NOT NULL DEFAULT 'P'");
    }

    public function down()
    {
        // Revert back to original enum values 'P' and 'A'. If any 'L' rows exist,
        // they will be converted to 'P' to avoid data loss.
        DB::statement("UPDATE `attendances` SET `status` = 'P' WHERE `status` = 'L'");
        DB::statement("ALTER TABLE `attendances` MODIFY `status` ENUM('P','A') NOT NULL DEFAULT 'P'");
    }
}
