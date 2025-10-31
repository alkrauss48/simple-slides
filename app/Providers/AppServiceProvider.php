<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
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
            $menuItems = [];

            if (Auth::check() && Auth::user()?->username) {
                $menuItems[] = UserMenuItem::make()
                    ->label('Public Profile')
                    ->url(
                        route('profile.show', ['user' => Auth::user()->username]),
                        shouldOpenInNewTab: true
                    )->icon('heroicon-s-user');
            }

            $menuItems[] = UserMenuItem::make()
                ->label('Helpful Videos')
                ->url(
                    'https://www.youtube.com/playlist?list=PLWXp2X5PBDOkzYGV3xd0zviD6xR8OoiFR',
                    shouldOpenInNewTab: true
                )->icon('heroicon-s-play-circle');

            Filament::registerUserMenuItems($menuItems);
        });
    }
}
