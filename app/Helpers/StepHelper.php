<?php

namespace App\Helpers;

use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingHeater;
use App\Models\BuildingPvPanel;
use App\Models\BuildingRoofType;
use App\Models\Cooperation;
use App\Models\Element;
use App\Models\InputSource;
use App\Models\Questionnaire;
use App\Models\RoofType;
use App\Models\Service;
use App\Models\Step;
use App\Models\UserEnergyHabit;
use App\Models\UserProgress;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Support\Arr;

class StepHelper
{
    const STEP_INTERESTS = [
        'ventilation-information' => [
            'service' => [
                6,
            ],
        ],
        // step name
        'wall-insulation' => [
            // type
            'element' => [
                // interested in id (Element id, service id etc)
                3,
            ],
        ],
        'insulated-glazing' => [
            'element' => [
                1,
                2,
            ],
        ],
        'floor-insulation' => [
            'element' => [
                4,
            ],
        ],
        'roof-insulation' => [
            'element' => [
                5,
            ],
        ],
        'high-efficiency-boiler' => [
            'service' => [
                4,
            ],
        ],
        'heat-pump' => [
            'service' => [
                1,
                2,
            ],
        ],
        'solar-panels' => [
            'service' => [
                7,
            ],
        ],
        'heater' => [
            'service' => [
                3,
            ],
        ],
    ];

    /**
     * Method to return a collection of all comments, categorized under step and input source.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getAllCommentsByStep()
    {
        $building = Building::find(HoomdossierSession::getBuilding());
        $allInputForMe = collect();
        $commentsByStep = collect();
        $comment = '';

        /* General-data */
        $userEnergyHabitForMe = UserEnergyHabit::forMe()->get();
        $allInputForMe->put('general-data', $userEnergyHabitForMe);

        /* wall insulation */
        $buildingFeaturesForMe = BuildingFeature::forMe()->get();
        $allInputForMe->put('wall-insulation', $buildingFeaturesForMe);

        /* floor insualtion */
        $crawlspace = Element::where('short', 'crawlspace')->first();
        $buildingElementsForMe = BuildingElement::forMe()->get();
        $allInputForMe->put('floor-insulation', $buildingElementsForMe->where('element_id', $crawlspace->id));

        /* beglazing */
        $insulatedGlazingsForMe = $building->currentInsulatedGlazing()->forMe()->get();
        $allInputForMe->put('insulated-glazing', $insulatedGlazingsForMe);

        /* roof */
        $currentRoofTypesForMe = $building->roofTypes()->forMe()->get();
        $allInputForMe->put('roof-insulation', $currentRoofTypesForMe);

        /* hr boiler ketel */
        $boiler = Service::where('short', 'boiler')->first();
        $installedBoilerForMe = $building->buildingServices()->forMe()->where('service_id', $boiler->id)->get();
        $allInputForMe->put('high-efficiency-boiler', $installedBoilerForMe);

        /* sun panel*/
        $buildingPvPanelForMe = BuildingPvPanel::forMe()->get();
        $allInputForMe->put('solar-panels', $buildingPvPanelForMe);

        /* heater */
        $buildingHeaterForMe = BuildingHeater::forMe()->get();
        $allInputForMe->put('heater', $buildingHeaterForMe);


