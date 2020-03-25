<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Invoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned()->nullable(false);
            $table->unsignedBigInteger('customerId')->nullable(false);
            $table->double('price')->nullable(false);
            $table->integer('date')->nullable(false);
            $table->smallInteger('paid')->unsigned()->default(0);
            $table->smallInteger('active')->unsigned()->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
