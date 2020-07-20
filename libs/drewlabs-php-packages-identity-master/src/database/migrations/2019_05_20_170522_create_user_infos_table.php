<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_infos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('firstname', 50)->nullable();
            $table->string('lastname', 50)->nullable();
            $table->string('address')->nullable();
            $table->string('email', 100)->nullable()->unique('email', 'u_email');
            $table->string('other_email', 100)->nullable()->unique('other_email', 'u_o_email');
            $table->string('phone_number', 20)->nullable()->unique('phone_number', 'u_phone');
            $table->string('postal_code')->nullable();
            $table->string('birthdate', 50)->nullable();
            $table->enum('sex', ['F', 'M'])->nullable();
            $table->unsignedBigInteger('user_id')->unsigned();
            $table->unsignedSmallInteger('department_id')->nullable()->index();
            $table->unsignedSmallInteger('agence_id')->nullable()->index();
            $table->text('profile_url')->nullable();
            $table->string('organisation_name', 145)->nullable()->index();

            // Foreign keys definitions
            $table->foreign('user_id')->references('user_id')->on('users');
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
        Schema::table('user_infos', function (Blueprint $table) {
            //
        });
    }
}
