<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Building;
use App\Models\PrivateMessage;


class DropColumnAllowAccessOnPrivateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // first migrate the allow access to the users table.
        $buildings = Building::all();
        foreach ($buildings as $building) {
            $building->user()->update([
                'allow_access' => PrivateMessage::allowedAccess($building)
            ]);
        }

        Schema::table('private_messages', function (Blueprint $table) {
            $table->dropColumn('allow_access');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('private_messages', function (Blueprint $table) {
            $table->boolean('allow_access')->after('to_cooperation_id');
        });
    }
}
