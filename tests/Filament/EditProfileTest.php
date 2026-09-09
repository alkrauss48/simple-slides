<?php

use App\Filament\Pages\Auth\EditProfile;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->actingAs($this->user);
});

it('can render the profile page', function () {
    livewire(EditProfile::class)
        ->assertStatus(200);
});

it('loads initial data', function () {
    livewire(EditProfile::class)
        ->assertFormSet([
            'name' => $this->user->name,
            'email' => $this->user->email,
            'username' => $this->user->username,
        ]);
});

it('updates the username', function () {
    livewire(EditProfile::class)
        ->fillForm(['username' => 'new-username'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->user->refresh())
        ->username->toBe('new-username');
});

it('rejects a username that is already taken', function () {
    $other = User::factory()->create();

    livewire(EditProfile::class)
        ->fillForm(['username' => $other->username])
        ->call('save')
        ->assertHasFormErrors(['username']);

    expect($this->user->refresh())
        ->username->not->toBe($other->username);
});

it('rejects a username with invalid characters', function () {
    livewire(EditProfile::class)
        ->fillForm(['username' => 'Not A Username'])
        ->call('save')
        ->assertHasFormErrors(['username']);
});

it('is reachable through the panel', function () {
    $this->get(EditProfile::getUrl())
        ->assertSuccessful();
});
