<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\Team;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Determine whether the user can view any categories for the team.
     */
    public function viewAny(User $user, Team $team): bool
    {
        return $team->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create a category for the team.
     */
    public function create(User $user, Team $team): bool
    {
        return $team->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can update the category.
     */
    public function update(User $user, Category $category, Team $team): bool
    {
        return $team->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can delete the category.
     */
    public function delete(User $user, Category $category, Team $team): bool
    {
        return $team->members()->where('user_id', $user->id)->exists();
    }
}
