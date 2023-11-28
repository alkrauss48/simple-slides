<?php

use App\Models\User;

test('user can upload if they are below their max storage space', function () {
    $user = User::factory()->create();

    expect($user->can('upload', User::class))->toBeTrue();
});

test('user can not upload if they are at their max storage space', function () {
    $user = User::factory()->create([
        'image_uploaded_size' => config('app-upload.limit'),
    ]);

    expect($user->can('upload', User::class))->toBeFalse();
});

test('user can not upload if they are above their max storage space', function () {
    $user = User::factory()->create([
        'image_uploaded_size' => (config('app-upload.limit') + 10),
    ]);

    expect($user->can('upload', User::class))->toBeFalse();
});

test('admin users can upload even if they are above their max storage space', function () {
    $user = User::factory()->create([
        'is_admin' => true,
        'image_uploaded_size' => (config('app-upload.limit') + 10),
    ]);

    expect($user->can('upload', User::class))->toBeTrue();
});
