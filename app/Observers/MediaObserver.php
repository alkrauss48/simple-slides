<?php

namespace App\Observers;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaObserver
{
    /**
     * Handle the Media "created" event.
     */
    public function created(Media $media): void
    {
        if (! auth()->check()) {
            return;
        }

        auth()->user()->modifyImageUploadedSize($media->size);
    }

    /**
     * Handle the Media "deleted" event.
     */
    public function deleted(Media $media): void
    {
        if (! auth()->check()) {
            return;
        }

        auth()->user()->modifyImageUploadedSize($media->size * -1);
    }
}
