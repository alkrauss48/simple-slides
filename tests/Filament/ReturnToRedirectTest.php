<?php

use App\Filament\Pages\Auth\Login;
use App\Models\User;

use function Pest\Livewire\livewire;

it('sends the user to a same-origin returnTo after logging in', function () {
    $user = User::factory()->create();
    $returnTo = route('invitations.accept', 'some-token');

    // mount() stashes returnTo as the intended URL, which Filament's
    // LoginResponse then consumes via redirect()->intended().
    $this->get(route('filament.admin.auth.login', ['returnTo' => $returnTo]))
        ->assertSuccessful();

    livewire(Login::class)
        ->fillForm([
            'email' => $user->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertRedirect($returnTo);
});

it('ignores an off-site returnTo', function () {
    $user = User::factory()->create();

    $this->get(route('filament.admin.auth.login', ['returnTo' => 'https://evil.example.com/steal']))
        ->assertSuccessful();

    livewire(Login::class)
        ->fillForm([
            'email' => $user->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        // Falls back to the panel rather than honouring the off-site URL.
        ->assertRedirect(filament()->getUrl());
});

it('falls back to the panel when no returnTo is given', function () {
    $user = User::factory()->create();

    $this->get(route('filament.admin.auth.login'))
        ->assertSuccessful();

    livewire(Login::class)
        ->fillForm([
            'email' => $user->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertRedirect(filament()->getUrl());
});
