<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'pen')) {
                $table->string('pen', 16)->nullable()->after('aadhar');
            }

            if (!Schema::hasColumn('users', 'apaar')) {
                $table->string('apaar', 16)->nullable()->after('pen');
            }

            if (!Schema::hasColumn('users', 'house')) {
                $table->string('house')->nullable()->after('apaar');
            }
        });

        if (Schema::hasColumn('users', 'aadhar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('aadhar', 16)->nullable()->change();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'aadhar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('aadhar')->nullable()->change();
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'apaar')) {
                $table->dropColumn('apaar');
            }

            if (Schema::hasColumn('users', 'pen')) {
                $table->dropColumn('pen');
            }
        });
    }
};
