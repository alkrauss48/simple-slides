<?php

use App\Models\ImageUpload;
use App\Models\Presentation;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

test('isAdministrator returns true for admins', function () {
    $user = User::factory()->create(['is_admin' => true]);

    expect($user->isAdministrator())->toBeTrue();
});

test('isAdministrator returns false for non-admins', function () {
    $user = User::factory()->create(['is_admin' => false]);

    expect($user->isAdministrator())->toBeFalse();
});

test('modifyImageUploadedSize can increase a user\'s uploaded size', function () {
    $user = User::factory()->create([
        'image_uploaded_size' => 0,
    ]);

    $user->modifyImageUploadedSize(10);

    expect($user)
        ->image_uploaded_size->toBe(10);
});

test('modifyImageUploadedSize can decrease a user\'s uploaded size', function () {
    $user = User::factory()->create([
        'image_uploaded_size' => 20,
    ]);

    $user->modifyImageUploadedSize(-10);

    expect($user)
        ->image_uploaded_size->toBe(10);
});

test('modifyImageUploadedSize can never decrease a user\'s uploaded size below 0', function () {
    $user = User::factory()->create([
        'image_uploaded_size' => 20,
    ]);

    $user->modifyImageUploadedSize(-100);

    expect($user)
        ->image_uploaded_size->toBe(0);
});

describe('users with no image uploads', function () {
    test('regenerateImageUploadedSize resets a user\'s image size', function () {
        $user = User::factory()->create([
            'image_uploaded_size' => 200,
        ]);

        $user->regenerateImageUploadedSize();

        expect($user)
            ->image_uploaded_size->toBe(0);
    });
});

describe('users with image uploads', function () {
    beforeEach(function () {
        $this->user = User::factory()
            ->hasPresentations(2)
            ->hasImageUploads(2)
            ->create();

        Presentation::each(function (Presentation $record) {
            $record
                ->addMedia(UploadedFile::fake()->image('avatar.jpg'))
                ->toMediaCollection('thumbnail');
        });

        ImageUpload::each(function (ImageUpload $record) {
            $record
                ->addMedia(UploadedFile::fake()->image('foo.png'))
                ->toMediaCollection('image');
        });
    });

    test('regenerateImageUploadedSize resets a user\'s image size', function () {
        expect($this->user)->image_uploaded_size->toBe(0);

        $this->user->regenerateImageUploadedSize();

        expect($this->user)
            ->image_uploaded_size->toBe(Media::sum('size'))
            ->image_uploaded_size->not->toBe(0);
    });
});
