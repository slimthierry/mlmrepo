<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsOperations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('account_id');
            $table->enum('operation_type', ['C', 'D']);
            $table->decimal('balance_before', 15,2)->default(0);
            $table->decimal('balance_after', 15,2)->default(0);
            $table->decimal('amount', 15,2)->default(0);
            $table->decimal('label', 15,2)->default(0);
            $table->string('reference')->default(0);

            $table->timestamps();

            $table->foreign('account_id','accounts')->references('id')->on('accounts')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts_operations');
    }
}
