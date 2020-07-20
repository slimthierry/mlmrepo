<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ApplySoftDeleteOnIdentityModel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_infos', function (Blueprint $table) {
            //
            $table->softDeletes();
        });
        Schema::table('roles', function (Blueprint $table) {
            //
            $table->softDeletes();
        });
        Schema::table('permissions', function (Blueprint $table) {
            //
            $table->softDeletes();
        });
        Schema::table('role_users', function (Blueprint $table) {
            //
            $table->softDeletes();
        });
        Schema::table('permission_roles', function (Blueprint $table) {
            //
            $table->softDeletes();
        });
        if (\drewlabs_identity_configs('runs_organisation_entities_migrations')) {
            Schema::table('department_users', function (Blueprint $table) {
                //
                $table->softDeletes();
            });
            Schema::table('department_roles', function (Blueprint $table) {
                //
                $table->softDeletes();
            });
            Schema::table('organisations', function (Blueprint $table) {
                //
                $table->softDeletes();
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
        // Schema::table('user_infos', function (Blueprint $table) {
        //     //
        // });
    }
}
