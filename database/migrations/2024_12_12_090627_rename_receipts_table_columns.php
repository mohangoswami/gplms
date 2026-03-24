<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameReceiptsTableColumns extends Migration
{
    public function up()
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->renameColumn('january', 'jan');
            $table->renameColumn('february', 'feb');
            $table->renameColumn('march', 'mar');
            $table->renameColumn('april', 'apr');
            $table->renameColumn('may', 'may'); // remains the same
            $table->renameColumn('june', 'jun');
            $table->renameColumn('july', 'jul');
            $table->renameColumn('august', 'aug');
            $table->renameColumn('september', 'sep');
            $table->renameColumn('october', 'oct');
            $table->renameColumn('november', 'nov');
            $table->renameColumn('december', 'dec');
        });
    }

    public function down()
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->renameColumn('jan', 'january');
            $table->renameColumn('feb', 'february');
            $table->renameColumn('mar', 'march');
            $table->renameColumn('apr', 'april');
            $table->renameColumn('may', 'may'); // remains the same
            $table->renameColumn('jun', 'june');
            $table->renameColumn('jul', 'july');
            $table->renameColumn('aug', 'august');
            $table->renameColumn('sep', 'september');
            $table->renameColumn('oct', 'october');
            $table->renameColumn('nov', 'november');
            $table->renameColumn('dec', 'december');
        });
    }
}
