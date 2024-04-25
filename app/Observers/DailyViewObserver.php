<?php

namespace App\Observers;

use App\Models\DailyView;

class DailyViewObserver
{
    /**
     * Handle the DailyView "creating" event.
     */
    public function creating(DailyView $dailyView): void
    {
        if (! empty($dailyView->session_id)) {
            return;
        }

        $dailyView->session_id = session()->getId();
    }
}
