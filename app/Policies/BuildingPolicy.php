<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\Building;
use App\Models\BuildingPermission;
use App\Models\PrivateMessage;
use App\Models\User;
use App\Services\BuildingCoachStatusService;
use Illuminate\Auth\Access\HandlesAuthorization;

class BuildingPolicy
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

    public function edit(Account $account, Building $building)
    {
        $user = $account->user();
        // While a user is allowed to see his own stuff, he is not allowed to do anything in it.
        if ($user->id === $building->user_id) {
            return false;
        }

        return $user->hasRoleAndIsCurrentRole(['cooperation-admin', 'coordinator']);
    }

    /**
     * Determine if a user is allowed to see a building overview.
     *
     * @return bool
     */
    public function show(Account $account, Building $building)
    {
        $user = $account->user();
        if ($user->hasRoleAndIsCurrentRole('coach')) {
            // get the buildings the user is connected to.
            $connectedBuildingsForUser = BuildingCoachStatusService::getConnectedBuildingsByUser($user);

            // check if the current building is in that collection.
            return $connectedBuildingsForUser->contains('building_id', $building->id);
        }

        // they can always view a building.
        return $user->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin']);
    }

    /**
     * Determine if its possible / authorized to talk to a resident.
     *
     * Its possible when there is 1 public message from the resident itself.
     *
     * @return bool
     */
    public function talkToResident(Account $account, Building $building)
    {
        $user = $account->user();
        if ($user->hasRoleAndIsCurrentRole('coach')) {
            // get the buildings the user is connected to.
            $connectedBuildingsForUser = BuildingCoachStatusService::getConnectedBuildingsByUser($user);

            // check if the current building is in that collection and if there are public messages.
            return $connectedBuildingsForUser->contains('building_id', $building->id) && $building->privateMessages()->public()->first() instanceof PrivateMessage;
        }

        return $user->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin']) && $building->privateMessages()->public()->first() instanceof PrivateMessage;
    }

    /**
     * Determine if a user can access his building.
     *
     * With access we mean observing / filling the tool.
     */
    public function accessBuilding(Account $account, Building $building): bool
    {
        $user = $account->user();

        // While a user is allowed to see his own stuff, he is not allowed to do anything in it.
        if ($user->id === $building->user_id) {
            return false;
        }

        if ($user->hasRoleAndIsCurrentRole('coach')) {
            // check if the coach has building permission
            $coachHasBuildingPermission = Building::withTrashed()
                    ->find($building->id)
                    ->buildingPermissions()
                    ->where('user_id', $user->id)->first() instanceof BuildingPermission;

            return $building->user->allowedAccess() && $coachHasBuildingPermission;
        }

        // they can always access a building (if the user / resident gave access)
        return $building->user->allowedAccess() && $user->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin']);
    }

    /**
     * Check whether its allowed to set an appointment on a building.
     */
    public function setAppointment(Account $account, Building $building): bool
    {
        // a user cant set an appointment on its own building
        return $account->user()->id != $building->user_id;
    }

    /**
     * Check whether its allowed to set an status on a building.
     */
    public function setStatus(Account $account, Building $building): bool
    {
        // a user cant set the building status on its own building
        return $account->user()->id != $building->user_id;
    }
}
