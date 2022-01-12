<?php

namespace App\Policies;

use App\Models\Repository;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RepositoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->hasTeamPermission($user->team, 'repository:read');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Repository  $repository
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Repository $repository)
    {
        dd('hello');
        return $user->hasTeamPermission($repository->team, 'repository:read');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasTeamPermission($user->team, 'create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Repository  $repository
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Repository $repository)
    {
        return $user->hasTeamPermission($repository->team, 'update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Repository  $repository
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Repository $repository)
    {
        return $user->hasTeamPermission($repository->team, 'delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Repository  $repository
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Repository $repository)
    {
        return $user->hasTeamPermission($repository->team, 'restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Repository  $repository
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Repository $repository)
    {
        return $user->hasTeamPermission($repository->team, 'forceDelete');
    }
}
