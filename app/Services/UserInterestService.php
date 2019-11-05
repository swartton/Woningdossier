<?php

namespace App\Services;

use App\Models\InputSource;
use App\Models\User;
use App\Models\UserInterest;

class UserInterestService {

    /**
     * Method to save a interest for a specific type and id.
     *
     * @param User $user
     * @param $interestedInType
     * @param int $interestedInId
     * @param int $interestId
     */
    public static function save(User $user, InputSource $inputSource, $interestedInType, int $interestedInId, int $interestId)
    {
        UserInterest::forMe($user)->updateOrCreate(
            [
                'input_source_id' => $inputSource->id,
                'interested_in_type' => $interestedInType,
                'interested_in_id' => $interestedInId,
            ],
            [
                'interest_id' => $interestId,
            ]
        );
    }
}