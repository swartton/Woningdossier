<?php

namespace App\Helpers\Cooperation\Tool;

use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\InputSource;
use App\Scopes\GetValueScope;

class WallInsulationHelper
{

    /**
     * Method to clear all the saved data for the step, except for the comments.
     *
     * @param Building $building
     * @param InputSource $inputSource
     * @param array $saveData
     */
    public static function save(Building $building, InputSource $inputSource, array $saveData)
    {
        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            $saveData
        );
    }

    /**
     * Method to clear the building feature data for wall insulation step.
     *
     * @param Building $building
     * @param InputSource $inputSource
     */
    public static function clear(Building $building, InputSource $inputSource)
    {
        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            [
                'facade_plastered_surface_id' => null,
                'wall_joints' => null,
                'cavity_wall' => null,
                'contaminated_wall_joints' => null,
                'wall_surface' => null,
                'insulation_wall_surface' => null,
                'facade_damaged_paintwork_id' => null,
                'facade_plastered_painted' => null
            ]
        );
    }
}