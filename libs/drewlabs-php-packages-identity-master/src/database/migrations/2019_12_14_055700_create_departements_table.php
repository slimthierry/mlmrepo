<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\drewlabs_identity_configs('runs_organisation_entities_migrations')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->smallIncrements('id');
                $table->string('name', 145);
                $table->text('description')->nullable();
                $table->unsignedInteger('organisation_id')->nullable();
                $table->foreign('organisation_id')->references('id')->on('organisations')->onDelete('cascade');
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
        Schema::dropIfExists('departments');
    }
}
