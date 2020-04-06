<?php

namespace App\Helpers\Cooperation\Tool;

use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingInsulatedGlazing;
use App\Models\BuildingPaintworkStatus;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\InputSource;
use App\Scopes\GetValueScope;
use App\Services\ModelService;

class InsulatedGlazingHelper
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

        $buildingFeatureData = $saveData['building_features'];
        $buildingInsulatedGlazingData = $saveData['building_insulated_glazings'];
        $buildingElementData = $saveData['building_elements'];
        $buildingPaintworkStatusData = $saveData['building_paintwork_statuses'];


        foreach ($buildingInsulatedGlazingData as $measureApplicationId => $buildingInsulatedGlazing) {
            // update or Create the buildingInsulatedGlazing
            BuildingInsulatedGlazing::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
                [
                    'building_id' => $building->id,
                    'input_source_id' => $inputSource->id,
                    'measure_application_id' => $measureApplicationId,
                ],
                $buildingInsulatedGlazingData
            );
        }


        $frames = Element::where('short', 'frames')->first();
        $woodElements = Element::where('short', 'wood-elements')->first();

        // lets save the frame element value (main element)
        BuildingElement::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'element_id' => $frames->id
            ],
            [
                'element_value_id' => $buildingElementData[$frames->id]
            ]
        );


        // collect the wood element create data
        // after that we can delete the old records and create the new ones
        $woodElementCreateData = [];
        foreach ($buildingElementData[$woodElements->id] as $woodElementValueId) {
            $woodElementCreateData[] = [
                'element_value_id' => $woodElementValueId
            ];
        }
        ModelService::deleteAndCreate(BuildingElement::class,
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'element_id' => $woodElements->id,
            ],
            $woodElementCreateData
        );


        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            $buildingFeatureData
        );

        $lastPaintedYear = null;
        if (array_key_exists('last_painted_year', $buildingPaintworkStatusData)) {
            $year = (int)$buildingPaintworkStatusData['last_painted_year'];
            if ($year > 1950) {
                $buildingPaintworkStatusData['last_painted_year'] = $year;
            }
        }
        BuildingPaintworkStatus::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            $buildingPaintworkStatusData
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
        $frames = Element::where('short', 'frames')->first();
        $woodElements = Element::where('short', 'wood-elements')->first();

        BuildingFeature::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            [
                'window_surface' => null
            ]
        );

        // delete the building elements for the page, wood element and frame
        BuildingElement::forMe($building->user)
            ->forInputSource($inputSource)
            ->where(function ($query) use ($woodElements, $frames) {
                return $query->where('element_id', $woodElements->id)
                    ->orWhere('element_id', $frames->id);
            })->delete();

        BuildingPaintworkStatus::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            [
                'last_painted_year' => null,
                'paintwork_status_id' => null,
                'wood_rot_status_id' => null
            ]
        );
    }
}