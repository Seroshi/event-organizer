<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EventPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Event $event): bool
    {
        // Superadmin/Admins can delete anything
        if (auth()->check()) {
            return true;
        }

        // Deny everyone else
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Event $event): bool
    {
        // Superadmin/Admins can update anything
        if ($user->role->accessLevel() === 'full-access') {
            return true;
        }

        // Organizers can only update if they own the event
        if ($user->role->accessLevel() === 'creator-access') {
            return $user->id === $event->user_id; 
        }

        // Deny everyone else
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Event $event): bool
    {
        // Superadmin/Admins can delete anything
        if ($user->role->accessLevel() === 'full-access') {
            return true;
        }

        // Organizers can only delete if they own the event
        if ($user->role->accessLevel() === 'creator-access') {
            return $user->id === $event->user_id; 
        }

        // Deny everyone else
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Event $event): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Event $event): bool
    {
        return false;
    }
}
