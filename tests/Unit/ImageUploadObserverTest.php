<?php

use App\Models\ImageUpload;
use App\Models\User;

test('ImageUploads automatically set user to the auth user on create', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $imageUpload = ImageUpload::factory()->create([
        'user_id' => null,
    ]);

    expect($imageUpload)
        ->user_id->toBe($user->id);
});

test('ImageUploads keep their original user if it exists on create', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $imageUpload = ImageUpload::factory()->create();

    expect($imageUpload)
        ->user_id->not->toBe($user->id);
});

test('ImageUploads will fail if missing a user_id, with no authenticated user', function () {
    expect(fn () => ImageUpload::factory()->create(['user_id' => null]))
        ->toThrow(Exception::class);
});
