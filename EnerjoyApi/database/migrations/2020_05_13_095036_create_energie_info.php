<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnergieInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('energie_infos', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();;
            $table->string('type');
            $table->string('eenheid');
            $table->string('opwekkings_manier');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('energie_infos');
    }
}
