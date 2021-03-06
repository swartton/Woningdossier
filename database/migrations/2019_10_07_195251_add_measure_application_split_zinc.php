<?php

use App\Helpers\Str;
use Illuminate\Database\Migrations\Migration;

class AddMeasureApplicationSplitZinc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // There will be no migration / changes when there's no step
        $step = DB::table('steps')->where('slug', '=',
            'roof-insulation')->first();

        if (! is_null($step)) {
            // cost unit creation
            $costUnit = DB::table('translations')
                          ->where('language', '=', 'nl')
                          ->where('translation', '=', 'per m')
                          ->first();

            if (! $costUnit) {
                $cuUuid = Str::uuid();
                \DB::table('translations')->insert([
                    'key'         => $cuUuid,
                    'language'    => 'nl',
                    'translation' => 'per m',
                ]);
            } else {
                $cuUuid = $costUnit->key;
            }

            // Pitched
            // Measure name
            DB::table('translations')
              ->where('language', '=', 'nl')
              ->where('translation', '=', 'Zinkwerk')
              ->update(['translation' => 'Zinkwerk hellend dak']);

            $mTrans = DB::table('translations')
                        ->where('language', '=', 'nl')
                        ->where('translation', '=', 'Zinkwerk hellend dak')
                        ->first();

            if (! $mTrans) {
                $mnUuid = Str::uuid();
                \DB::table('translations')->insert([
                    'key'         => $mnUuid,
                    'language'    => 'nl',
                    'translation' => 'Zinkwerk plat dak',
                ]);
            } else {
                $mnUuid = $mTrans->key;
            }

            DB::table('measure_applications')
              ->where('short', '=', 'replace-zinc')
              ->update(
                  [
                      'measure_name' => $mnUuid,
                      'short'        => 'replace-zinc-pitched',
                      'costs'        => 100,
                      'cost_unit'    => $cuUuid,
                  ]
              );

            // Flat
            // Measure name
            $flatMeasureName = DB::table('translations')
                                 ->where('language', '=', 'nl')
                                 ->where('translation', '=',
                                     'Zinkwerk plat dak')
                                 ->first();

            if (! $flatMeasureName) {
                $fmnUuid = Str::uuid();
                \DB::table('translations')->insert([
                    'key'         => $fmnUuid,
                    'language'    => 'nl',
                    'translation' => 'Zinkwerk plat dak',
                ]);
            } else {
                $fmnUuid = $flatMeasureName->key;
            }

            $maintenanceUnit = DB::table('translations')
                                 ->where('language', '=', 'nl')
                                 ->where('translation', '=', 'jaar')
                                 ->first();

            if (! $maintenanceUnit) {
                $maintenanceUnitId = DB::table('translations')->insertGetId([
                    'key'         => Str::uuid(),
                    'language'    => 'nl',
                    'translation' => 'jaar',
                ]);

                $maintenanceUnit = DB::table('translations')->find($maintenanceUnitId);
            }

            DB::table('measure_applications')->updateOrInsert(
                [
                    'short' => 'replace-zinc-flat',
                ],
                [
                    'measure_type'         => 'maintenance',
                    'measure_name'         => $fmnUuid,
                    'application'          => 'replace',
                    'costs'                => 25,
                    'cost_unit'            => $cuUuid,
                    'minimal_costs'        => 250,
                    'maintenance_interval' => 25,
                    'maintenance_unit'     => $maintenanceUnit->key,
                    'step_id'              => $step->id,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $step = DB::table('steps')->where('slug', '=',
            'roof-insulation')->first();

        if (! is_null($step)) {
            // cost unit creation
            $costUnit = DB::table('translations')
                          ->where('language', '=', 'nl')
                          ->where('translation', '=', 'per m2')
                          ->first();

            if (! $costUnit) {
                $cuUuid = Str::uuid();
                \DB::table('translations')->insert([
                    'key'         => $cuUuid,
                    'language'    => 'nl',
                    'translation' => 'per m2',
                ]);
            } else {
                $cuUuid = $costUnit->key;
            }

            // Pitched
            // Measure name
            DB::table('translations')
              ->where('language', '=', 'nl')
              ->where('translation', '=', 'Zinkwerk hellend dak')
              ->update(['translation' => 'Zinkwerk']);

            $mTrans = DB::table('translations')
                        ->where('language', '=', 'nl')
                        ->where('translation', '=', 'Zinkwerk')
                        ->first();

            $mnUuid = $mTrans->key;

            DB::table('measure_applications')
              ->where('short', '=', 'replace-zinc-pitched')
              ->update(
                  [
                      'measure_name' => $mnUuid,
                      'short'        => 'replace-zinc',
                      'costs'        => 125,
                      'cost_unit'    => $cuUuid,
                  ]
              );

            $replaceZinc = DB::table('measure_applications')->where('short',
                '=',
                'replace-zinc')->first();

            $replaceZincFlat = DB::table('measure_applications')->where('short',
                '=', 'replace-zinc-flat')->first();

            $advicesToUpdate = DB::table('user_action_plan_advices')->where('measure_application_id',
                '=', $replaceZincFlat->id)->get();

            foreach ($advicesToUpdate as $adviceToUpdate) {
                // costs replace zinc = 125, replace zinc flat = 25, so: in action plan advice, do costs * 5.
                DB::table('user_action_plan_advices')
                  ->where('id', '=', $adviceToUpdate->id)
                  ->update([
                      'measure_application_id' => $replaceZinc->id,
                      'costs'                  => $adviceToUpdate->costs * 5,
                  ]);
            }

            // now delete the replace zinc flat measure application
            DB::table('measure_applications')->where('short', '=',
                'replace-zinc-flat')->delete();
        }
    }
}
