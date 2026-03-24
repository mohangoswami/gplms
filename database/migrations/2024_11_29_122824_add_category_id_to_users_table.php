<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('grade'); // Add foreign key column
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade'); // Add foreign key constraint
        });

        // Optionally remove the old `category` column
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'category')) {
                $table->dropColumn('category');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('category')->nullable()->after('grade'); // Restore old column if needed
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
}
