<?php

use App\Models\Presentation;
use App\Models\User;

test('Presentations automatically set user to the auth user on create', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $presentation = Presentation::factory()->create([
        'user_id' => null,
    ]);

    expect($presentation)
        ->user_id->toBe($user->id);
});

test('Presentations keep their original user if it exists on create', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $presentation = Presentation::factory()->create();

    expect($presentation)
        ->user_id->not->toBe($user->id);
});

test('Presentations will fail if missing a user_id, with no authenticated user', function () {
    expect(fn () => Presentation::factory()->create(['user_id' => null]))
        ->toThrow(Exception::class);
});
