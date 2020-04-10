<?php

namespace App\Helpers\Cooperation\Tool;

use App\Calculations\HighEfficiencyBoiler;
use App\Events\StepCleared;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingService;
use App\Models\Element;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Service;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Scopes\GetValueScope;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HighEfficiencyBoilerHelper
{

    /**
     * Method to clear all the saved data for the step, except for the comments.
     *
     * @param Building $building
     * @param InputSource $inputSource
     * @param array $buildingFeatureData
     * @param array $buildingElementData
     */
    public static function save(Building $building, InputSource $inputSource, array $saveData)
    {
        $service = Service::findByShort('boiler');

        BuildingService::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'service_id' => $service->id,
            ],
            $saveData['building_services']
        );

        UserEnergyHabit::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'user_id' => $building->user_id,
                'input_source_id' => $inputSource->id,
            ],
            $saveData['user_energy_habits']
        );

        self::saveAdvices($building, $inputSource, $saveData);
    }

    /**
     * Save the advices for the step
     *
     * @param Building $building
     * @param InputSource $inputSource
     * @param array $saveData
     * @throws \Exception
     */
    public static function saveAdvices(Building $building, InputSource $inputSource, array $saveData)
    {
        $user = $building->user;

        $results = HighEfficiencyBoiler::calculate($user->energyHabit, $saveData);

        $step = Step::findByShort('high-efficiency-boiler');

        // remove old results
        UserActionPlanAdviceService::clearForStep($user, $inputSource, $step);

        if (isset($results['cost_indication']) && $results['cost_indication'] > 0) {
            $measureApplication = MeasureApplication::where('short', 'high-efficiency-boiler-replace')->first();
            if ($measureApplication instanceof MeasureApplication) {
                $actionPlanAdvice = new UserActionPlanAdvice($results);
                $actionPlanAdvice->costs = $results['cost_indication'];
                $actionPlanAdvice->year = $results['replace_year'];
                $actionPlanAdvice->user()->associate($user);
                $actionPlanAdvice->measureApplication()->associate($measureApplication);
                $actionPlanAdvice->step()->associate($step);
                $actionPlanAdvice->save();
            }
        }
    }

    /**
     * * Method to clear the hr step
     *
     * @param Building $building
     * @param InputSource $inputSource
     * @throws \Exception
     */
    public static function clear(Building $building, InputSource $inputSource)
    {
        $service = Service::findByShort('boiler');

        BuildingService::forMe($building->user)
            ->forInputSource($inputSource)
            ->where('service_id', $service->id)
            ->delete();



        // questionable reset as this is base data
//        UserEnergyHabit::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
//            [
//                'user_id' => $building->user->id,
//                'input_source_id' => $inputSource->id,
//            ],
//            [
//                'amount_gas' => null,
//                'resident_count' => null,
//            ]
//        );

        StepCleared::dispatch($building->user, $inputSource, Step::findByShort('high-efficiency-boiler'));
    }
}