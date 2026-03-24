<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // add admin_id if it doesn't exist
            if (!Schema::hasColumn('exams', 'admin_id')) {
                $table->unsignedBigInteger('admin_id')->nullable()->after('term_id');
                $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'admin_id')) {
                $table->dropForeign(['admin_id']);
                $table->dropColumn('admin_id');
            }
        });
    }
};
