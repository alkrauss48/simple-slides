<?php

namespace App\Providers;

use App\Observers\MediaObserver;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Prevent wrapping of resources in `data` key.
        JsonResource::withoutWrapping();

        // The app's own models declare their observers with #[ObservedBy].
        // Media belongs to Spatie, so it has to be registered here.
        Media::observe(MediaObserver::class);
    }
}
