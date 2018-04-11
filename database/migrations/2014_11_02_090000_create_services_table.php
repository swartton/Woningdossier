<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('name');
            $table->string('short'); // kind of slug identifier
            $table->integer('service_type_id')->unsigned();
            $table->foreign('service_type_id')->references('id')->on('service_types')->onDelete('restrict');

	        $table->integer('order');
	        $table->uuid('info');
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
        Schema::dropIfExists('services');
    }
}
