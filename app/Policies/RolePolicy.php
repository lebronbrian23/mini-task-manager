<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->hasRole('admin') ? Response::allow() : Response::deny('You can\'t update roles.',403);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): Response
    {
        return $user->hasRole('admin') ?  Response::allow() : Response::deny('You can\'t update roles.',403);
    }

    /** Assign role to user
     * @param User $user
     * @param Role $role
     * @return Response
     */
    public function assign(User $user, Role $role): Response
    {
        return $user->hasRole('admin') ?  Response::allow() : Response::deny('You can\'t assign roles.',403);
    }

    /**
     * Remove role from user
     * @param User $user
     * @param Role $role
     * @return Response
     */
    public function removeRole(User $user, Role $role): Response
    {
        return $user->hasRole('admin') ?  Response::allow() : Response::deny('You can\'t remove roles.',403);
    }


    /**
     * Determine whether the user can update the model.
     */
    public function attachPermission(User $user, Role $role): Response
    {
        return $user->hasRole('admin') ?  Response::allow() : Response::deny('You can\'t attach permissions to a role.',403);
    }

    public function detachPermission(User $user, Role $role): Response
    {
        return $user->hasRole('admin') ?  Response::allow() : Response::deny('You can\'t detach permissions from a role.',403);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): Response
    {
        return $user->hasRole('admin') ? Response::allow() : Response::deny('You can\'t delete roles.', 403);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Role $role): Response
    {
        return $user->hasRole('admin') ? Response::allow() : Response::deny('You can\'t restore roles', 403);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Role $role): Response
    {
        return $user->hasRole('admin') ? Response::allow() : Response::deny('You can\'t delete roles permanently', 403);
    }
}
