<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganisationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\drewlabs_identity_configs('runs_organisation_entities_migrations')) {
            Schema::create('organisations', function (Blueprint $table) {
                $table->increments('id');
                $table->string("name", 145);
                $table->string("phone_number", 20);
                $table->string("address");
                $table->string("postal_code")->nullable();
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
        Schema::dropIfExists('organisations');
    }
}
