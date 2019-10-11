<?php

namespace App\Models;

use App\Helpers\Calculator;
use App\Helpers\HoomdossierSession;
use App\Helpers\NumberFormatter;
use App\Scopes\GetValueScope;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use App\Traits\ToolSettingTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * App\Models\UserActionPlanAdvice
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $input_source_id
 * @property int $measure_application_id
 * @property float|null $costs
 * @property float|null $savings_gas
 * @property float|null $savings_electricity
 * @property float|null $savings_money
 * @property int|null $year
 * @property bool $planned
 * @property int|null $planned_year
 * @property int $step_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read \App\Models\MeasureApplication $measureApplication
 * @property-read \App\Models\Step $step
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice forMe(\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice forStep(\App\Models\Step $step)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereCosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereMeasureApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice wherePlanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice wherePlannedYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereSavingsElectricity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereSavingsGas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereSavingsMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereYear($value)
 * @mixin \Eloquent
 */
class UserActionPlanAdvice extends Model
{
    use GetValueTrait, GetMyValuesTrait, ToolSettingTrait;

    public $fillable = [
        'user_id', 'measure_application_id', // old
        'costs', 'savings_gas', 'savings_electricity', 'savings_money',
        'year', 'planned', 'planned_year', 'input_source_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'planned' => 'boolean',
    ];


    /**
     * Scope a query to only include results for the particular step.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Step                                  $step
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForStep($query, Step $step)
    {
        return $query->where('step_id', $step->id);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function measureApplication()
    {
        return $this->belongsTo(MeasureApplication::class);
    }

    public function step()
    {
        return $this->belongsTo(Step::class);
    }

    /**
     * Get all the comments that are saved in multiple tables.
     *
     * @return Collection
     */
    public static function getAllCoachComments(): Collection
    {
        $building = HoomdossierSession::getBuilding(true);
        $allInputForMe = collect();
        $coachComments = collect();
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


        foreach ($allInputForMe as $step => $inputForMe) {
            // get the coach his input from the collection
            $coachInputSource = InputSource::findByShort('coach');
            // get the coach answers
            $coachInputs = $inputForMe->where('input_source_id', $coachInputSource->id);

            // loop through them and extract the comments from them
            foreach ($coachInputs as $coachInput) {
                if (! is_null($coachInput)) {

                    if (is_array($coachInput->extra) && array_key_exists('comment', $coachInput->extra)) {
                        $comments = [$coachInput->extra['comment']];
                    } else {
                        $possibleAttributes = ['comment', 'additional_info', 'living_situation_extra', 'motivation_extra'];

                        $comments = Arr::only($coachInput->attributes, $possibleAttributes);
                    }

                    // for the rooftype there are multiple comments
                    if ($coachInput instanceof BuildingRoofType) {
                        $coachComments->put($step.'-'.str_slug(RoofType::find($coachInput->roof_type_id)->name), $comments);
                    } else {
                        // comment as key, yes. Comments will be unique.
                        $coachComments->put($step, $comments);
                    }
                }
            }
        }
        return $coachComments;
    }

    public function getAdviceYear(InputSource $inputSource = null)
    {
        // todo Find a neater solution for this as this was one of many additions in hindsight
        // Step slug => element short
        $slugElements = [
            'wall-insulation' => 'wall-insulation',
            //'insulated-glazing' => 'living-rooms-windows', // this is nonsense.. there's no location specification in this step, while there is on general-data
            'floor-insulation' => 'floor-insulation',
            //'roof-insulation' => 'roof-insulation',
        ];
        if (! $this->step instanceof Step) {
            return null;
        }

        if ('insulated-glazing' == $this->step->slug) {
            $userInterest = $this->user->getInterestedType('measure_application', $this->measureApplication->id, $inputSource);
            if (! $userInterest instanceof UserInterest) {
                return null;
            }
            if (1 == $userInterest->interest->calculate_value) {
                return Carbon::now()->year;
            }
            if (2 == $userInterest->interest->calculate_value) {
                return Carbon::now()->year + 5;
            }

            return null;
        }

        if (! array_key_exists($this->step->slug, $slugElements)) {
            return null;
        }
        $elementShort = $slugElements[$this->step->slug];
        $element = Element::where('short', $elementShort)->first();
        if (! $element instanceof Element) {
            return null;
        }
        $userInterest = $this->user->getInterestedType('element', $element->id, $inputSource);
        if (! $userInterest instanceof UserInterest) {
            return null;
        }
        if (1 == $userInterest->interest->calculate_value) {
            return Carbon::now()->year;
        }
        if (2 == $userInterest->interest->calculate_value) {
            return Carbon::now()->year + 5;
        }

        return null;
    }

    /**
     * Method to return a year for the personal plan
     *
     * @return array|int|null|string
     */
    public function getYear(InputSource $inputSource)
    {
        $year = isset($this->planned_year) ? $this->planned_year : $this->year;

        if (is_null($year)) {
            $year = $this->getAdviceYear($inputSource) ?? __('woningdossier.cooperation.tool.my-plan.no-year');
        }

        return $year;
    }


    /**
     * Check whether someone is interested in the measure
     *
     * @param Building $building
     * @param InputSource $inputSource
     * @param Step $step
     *
     * @return bool
     */
    public static function hasInterestInMeasure(Building $building, InputSource $inputSource, Step $step): bool
    {
        return self::forInputSource($inputSource)
            ->where('user_id', $building->user_id)
            ->where('step_id', $step->id)
            ->where('planned', true)
            ->first() instanceof UserActionPlanAdvice;
    }
}
