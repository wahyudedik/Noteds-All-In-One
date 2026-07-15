<?php

namespace App\Policies;

use App\Models\Label;
use App\Models\Team;
use App\Models\User;

class LabelPolicy
{
    /**
     * Determine whether the user can view any labels for the team.
     */
    public function viewAny(User $user, Team $team): bool
    {
        return $team->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create a label for the team.
     */
    public function create(User $user, Team $team): bool
    {
        return $team->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can update the label.
     */
    public function update(User $user, Label $label, Team $team): bool
    {
        return $team->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can delete the label.
     */
    public function delete(User $user, Label $label, Team $team): bool
    {
        return $team->members()->where('user_id', $user->id)->exists();
    }
}
