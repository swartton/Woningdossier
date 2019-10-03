<?php

namespace App\Policies;

use App\Helpers\HoomdossierSession;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

class UserPolicy
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
     * Check if a user is authorized to do admin stuff.
     *
     * @param User $user
     *
     * @return bool
     */
    public function accessAdmin(User $user): bool
    {
        if ($user->hasAnyRole(['coordinator', 'super-user', 'coach', 'cooperation-admin'])) {
            return true;
        }

        return false;
    }


    /**
     * Check if a user is authorized to delete a user.
     *
     * @param User $user
     * @param User $userToDelete
     * @return bool
     */
    public function deleteUser(User $user, User $userToDelete): bool
    {
        if ($user->hasRoleAndIsCurrentRole(['super-admin', 'cooperation-admin']) && $userToDelete->id != $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if a user is authorize to delete his own account
     *
     * @param  User  $user
     *
     * @return bool
     */
    public function deleteOwnAccount(User $user)
    {
        if ($user->hasRole(['cooperation-admin'])) {
            return false;
        }
        return true;
    }

    /**
     * Check if a user is authorized to destroy a user.
     *
     * @param User $user
     * @param User $userToDestroy
     *
     * @return bool
     */
    public function destroy(User $user, User $userToDestroy)
    {
        // check if the user can delete a user, and if the user to be destroyed is a member of the user his cooperation
        // remove the cooperations stuff
        if ($user->can('delete-user', $userToDestroy) && $userToDestroy->cooperation->id == HoomdossierSession::getCooperation()) {
            return true;
        }

        return false;
    }

    /**
     * Check if a user is allowed to participate in a group chat or not.
     *
     * @param User $user
     * @param $buildingId
     *
     * @return bool
     */
    public function participateInGroupChat(User $user, $buildingId): bool
    {
        // if the user is a coach and has a active building coach status, return true
        if ($user->hasRole('coach') && $user->isNotRemovedFromBuildingCoachStatus($buildingId)) {
            return true;
        } elseif ($user->hasRole('resident') && HoomdossierSession::getBuilding() == $buildingId) {
            return true;
        }

        return false;
    }

    /**
     * Check if a user can remove a participant from the group chat.
     *
     * @param User $user             | Auth user
     * @param User $groupParticipant | Participant from the group chat
     *
     * @return bool
     */
    public function removeParticipantFromChat(User $user, User $groupParticipant): bool
    {
        // a coordinator and resident can remove a coach from a conversation
        // also check if the current building id is from the $groupParticipant, cause ifso we cant remove him because he is the building owner
        if ($user->hasRoleAndIsCurrentRole(['resident', 'coordinator', 'cooperation-admin']) && $groupParticipant->hasRole(['coach'])) {
            return true;
        }

        return false;
    }


    /**
     * Returns if a user can assign a particular role (just if the user is
     * allowed to assign roles).
     *
     * @param  User  $user
     * @param  Role  $role The role which is to be assigned
     *
     * @return bool
     */
    public function assignRole(User $user, Role $role)
    {
        if ($user->hasRoleAndIsCurrentRole('super-admin')){
            return true;
        }
        if ($user->hasRoleAndIsCurrentRole('cooperation-admin')){
            return in_array($role->name, ['coordinator', 'coach', 'resident']);
        }
        if ($user->hasRoleAndIsCurrentRole('coordinator')){
            return in_array($role->name, ['coach', 'resident']);
        }
        return false;
    }

    /**
     * Returns if a user can assign a particular role to another user.
     * This checks the cooperation, and the role of the 'giving' user.
     *
     * @param  User  $user
     * @param  Role  $role The role which is to be assigned
     * @param  User  $toUser The user who should get the role
     *
     * @return bool
     */
    public function assignRoleToUser(User $user, Role $role, User $toUser)
    {
        \Log::debug(__METHOD__ . " cooperation check: " . $user->cooperation_id . " vs " . $toUser->cooperation_id);
        if ($user->cooperation_id !== $toUser->cooperation_id){
            return false;
        }

        return $this->assignRole($user, $role);
    }


}
