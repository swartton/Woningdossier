<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInputSourceIdToToolSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tool_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('tool_settings', 'input_source_id')) {
                $table->integer('input_source_id')->unsigned()->nullable()->default(1)->after('changed_input_source_id');
                $table->foreign('input_source_id')->references('id')->on('input_sources')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tool_settings', function (Blueprint $table) {
            $table->dropForeign(['input_source_id']);
            $table->dropColumn('input_source_id');
        });
    }
}
