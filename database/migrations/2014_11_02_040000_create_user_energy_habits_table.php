<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserEnergyHabitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_energy_habits', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned()->nullable()->default(null);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');

            $table->integer('resident_count')->nullable()->default(null);
            $table->decimal('thermostat_high')->nullable()->default(null);
            $table->decimal('thermostat_low')->nullable()->default(null);
            $table->integer('hours_high')->nullable()->default(null);
            $table->integer('heating_first_floor')->unsigned()->nullable()->default(null);
            $table->foreign('heating_first_floor')->references('id')->on('building_heatings')->onDelete('restrict');
            $table->integer('heating_second_floor')->unsigned()->nullable()->default(null);
            $table->foreign('heating_second_floor')->references('id')->on('building_heatings')->onDelete('restrict');
            $table->integer('heated_space_outside')->nullable()->default(null);
            $table->boolean('cook_gas')->default(false);
            $table->integer('water_comfort_id')->nullable()->default(null);
            $table->integer('amount_electricity')->nullable()->default(null);
            $table->integer('amount_gas')->nullable()->default(null);
            $table->integer('amount_water')->nullable()->default(null);
            $table->longText('living_situation_extra')->nullable()->default(null);
            $table->longText('motivation_extra')->nullable()->default(null);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
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
        Schema::dropIfExists('user_energy_habits');
    }
}
