<?php

use App\Models\User;
use Filament\Notifications\Auth\ResetPassword as ResetPasswordNotification;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use Filament\Pages\Auth\PasswordReset\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

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

    Notification::assertSentTo($user, ResetPasswordNotification::class);
});

test('reset password form displays with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    livewire(RequestPasswordReset::class)
        ->set('data.email', $user->email)
        ->call('request');

    Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) {
        $response = $this->get($notification->url);

        $response->assertSuccessful();

        return true;
    });
});

test('password can be reset through filament form', function () {
    Notification::fake();

    $user = User::factory()->create();
    $newPassword = 'new-password';

    livewire(RequestPasswordReset::class)
        ->set('data.email', $user->email)
        ->call('request');

    Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use ($newPassword) {
        Livewire::withQueryParams([
            'token' => str($notification->url)->explode('token=')[1],
        ])
            ->test(ResetPassword::class)
            ->set([
                'password' => $newPassword,
                'passwordConfirmation' => $newPassword,
            ])
            ->call('resetPassword');

        return true;
    });
});
