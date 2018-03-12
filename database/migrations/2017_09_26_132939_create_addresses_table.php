<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users') ->onDelete('restrict');

            $table->string('type_id', 16)->nullable()->default(null);

            $table->string('street')->default('');
            $table->string('number')->default('');
            $table->string('city')->default('');
            $table->string('postal_code')->default('');

            $table->boolean('owner')->unsigned()->nullable();

            $table->boolean('primary')->default(false);

            $table->string('bag_addressid')->default('');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
