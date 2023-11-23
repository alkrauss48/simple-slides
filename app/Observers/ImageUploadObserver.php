<?php

namespace App\Observers;

use App\Models\ImageUpload;

class ImageUploadObserver
{
    /**
     * Handle the ImageUpload "creating" event.
     */
    public function creating(ImageUpload $imageUpload): void
    {
        if (auth()->check() && $imageUpload->user_id === null) {
            $imageUpload->user_id = intval(auth()->id());
        }
    }
}
