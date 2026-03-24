<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');;
            $table->integer('receiptId');
            $table->date('date');
            $table->integer('oldBalance')->nullable();
            $table->string('feeHead')->nullable();
            $table->integer('january')->nullable();
            $table->integer('february')->nullable();
            $table->integer('march')->nullable();
            $table->integer('april')->nullable();
            $table->integer('may')->nullable();
            $table->integer('june')->nullable();
            $table->integer('july')->nullable();
            $table->integer('august')->nullable();
            $table->integer('september')->nullable();
            $table->integer('october')->nullable();
            $table->integer('november')->nullable();
            $table->integer('december')->nullable();
            $table->integer('total');
            $table->integer('lateFee')->nullable();
            $table->integer('concession')->nullable();
            $table->integer('concessionP')->nullable();
            $table->integer('netFee');
            $table->integer('receivedAmt');
            $table->integer('balance')->nullable();
            $table->string('paymentMode')->nullable();
            $table->string('bankName')->nullable();
            $table->string('chequeNo')->nullable();
            $table->string('chequeDate')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receipts');
    }
}
