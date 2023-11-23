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
        if (auth()->check() && $presentation->user_id === null) {
            $presentation->user_id = intval(auth()->id());
        }
    }
}
