<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserConnexionsTable extends Migration
{
    /**
  * Run the migrations.
  *
  * @return void
  */
    public function up()
    {
        Schema::create('user_connexions', function (Blueprint $table) {
            $table->bigIncrements('user_connexion_id');
            $table->string('identifier');
            $table->unsignedTinyInteger('user_connexion_status')->default(0);
            $table->string('user_connexion_ip_address', 50)->default(0);
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
        Schema::dropIfExists('user_connexions');
    }
}
