<?php

namespace App\Observers;

use App\Models\PresentationUser;
use Illuminate\Support\Facades\Notification;

class PresentationUserObserver
{
    /**
     * Handle the PresentationUser "created" event.
     */
    public function created(PresentationUser $presentationUser): void
    {
        // Send invitation email (on-demand if user doesn't exist)
        if ($presentationUser->user_id) {
            $presentationUser->user->notify(new \App\Notifications\PresentationUserCreated($presentationUser));
        } else {
            Notification::route('mail', $presentationUser->email)
                ->notify(new \App\Notifications\PresentationUserCreated($presentationUser));
        }
    }

    /**
     * Handle the PresentationUser "updated" event.
     */
    public function updated(PresentationUser $presentationUser): void
    {
        //
    }

    /**
     * Handle the PresentationUser "deleted" event.
     */
    public function deleted(PresentationUser $presentationUser): void
    {
        //
    }

    /**
     * Handle the PresentationUser "restored" event.
     */
    public function restored(PresentationUser $presentationUser): void
    {
        //
    }

    /**
     * Handle the PresentationUser "force deleted" event.
     */
    public function forceDeleted(PresentationUser $presentationUser): void
    {
        //
    }
}
