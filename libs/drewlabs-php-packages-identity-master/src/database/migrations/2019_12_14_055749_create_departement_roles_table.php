<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartementRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\drewlabs_identity_configs('runs_organisation_entities_migrations')) {
            Schema::create('department_roles', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedSmallInteger('department_id');
                $table->unsignedBigInteger('role_id');
                $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('department_roles');
    }
}
