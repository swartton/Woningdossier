<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

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
        //
    }

    public function accessAdmin(User $user)
    {
        if ($user->hasAnyRole(['coordinator', 'super-user', 'coach', 'cooperation-admin'])) {
            return true;
        }
        return false;
    }
}
