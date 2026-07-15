<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\Team;
use App\Models\User;

class NotePolicy
{
    /**
     * Determine whether the user can view any notes for the team.
     */
    public function viewAny(User $user, Team $team): bool
    {
        return $team->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can view the note.
     */
    public function view(User $user, Note $note, Team $team): bool
    {
        return $team->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create a note for the team.
     */
    public function create(User $user, Team $team): bool
    {
        return $team->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can update the note.
     */
    public function update(User $user, Note $note, Team $team): bool
    {
        // Author or team admin/owner can update
        if ($note->user_id === $user->id) {
            return true;
        }

        $membership = $team->members()
            ->where('user_id', $user->id)
            ->first();

        if (! $membership) {
            return false;
        }

        return in_array($membership->pivot->role->value, ['owner', 'admin']);
    }

    /**
     * Determine whether the user can delete the note.
     */
    public function delete(User $user, Note $note, Team $team): bool
    {
        // Author or team admin/owner can delete
        if ($note->user_id === $user->id) {
            return true;
        }

        $membership = $team->members()
            ->where('user_id', $user->id)
            ->first();

        if (! $membership) {
            return false;
        }

        return in_array($membership->pivot->role->value, ['owner', 'admin']);
    }
}
