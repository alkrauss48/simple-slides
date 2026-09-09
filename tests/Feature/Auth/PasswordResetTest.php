<?php

use App\Models\User;
use Filament\Auth\Notifications\ResetPassword;
use Filament\Auth\Pages\PasswordReset\RequestPasswordReset;
use Illuminate\Support\Facades\Notification;

use function Pest\Livewire\livewire;

test('reset password request page loads correctly', function () {
    $response = $this->get(route('filament.admin.auth.password-reset.request'));

    $response->assertSuccessful();
});

test('password reset request can be initiated', function () {
    Notification::fake();

    $user = User::factory()->create();

    livewire(RequestPasswordReset::class)
        ->set('data.email', $user->email)
        ->call('request');

    Notification::assertSentTo($user, ResetPassword::class);
});

test('reset password form displays with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    livewire(RequestPasswordReset::class)
        ->set('data.email', $user->email)
        ->call('request');

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $response = $this->get($notification->url);

        $response->assertSuccessful();

        return true;
    });
});
