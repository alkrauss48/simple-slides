<?php

use App\Livewire\UsernameComponent;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->actingAs($this->user);
});

it('can render component', function () {
    livewire(UsernameComponent::class)
        ->assertStatus(200);
});

it('loads initial data', function () {
    livewire(UsernameComponent::class)
        ->assertViewHas('data.username', $this->user->username);
});

it('updates the username', function () {
    livewire(UsernameComponent::class)
        ->set('data.username', 'new-username')
        ->call('submit');

    expect($this->user->refresh())
        ->username->toBe('new-username');
});
