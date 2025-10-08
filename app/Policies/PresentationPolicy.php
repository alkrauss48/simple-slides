<?php

namespace App\Policies;

use App\Enums\InviteStatus;
use App\Models\Presentation;
use App\Models\User;

class PresentationPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdministrator()) {
            return true;
        }

        return null;
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
    public function view(User $user, Presentation $presentation): bool
    {
        return $this->isOwnerOrSharedUser($user, $presentation);
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
    public function update(User $user, Presentation $presentation): bool
    {
        return $this->isOwnerOrSharedUser($user, $presentation);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Presentation $presentation): bool
    {
        // Only the owner can delete the presentation
        return $presentation->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Presentation $presentation): bool
    {
        // Only the owner can restore the presentation
        return $presentation->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Presentation $presentation): bool
    {
        // Only the owner can permanently delete the presentation
        return $presentation->user_id === $user->id;
    }

    /**
     * Check if the user is the owner or a shared user with accepted invitation.
     */
    private function isOwnerOrSharedUser(User $user, Presentation $presentation): bool
    {
        // Check if the user is the owner
        if ($presentation->user_id === $user->id) {
            return true;
        }

        // Check if the user is a shared user with an accepted invitation
        return $presentation->presentationUsers()
            ->where('user_id', $user->id)
            ->where('invite_status', InviteStatus::ACCEPTED)
            ->exists();
    }
}
