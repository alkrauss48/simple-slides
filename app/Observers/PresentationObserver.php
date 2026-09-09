<?php

namespace App\Observers;

use App\Models\Presentation;

class PresentationObserver
{
    /**
     * Handle the Presentation "creating" event.
     */
    public function creating(Presentation $presentation): void
    {
        if (auth()->check() && ! isset($presentation->user_id)) {
            $presentation->user()->associate(auth()->user());
        }
    }
}
