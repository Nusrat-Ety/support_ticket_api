<?php

namespace App\Policies\V1;

use App\Models\User;
use App\Permissions\V1\Abilities;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Check if the user can store (create) a user.
     *
     * @param  User  $user  The authenticated user
     * @return bool  Whether the user can store a user
     */
    public function store(User $user)
    {
        return $user->tokenCan(Abilities::CreateUser);
    }

    /**
     * Check if the user can replace a user.
     *
     * @param  User  $user  The authenticated user
     * @return bool  Whether the user can replace a user
     */
    public function replace(User $user)
    {
        return $user->tokenCan(Abilities::ReplaceUser); 
    }

    /**
     * Check if the user can update a user.
     *
     * @param  User    $user    The authenticated user
     * @return bool  Whether the user can update the user
     */
    public function update(User $user)
    {
        return $user->tokenCan(Abilities::UpdateUser);
    }

    /**
     * Check if the user can delete a user.
     *
     * @param  User    $user    The authenticated user
     * @return bool  Whether the user can delete the user
     */
    public function delete(User $user)
    {
        return $user->tokenCan(Abilities::DeleteUser);
    }
}
