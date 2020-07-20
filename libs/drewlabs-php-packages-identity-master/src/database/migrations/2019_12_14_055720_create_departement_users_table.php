<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartementUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\drewlabs_identity_configs('runs_organisation_entities_migrations')) {
            Schema::create('department_users', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedSmallInteger('department_id');
                $table->unsignedBigInteger('user_id')->index();
                $table->boolean('is_manager')->default(0);
                $table->foreign('department_id')->references('id')->on('departments');
                $table->foreign('user_id', 'department_user_id_fk')->references('id')->on('user_infos');
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
        Schema::dropIfExists('department_users');
    }
}
