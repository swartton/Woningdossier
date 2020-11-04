<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropMotivationExtraFromUserEnergyHabitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_energy_habits', function (Blueprint $table) {
            $table->dropColumn('motivation_extra');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_energy_habits', function (Blueprint $table) {
            $table->longText('motivation_extra')->nullable()->default(null);
        });
    }
}
