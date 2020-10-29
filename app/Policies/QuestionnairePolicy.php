<?php

namespace App\Policies;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionnairePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Check if the user is permitted to edit the questionnaire.
     *
     * @return bool
     */
    public function edit(User $user, Questionnaire $questionnaire)
    {
        // get the current cooperation
        $currentCooperation = HoomdossierSession::getCooperation(true);

        // check if the cooperation from the requested questionnaire is the same as the cooperation from the authenticated user
        return $questionnaire->cooperation->slug == $currentCooperation->slug;
    }

    /**
     * Check if the user is permitted to set the active status of a questionnaire.
     *
     * @return bool
     */
    public function setActiveStatus(User $user, Questionnaire $questionnaire)
    {
        // same logic (for now)
        return $this->edit($user, $questionnaire);
    }

    /**
     * Check if the user is permitted to create a new questionnaire.
     *
     * @return bool
     */
    public function store(User $user)
    {
        // if the user has the right roles
        return $user->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin']);
    }

    public function update(User $user, Questionnaire $questionnaire)
    {
        return $this->edit($user, $questionnaire);
    }

    public function delete(User $user, Questionnaire $questionnaire)
    {
        $currentCooperation = HoomdossierSession::getCooperation(true);

        // and check if the questionnaire from the question has a relation with the cooperation
        $cooperationFromQuestionnaire = $questionnaire->cooperation;

        // check if the user has the right roles
        return $user->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin']) && $cooperationFromQuestionnaire->slug == $currentCooperation->slug;
    }
}
