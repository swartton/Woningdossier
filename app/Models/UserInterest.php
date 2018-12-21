<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use App\Scopes\GetValueScope;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserInterest.
 *
 * @property int $id
 * @property int $user_id
 * @property string $interested_in_type
 * @property int $interested_in_id
 * @property int $interest_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \App\Models\Interest $interest
 * @property \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInterest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInterest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInterest whereInterestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInterest whereInterestedInId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInterest whereInterestedInType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInterest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserInterest whereUserId($value)
 * @mixin \Eloquent
 */
class UserInterest extends Model
{
    use GetValueTrait;

    protected $fillable = [
        'user_id', 'interested_in_type', 'interested_in_id', 'interest_id', 'input_source_id'
    ];


    /**
     * Normally we would use the GetMyValuesTrait, but that uses the building_id to query on.
     * The UserEnergyHabit uses the user_id instead off the building_id
     * @param $query
     * @return mixed
     */
    public function scopeForMe($query)
    {
        $building = Building::find(HoomdossierSession::getBuilding());

        return $query->withoutGlobalScope(GetValueScope::class)->where('user_id', $building->user_id);
    }


    /**
     * Get the input Sources
     *
     * @return InputSource
     */
    public function inputSource()
    {
        return $this->belongsTo('App\Models\InputSource');
    }

    /**
     * Get a input source name
     *
     * @return InputSource name
     */
    public function getInputSourceName()
    {
        return $this->inputSource()->first()->name;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function interest()
    {
        return $this->belongsTo(Interest::class);
    }

    public function getInterestsInServices()
    {
        $interests = [];
        /*$serviceInterests = $this->where('interested_in_type', 'element')->get();
        foreach($serviceInterests as $serviceInterest){
            $serviceInterest->interested_in_id;
            $element = Service::find($serviceInterest->interested_in_id);
            if ($element instanceof Service){
                $interests[]= $element;
            }
        }*/
        return $interests;
    }

    public function getInterestsInElements()
    {
        $interests = [];
        $serviceInterests = $this->where('interested_in_type', 'element')->get();
        /** @var self $serviceInterest */
        foreach ($serviceInterests as $serviceInterest) {
            $serviceInterest->interested_in_id;
            $element = Element::find($serviceInterest->interested_in_id);
            if ($element instanceof Element) {
                $interests[] = $element;
            }
        }

        return $interests;
    }

    public function getInterestInMeasureApplications()
    {
        $interests = [];
        $serviceInterests = $this->where('interested_in_type', 'measure_application')->get();
        /** @var self $serviceInterest */
        foreach ($serviceInterests as $serviceInterest) {
            $serviceInterest->interested_in_id;
            $element = MeasureApplication::find($serviceInterest->interested_in_id);
            if ($element instanceof MeasureApplication) {
                $interests[] = $element;
            }
        }

        return $interests;
    }

    public function getInterestsInRoofTypes()
    {
        $interests = [];
        $serviceInterests = $this->where('interested_in_type', 'roof_type')->get();
        /** @var self $serviceInterest */
        foreach ($serviceInterests as $serviceInterest) {
            $serviceInterest->interested_in_id;
            $element = RoofType::find($serviceInterest->interested_in_id);
            if ($element instanceof RoofType) {
                $interests[] = $element;
            }
        }

        return $interests;
    }

    public function getInterests()
    {
        return [
            'service' => $this->getInterestsInServices(),
            'element' => $this->getInterestsInElements(),
            'measure_application' => $this->getInterestInMeasureApplications(),
            'roof_type' => $this->getInterestsInRoofTypes(),
        ];
    }

    /**
     * Function to update or create the user interests.
     *
     * @param array $interests
     * @param User  $user
     */
    public static function saveUserInterests(User $user, array $interests)
    {
        foreach ($interests as $type => $interestTypes) {
            foreach ($interestTypes as $typeId => $interestId) {
                self::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'interested_in_type' => $type,
                        'interested_in_id' => $typeId,
                    ],
                    [
                        'interest_id' => $interestId,
                    ]
                );
            }
        }
    }
}
