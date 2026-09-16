<?php

use App\Filament\Pages\Auth\Register;
use App\Models\User;

use function Pest\Livewire\livewire;

test('registration screen can be rendered', function () {
    // Not '/register' — that is not a route, and the catch-all /{user} pattern
    // swallows it into the ad-hoc slides page, which also answers 200.
    $response = $this->get(route('filament.admin.auth.register'));

    $response->assertStatus(200);
});

test('new users can register', function () {
    livewire(Register::class)
        ->set('data.name', 'New User')
        ->set('data.email', 'newuser@example.com')
        ->set('data.username', 'new-user')
        ->set('data.password', 'password123')
        ->set('data.passwordConfirmation', 'password123')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(filament()->getUrl());

    expect(User::where('email', 'newuser@example.com')->exists())->toBeTrue();

    $this->assertAuthenticated();
});

test('users must register with a username', function () {
    livewire(Register::class)
        ->set('data.name', 'New User')
        ->set('data.email', 'newuser@example.com')
        ->set('data.password', 'password123')
        ->set('data.passwordConfirmation', 'password123')
        ->call('register')
        ->assertHasErrors(['data.username']);

    $this->assertGuest();
});
