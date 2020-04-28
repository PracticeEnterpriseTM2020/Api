<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeterCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meter_customer', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('customer_email')->nullable(false);
            $table->unsignedBigInteger('meter_id');
            $table->bigInteger('installedOn')->nullable();
            $table->tinyInteger('deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meter_customer');
    }
}
