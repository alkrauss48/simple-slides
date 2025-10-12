<?php

use App\Http\Controllers\AdhocSlidesController;
use App\Http\Controllers\PresentationController;
use App\Http\Controllers\SettingsController;
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

Route::get('/{slides}', [AdhocSlidesController::class, 'show'])
    ->name('adhoc-slides.show');
