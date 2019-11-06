<?php

namespace App\Helpers;

use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingHeater;
use App\Models\BuildingPvPanel;
use App\Models\Element;
use App\Models\InputSource;
use App\Models\Interest;
use App\Models\Questionnaire;
use App\Models\Service;
use App\Models\Step;
use App\Models\StepComment;
use App\Models\User;
use App\Models\UserActionPlanAdviceComments;
use App\Models\UserEnergyHabit;
use App\Models\CompletedStep;
use App\Models\UserInterest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Log;

class StepHelper
{
    const ELEMENT_TO_SHORT = [
        'sleeping-rooms-windows' => 'insulated-glazing',
        'living-rooms-windows' => 'insulated-glazing',
        'crack-sealing' => 'insulated-glazing',
        'wall-insulation' => 'wall-insulation',
        'floor-insulation' => 'floor-insulation',
        'roof-insulation' => 'roof-insulation',
    ];
    const SERVICE_TO_SHORT = [
        'hr-boiler' => 'high-efficiency-boiler',
        'boiler' => 'high-efficiency-boiler',
        'total-sun-panels' => 'solar-panels',
        'sun-boiler' => 'heater',
        'house-ventilation' => 'ventilation'
    ];

    public static function getAllCommentsByStep(User $user, $withEmptyComments = false): array
    {
        $building = $user->building;
        $commentsByStep = [];

        if (!$building instanceof Building) {
            return [];
        }

        $stepComments = StepComment::forMe($user)->with('step', 'inputSource')->get();

        foreach ($stepComments as $stepComment) {
            if (is_null($stepComment->short)) {
                $commentsByStep[$stepComment->step->short][$stepComment->inputSource->name] = $stepComment->comment;
            } else {
                $commentsByStep[$stepComment->step->short][$stepComment->inputSource->name][$stepComment->short] = $stepComment->comment;
            }
        }

        return $commentsByStep;
    }

    /**
     * Method to check whether a user has interest in a step
     *
     * @param User $user
     * @param InputSource $inputSource
     * @param $interestedInType
     * @param $interestedInId
     * @return bool
     */
    public static function hasInterestInStep(User $user, $interestedInType, $interestedInId, $inputSource = null): bool
    {
        $noInterestIds = Interest::whereIn('calculate_value', [4, 5])->select('id')->get()->pluck('id')->toArray();

        $userSelectedInterestedId = null;
        if ($inputSource instanceof InputSource) {
            $userSelectedUserInterest = $user->userInterestsForSpecificType($interestedInType, $interestedInId, $inputSource)->first();
        } else {
            $userSelectedUserInterest = $user->userInterestsForSpecificType($interestedInType, $interestedInId)->first();
        }

        if ($userSelectedUserInterest instanceof UserInterest) {
            $userSelectedInterestedId = $userSelectedUserInterest->interest_id;
        }

        return !in_array($userSelectedInterestedId, $noInterestIds);
    }

    /**
     * Get the next step for a user where the user shows interest in or the next questionnaire for a user.
     *
     * @param Building      $building
     * @param InputSource   $inputSource
     * @param Step          $current
     * @param Questionnaire $currentQuestionnaire
     *
     * @return array
     */
    public static function getNextStep(Building $building, InputSource $inputSource, Step $current, Questionnaire $currentQuestionnaire = null): array
    {
        // count all the active questionnaires for the current step
        $allActiveQuestionnairesForCurrentStepCount = $current->questionnaires()->active()->count();
        $user = $building->user;

        // before we check for other steps we want to check if the current step has active additional questionnaires
        // if it does and the user did not finish those we redirect to that tab
        if ($current->hasQuestionnaires() && $allActiveQuestionnairesForCurrentStepCount > 0) {
            // create the base query to obtain the non completed questionnaires for the current step.
            $nonCompletedQuestionnairesForCurrentStepQuery = $current->questionnaires()
                ->whereNotExists(function (Builder $query) use ($user) {
                    $query->select('*')
                        ->from('completed_questionnaires')
                        ->whereRaw('questionnaires.id = completed_questionnaires.questionnaire_id')
                        ->where('user_id', $user->id);
                })->active()
                ->orderBy('order');
            // since it can be null
            if ($currentQuestionnaire instanceof Questionnaire) {
                $nextQuestionnaire = $nonCompletedQuestionnairesForCurrentStepQuery
                    ->where('id', '!=', $currentQuestionnaire->id)
                    ->where('order', '>', $currentQuestionnaire->order)
                    ->first();
                // and return it with the tab id
                if ($nextQuestionnaire instanceof Questionnaire) {
                    return ['url' => route('cooperation.tool.'.$current->slug.'.index'), 'tab_id' => 'questionnaire-'.$nextQuestionnaire->id];
                }
            } else {
                // no need for extra queries.
                $nextQuestionnaire = $nonCompletedQuestionnairesForCurrentStepQuery->first();
                if ($nextQuestionnaire instanceof Questionnaire) {
                    return ['url' => route('cooperation.tool.'.$current->slug.'.index'), 'tab_id' => 'questionnaire-'.$nextQuestionnaire->id];
                }
            }
        }
        // the step does not have custom questionnaires or the user does not have uncompleted questionnaires left for that step.
        // so we will redirect them to a next step.
        // retrieve the non completed steps for a user.
        // we leave out the general data step itself since thats not a "real" step anymore
        $nonCompletedSteps = $user->cooperation
            ->steps()
            ->where('steps.short', '!=', 'general-data')
            ->orderBy('cooperation_steps.order')
            ->where('cooperation_steps.is_active', '1')
            ->whereNotExists(function (Builder $query) use ($building, $inputSource) {
                $query->select('*')
                    ->from('completed_steps')
                    ->whereRaw('steps.id = completed_steps.step_id')
                    ->where('building_id', $building->id)
                    ->where('input_source_id', $inputSource->id);
            })->get();

        // check if a user is interested
        // and if so return the route name
        foreach ($nonCompletedSteps as $nonCompletedStep) {
            // for now its not possible to give an interest to a substep. So if its a supstep and not completed just return it
            if ($nonCompletedStep->isSubStep() || self::hasInterestInStep($user, Step::class, $nonCompletedStep->id)) {

                $routeName = $nonCompletedStep->isSubStep()
                    ? 'cooperation.tool.'.$nonCompletedStep->parentStep->short.'.'.$nonCompletedStep->short.'.index'
                    : 'cooperation.tool.'.$nonCompletedStep->short.'.index';

                return ['url' => route($routeName), 'tab_id' => ''];
            }
        }
        // if the user has no steps left where they do not have any interest in, redirect them to their plan
        return ['url' => route('cooperation.tool.my-plan.index'), 'tab_id' => ''];
    }

    /**
     * Complete a step for a building.
     *
     * @param Step        $step
     * @param Building    $building
     * @param InputSource $inputSource
     *
     * @return Model|CompletedStep
     */
    public static function complete(Step $step, Building $building, InputSource $inputSource)
    {
        return CompletedStep::firstOrCreate([
            'step_id' => $step->id,
            //'input_source_id' => HoomdossierSession::getInputSource(),
            'input_source_id' => $inputSource->id,
            //'building_id' => HoomdossierSession::getBuilding(),
            'building_id' => $building->id,
        ]);
    }
}
