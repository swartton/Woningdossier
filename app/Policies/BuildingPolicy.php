<?php

namespace App\Policies;

use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
use App\Models\PrivateMessage;
use App\Models\User;
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


    /**
     * Determine if a user is allowed to see a building overview
     *
     *
     * @param  User  $user
     * @param  Building  $building
     *
     * @return bool
     */
    public function show(User $user, Building $building)
    {
        if ($user->hasRoleAndIsCurrentRole('coach')) {
            // get the buildings the user is connected to.
            $connectedBuildingsForUser = BuildingCoachStatus::getConnectedBuildingsByUserId($user->id);

            // check if the current building is in that collection.
            return (bool) $connectedBuildingsForUser->contains('building_id', $building->id);
        }

        // they can always view a building.
        return (bool) $user->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin']);
    }


    /**
     * Determine if a user is allowed to view the building info
     *
     * With building info we mean stuff like associate coaches, make appointments etc.
     * This is authorized when a user gave access in the conversation request / the allow_access is set to true
     *
     * @param  User  $user
     * @param  Building  $building
     *
     * @return bool
     */
    public function viewBuildingInfo(User $user, Building $building): bool
    {
        return PrivateMessage::allowedAccess($building->id);
    }

    /**
     * Determine if a user can access his building
     *
     * @param User $user
     * @param Building $building
     * @return bool
     */
    public function accessBuilding(User $user, Building $building): bool
    {

        if ($user->hasRoleAndIsCurrentRole('coach')) {

            // check if the coach has building permission
            $coachHasBuildingPermission = Building::withTrashed()->find($building->id)->buildingPermissions()->where('user_id', $user->id)->first() instanceof BuildingPermission;

            return (bool) PrivateMessage::allowedAccess($building->id) && $coachHasBuildingPermission;
        }

        // they can always access a building (if the user / resident gave access)
        return (bool) PrivateMessage::allowedAccess($building->id) && $user->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin']);
    }
}
