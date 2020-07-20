<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('user_id');
            $table->string('user_name', 100)->unique();
            $table->string('user_password', 100);
            $table->string('remember_token', 100)->nullable();
            $table->unsignedTinyInteger("lock_enabled")->nullable()->default(0);
            $table->unsignedTinyInteger("login_attempts")->nullable()->default(0);
            $table->dateTimeTz("lock_expired_at")->nullable();
            $table->unsignedTinyInteger('double_auth_active')->nullable()->default(0);
            $table->unsignedTinyInteger('is_active')->default(0); // is_verified
            // Added this column to be compliant with front-end requirement
            $table->unsignedTinyInteger('is_verified')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->softDeletes();
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
        Schema::dropIfExists('users');
    }
}
