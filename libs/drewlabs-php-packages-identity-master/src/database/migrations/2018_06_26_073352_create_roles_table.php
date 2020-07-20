<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
  * Run the migrations.
  *
  * @return void
  */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('label', 190)->unique();
            $table->string('display_label')->nullable();
            $table->boolean('is_admin_user_role')->default(0);
            $table->text('description')->nullable();
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
        Schema::dropIfExists('roles');
    }
}
