<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberships extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->integer('number');
            $table->unsignedInteger('membership_type_id');
            $table->unsignedInteger('membership_network_id');
            $table->integer('parent_id')->nullable()->default(0);
            $table->integer('level')->nullable()->default(0);
            $table->boolean('status')->default(0);
            $table->unsignedInteger('user_id')->nullable();


            $table->timestamps();

            $table->foreign('membership_type_id','memberships_types')->references('id')->on('memberships_types')->onDelete('cascade');
            $table->foreign('membership_network_id','memberships_networks')->references('id')->on('memberships_networks')->onDelete('cascade');
            $table->foreign('user_id','users')->references('id')->on('users')->index();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('memberships');
    }
}
