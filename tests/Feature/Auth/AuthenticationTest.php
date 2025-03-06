<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Filament\Pages\Auth\Login;

use function Pest\Livewire\livewire;

test('login screen can be rendered', function () {
    $response = $this->get(route('filament.admin.auth.login'));

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    livewire(Login::class)
        ->set('data.email', $user->email)
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertHasNoErrors()
        ->assertRedirect(RouteServiceProvider::HOME);

    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    livewire(Login::class)
        ->set('data.email', $user->email)
        ->set('data.password', 'wrong-password')
        ->call('authenticate')
        ->assertHasErrors();

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('filament.admin.auth.logout'));

    $this->assertGuest();
    $response->assertRedirect(route('filament.admin.auth.login'));
});
