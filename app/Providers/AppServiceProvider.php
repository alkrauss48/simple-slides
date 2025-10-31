<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

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

        Filament::serving(function () {
            Filament::registerUserMenuItems([
                UserMenuItem::make()
                    ->label('Public Profile')
                    ->url(
                        route('profile.show', ['user' => auth()->user()?->username]),
                        shouldOpenInNewTab: true
                    )->icon('heroicon-s-user'),
                UserMenuItem::make()
                    ->label('Helpful Videos')
                    ->url(
                        'https://www.youtube.com/playlist?list=PLWXp2X5PBDOkzYGV3xd0zviD6xR8OoiFR',
                        shouldOpenInNewTab: true
                    )->icon('heroicon-s-play-circle'),
            ]);
        });
    }
}