        foreach ($allInputForMe as $step => $inputForMeByInputSource) {
            foreach ($inputForMeByInputSource as $inputForMe) {

                if (is_array($inputForMe->extra) && array_key_exists('comment', $inputForMe->extra)) {
                    $comments = array_filter(
                        [$inputForMe->inputSource->name => $inputForMe->extra['comment']]
                    );
                } else {
                    $possibleAttributes = ['comment', 'additional_info', 'living_situation_extra', 'motivation_extra'];
                    // get the comment fields, and filter out the empty ones.
                    $comments = array_filter(
                        [Arr::only($inputForMe->getAttributes(), $possibleAttributes)]
                    );

                    $comments = [$inputForMe->inputSource->name => $comments];

                }

                if ($inputForMe instanceof BuildingRoofType) {
                    $commentsByStep->put($step.'-'.str_slug(RoofType::find($inputForMe->roof_type_id)->name), $comments);
                } else {
                    $commentsByStep->put($step, $comments);
                }
            }
        }
        return $commentsByStep;
    }

    /**
     * Check is a user is interested in a step.
     *
     * @param Step $step
     *
     * @return bool
     */
    public static function hasInterestInStep(Step $step): bool
    {
        $building = Building::find(HoomdossierSession::getBuilding());

        if (array_key_exists($step->slug, self::STEP_INTERESTS)) {
            foreach (self::STEP_INTERESTS[$step->slug] as $type => $interestedIn) {
                if ($building->isInterestedInStep($type, $interestedIn)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get the next step for a user where the user shows interest in or the next questionnaire for a user.
     *
     * @param Step          $current
     * @param Questionnaire $currentQuestionnaire
     *
     * @return array
     */
    public static function getNextStep(Step $current, Questionnaire $currentQuestionnaire = null): array
    {
        // get all the steps
        $steps = Cooperation::find(HoomdossierSession::getCooperation())->getActiveOrderedSteps();
        // create new collection for the completed steps
        $completedSteps = collect();

        $currentFound = false;

        // count all the active questionnaires for the current step
        $allActiveQuestionnairesForCurrentStepCount = $current->questionnaires()->active()->count();

        // before we check for other pets we want to check if the current step has active additional questionnaires
        // if it does and the user did not finish those we redirect to that tab
        if ($current->hasQuestionnaires() && $allActiveQuestionnairesForCurrentStepCount > 0) {
            // since it can be null
            if ($currentQuestionnaire instanceof Questionnaire) {
                // if so, get the next questionnaire in the right order
                $nextQuestionnaire = $current->questionnaires()
                    ->active()
                    ->where('id', '!=', $currentQuestionnaire->id)
                    ->where('order', '>', $currentQuestionnaire->order)
                    ->orderBy('order')
                    ->first();

                // and return it with the tab id
                if ($nextQuestionnaire instanceof Questionnaire) {
                    return ['route' => 'cooperation.tool.'.$current->slug.'.index', 'tab_id' => 'questionnaire-'.$nextQuestionnaire->id];
                }
            } else {
                // else, we just redirect them to the first questionnaire.
                $nextQuestionnaire = $current->questionnaires()->active()->orderBy('order')->first();

                return ['route' => 'cooperation.tool.'.$current->slug.'.index', 'tab_id' => 'questionnaire-'.$nextQuestionnaire->id];
            }
        }

        // the step does not have custom questionnaires or the user does not have uncompleted questionnaires left for that step.
        // so we will redirect them to a next step.

        // remove the completed steps from the steps
        foreach ($steps as $step) {
            if ($step->id != $current->id && ! $currentFound) {
                $completedStep = $steps->search(function ($item) use ($step) {
                    return $item->id == $step->id;
                });

                $completedSteps->push($steps->pull($completedStep));
            } elseif ($step->id == $current->id) {
                $currentFound = true;

                $completedStep = $steps->search(function ($item) use ($step) {
                    return $item->id == $step->id;
                });

                $completedSteps->push($steps->pull($completedStep));
            }
        }

        // since we pulled the completed steps of the collection
        $nonCompletedSteps = $steps;
        // check if a user is interested
        // and if so return the route name
        foreach ($nonCompletedSteps as $nonCompletedStep) {
            if (self::hasInterestInStep($nonCompletedStep)) {
                $routeName = 'cooperation.tool.'.$nonCompletedStep->slug.'.index';

                return ['route' => $routeName, 'tab_id' => ''];
            }
        }

        // if the user has no steps left where they do not have any interest in, redirect them to their plan
        return ['route' => 'cooperation.tool.my-plan.index', 'tab_id' => ''];
    }

    /**
     * Complete a step for a building.
     *
     * @param Step $step
     * @param Building $building
     * @param InputSource $inputSource
     *
     * @return Model|UserProgress
     */
    public static function complete(Step $step, Building $building, InputSource $inputSource)
    {
        return UserProgress::firstOrCreate([
            'step_id' => $step->id,
            //'input_source_id' => HoomdossierSession::getInputSource(),
            'input_source_id' => $inputSource->id,
            //'building_id' => HoomdossierSession::getBuilding(),
            'building_id' => $building->id,
        ]);
    }
}
