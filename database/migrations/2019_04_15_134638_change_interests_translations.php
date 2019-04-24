<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeInterestsTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $moreInformation = DB::table('interests')->where('calculate_value', 3)->first();
        DB::table('translations')->where('key', $moreInformation->name)->update(['translation' => 'Misschien, meer informatie gewenst']);

        $noAction = DB::table('interests')->where('calculate_value', 4)->first();
        DB::table('translations')->where('key', $noAction->name)->update(['translation' => 'Nee, geen interesse']);

        $notPossible = DB::table('interests')->where('calculate_value', 5)->first();
        DB::table('translations')->where('key', $notPossible->name)->update(['translation' => 'Nee, niet mogelijk / reeds uitgevoerd']);
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        $moreInformation = DB::table('interests')->where('calculate_value', 3)->first();
        DB::table('translations')->where('key', $moreInformation->name)->update(['translation' => 'Meer informatie gewenst']);

        $noAction = DB::table('interests')->where('calculate_value', 4)->first();
        DB::table('translations')->where('key', $noAction->name)->update(['translation' => 'Geen actie']);

        $notPossible = DB::table('interests')->where('calculate_value', 5)->first();
        DB::table('translations')->where('key', $notPossible->name)->update(['translation' => 'Niet mogelijk']);
    }

}
