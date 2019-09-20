<?php

namespace App\Services;

use App\Helpers\Calculator;
use App\Helpers\NumberFormatter;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Scopes\GetValueScope;
use Carbon\Carbon;

class UserActionPlanAdviceService {

    /**
     * Method to return input sources that have an action plan advice, on a building
     *
     * @param  User $user
     * @return UserActionPlanAdvice[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public static function availableInputSourcesForActionPlan(User $user)
    {
        return UserActionPlanAdvice::withoutGlobalScope(GetValueScope::class)
            ->where('user_id', $user->id)
            ->select('input_source_id')
            ->groupBy('input_source_id')
            ->get()
            ->map(function ($userActionPlanAdvice) {
                return $userActionPlanAdvice->inputSource;
            });
    }

    /**
     * Get the personal plan for a user and its input source
     *
     * @param User $user
     * @param InputSource $inputSource
     * @return array
     */
    public static function getPersonalPlan(User $user, InputSource $inputSource): array
    {
        $advices = self::getCategorizedActionPlan($user, $inputSource);

        $sortedAdvices = [];

        foreach($advices as $measureType => $stepAdvices) {

            foreach ($stepAdvices as $stepSlug => $advicesForStep) {

                foreach ($advicesForStep as $advice) {

                    $year = $advice->getYear($inputSource);

                    // if its a string, the $year contains 'geen jaartal'
                    if (is_string($year)) {
                        $costYear = Carbon::now()->year;
                    } else {
                        $costYear = $year;
                    }
                    if (!array_key_exists($year, $sortedAdvices)) {
                        $sortedAdvices[$year] = [];
                    }
                    // get step from advice
                    $step = $advice->step;

                    if ( ! array_key_exists($step->name, $sortedAdvices[$year])) {
                        $sortedAdvices[$year][$step->name] = [];
                    }

                    $sortedAdvices[$year][$step->name][] = [
                        'interested'          => $advice->planned,
                        'advice_id' => $advice->id,
                        'measure' => $advice->measureApplication->measure_name,
                        'measure_short'       => $advice->measureApplication->short,                    // In the table the costs are indexed based on the advice year
                        // Now re-index costs based on user planned year in the personal plan
                        'costs'               => NumberFormatter::round(Calculator::indexCosts($advice->costs, $costYear)),
                        'savings_gas'         => is_null($advice->savings_gas) ? 0 : NumberFormatter::round($advice->savings_gas),
                        'savings_electricity' => is_null($advice->savings_electricity) ? 0 : NumberFormatter::round($advice->savings_electricity),
                        'savings_money'       => is_null($advice->savings_money) ? 0 : NumberFormatter::round(Calculator::indexCosts($advice->savings_money, $costYear)),
                    ];
                }
            }
        }
        ksort($sortedAdvices);

        return $sortedAdvices;
    }

    /**
     * Get the action plan categorized under measure type
     *
     * @param User $user
     * @param InputSource $inputSource
     * @param bool $withAdvices
     * @return array
     */
    public static function getCategorizedActionPlan(User $user, InputSource $inputSource, $withAdvices = true)
    {

        $result = [];
        $advices = UserActionPlanAdvice::forInputSource($inputSource)
            ->where('user_id', $user->id)
            ->orderBy('step_id', 'asc')
            ->orderBy('year', 'asc')
            ->get();

        foreach ($advices as $advice) {

            if ($advice->step instanceof Step) {

                /** @var MeasureApplication$measureApplication */
                $measureApplication = $advice->measureApplication;

                if (is_null($advice->year)) {
                    $advice->year = $advice->getAdviceYear();
                }
                // if advices are not desirable and the measureApplication is not an advice it will be added to the result
                if (!$withAdvices && !$measureApplication->isAdvice()) {
                    $result[$measureApplication->measure_type][$advice->step->slug][] = $advice;
                }

                // if advices are desirable we always add it.
                if ($withAdvices) {
                    $result[$measureApplication->measure_type][$advice->step->slug][] = $advice;
                }
            }
        }

        ksort($result);

        return $result;
    }
}