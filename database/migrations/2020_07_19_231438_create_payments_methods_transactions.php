<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsMethodsTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments_methods_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('account');
            $table->dateTime('date');
            $table->unsignedInteger('payment_method_id');

            $table->timestamps();

            $table->foreign('payment_method_id','payments_methods_id')->references('id')->on('payments_methods')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments_methods_transactions');
    }
}
