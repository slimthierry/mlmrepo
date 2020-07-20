<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipsPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memberships_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('membership_id');
            $table->unsignedInteger('payment_method_id');
            $table->string('account');
            $table->dateTime('date');
            $table->timestamps();

            $table->foreign('membership_id','memberships')->references('id')->on('memberships')->onDelete('cascade');
            $table->foreign('payment_method_id','payments_methods')->references('id')->on('payments_methods')->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('memberships_payments');
    }
}
