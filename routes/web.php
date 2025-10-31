<?php

use App\Http\Controllers\AdhocSlidesController;
use App\Http\Controllers\PresentationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [AdhocSlidesController::class, 'index'])->name('home');
Route::get('/settings', [SettingsController::class, 'index'])->name('settings');

Route::get('/privacy', function (): Response {
    return Inertia::render('Privacy');
});

// Invitation routes (must be before catch-all routes)
Route::get('/invitations/{token}', [App\Http\Controllers\InvitationController::class, 'show'])
    ->name('invitations.show');
Route::get('/invitations/{token}/accept', [App\Http\Controllers\InvitationController::class, 'accept'])
    ->name('invitations.accept');

Route::get('/{user:username}/{slug}', [PresentationController::class, 'show'])
    ->name('presentations.show');

// Handle both profile and adhoc slides routes with the same logic
// Check if identifier is a username first, otherwise treat as base64 string
$handleProfileOrSlides = function (string $value) {
    // First, check if it's a valid username that exists
    $user = User::where('username', $value)->first();

    if ($user) {
        return app(ProfileController::class)->show(request(), $user);
    }

    // If not a username, treat it as potential adhoc slides (base64 string)
    return app(AdhocSlidesController::class)->show($value);
};

// Register with both route names for backward compatibility
// Both routes share the same URL pattern but use different parameter names for route() helper compatibility
Route::get('/{user}', $handleProfileOrSlides)
    ->where('user', '.*') // Allow any characters (for base64 strings)
    ->name('profile.show');
Route::get('/{slides}', $handleProfileOrSlides)
    ->where('slides', '.*')
    ->name('adhoc-slides.show');
