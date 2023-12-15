<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

test('user image uploaded size increases on media creation', function () {
    $user = User::factory()
        ->hasPresentations(1)
        ->create();

    $this->actingAs($user);

    expect($user)->image_uploaded_size->toBe(0);

    $user
        ->presentations
        ->first()
        ->addMedia(UploadedFile::fake()->image('avatar.jpg'))
        ->toMediaCollection('thumbnail');

    expect($user)->image_uploaded_size->not->toBe(0);
    expect($user)->image_uploaded_size->toBe(Media::first()->size);
});

test('user image uploaded size increases on media deletion', function () {
    $user = User::factory()
        ->hasPresentations(1)
        ->create();

    $this->actingAs($user);

    $user
        ->presentations
        ->first()
        ->addMedia(UploadedFile::fake()->image('avatar.jpg'))
        ->toMediaCollection('thumbnail');

    expect($user)->image_uploaded_size->not->toBe(0);

    Media::first()->delete();

    expect($user)->image_uploaded_size->toBe(0);
});
