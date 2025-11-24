<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PermissionPolicy
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
    public function view(User $user, Permission $permission): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->hasRole('admin') ? Response::allow() : Response::deny('You can\'t update permissions.',403);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Permission $permission): Response
    {
        return $user->hasRole('admin') ?  Response::allow() : Response::deny('You can\'t update permissions.',403);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Permission $permission): Response
    {
        return $user->hasRole('admin') ? Response::allow() : Response::deny('You can\'t delete permissions.', 403);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Permission $permission): Response
    {
        return $user->hasRole('admin') ? Response::allow() : Response::deny('You can\'t restore permissions', 403);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Permission $permission): Response
    {
        return $user->hasRole('admin') ? Response::allow() : Response::deny('You can\'t delete permissions permanently', 403);
    }
}
