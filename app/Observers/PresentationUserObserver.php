<?php

namespace App\Observers;

use App\Models\PresentationUser;

class PresentationUserObserver
{
    /**
     * Handle the PresentationUser "created" event.
     */
    public function created(PresentationUser $presentationUser): void
    {
        //
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
