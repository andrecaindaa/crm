<?php

namespace App\Policies;

use App\Models\Entity;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EntityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Entity $entity): bool
    {
        return $entity->user_id === $user->id;
    }

    public function update(User $user, Entity $entity): bool
    {
        return $entity->user_id === $user->id;
    }

    public function delete(User $user, Entity $entity): bool
    {
        return $entity->user_id === $user->id;
    }



    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Entity $entity): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Entity $entity): bool
    {
        return false;
    }
}
