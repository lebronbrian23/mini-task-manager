<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Admins are allowed to do anything
     * @param User $user
     * @param $ability
     * @return true|void
     */
    public function before(User $user, $ability)
    {
        if ( $user->hasRole('admin') ?? false) {
            return true;
        }
    }
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
    public function view(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): Response
    {
        return $user->id === $task->user_id ? Response::allow() : Response::deny('You can\'t update task.',403);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): Response
    {
        return $user->id === $task->user_id ? Response::allow() : Response::deny('You can\'t delete task.',403);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): Response
    {
        return $user->id === $task->user_id ? Response::allow() : Response::deny('You can\'t restore task.',403);

    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): Response
    {
        return $user->id === $task->user_id ? Response::allow() : Response::deny('You can\'t force detele a task.',403);
    }
}
