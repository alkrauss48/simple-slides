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
        if (auth()->check() && ! isset($imageUpload->user_id)) {
            $imageUpload->user()->associate(auth()->user());
        }
    }
}
