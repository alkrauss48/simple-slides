<?php

use App\Enums\InviteStatus;
use App\Models\Presentation;
use App\Models\PresentationUser;
use App\Models\User;

test('admin can view any presentation', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $user->id]);

    expect($admin->can('view', $presentation))->toBeTrue();
});

test('owner can view their own presentation', function () {
    $user = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $user->id]);

    expect($user->can('view', $presentation))->toBeTrue();
});

test('shared user with accepted invitation can view presentation', function () {
    $owner = User::factory()->create();
    $sharedUser = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);

    PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $sharedUser->id,
        'email' => $sharedUser->email,
        'invite_status' => InviteStatus::ACCEPTED,
        'accepted_at' => now(),
    ]);

    expect($sharedUser->can('view', $presentation))->toBeTrue();
});

test('user with pending invitation cannot view presentation', function () {
    $owner = User::factory()->create();
    $invitedUser = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);

    PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $invitedUser->id,
        'email' => $invitedUser->email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    expect($invitedUser->can('view', $presentation))->toBeFalse();
});

test('user with rejected invitation cannot view presentation', function () {
    $owner = User::factory()->create();
    $rejectedUser = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);

    PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $rejectedUser->id,
        'email' => $rejectedUser->email,
        'invite_status' => InviteStatus::REJECTED,
    ]);

    expect($rejectedUser->can('view', $presentation))->toBeFalse();
});

test('unrelated user cannot view presentation', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);

    expect($otherUser->can('view', $presentation))->toBeFalse();
});

test('admin can update any presentation', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $user->id]);

    expect($admin->can('update', $presentation))->toBeTrue();
});

test('owner can update their own presentation', function () {
    $user = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $user->id]);

    expect($user->can('update', $presentation))->toBeTrue();
});

test('shared user with accepted invitation can update presentation', function () {
    $owner = User::factory()->create();
    $sharedUser = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);

    PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $sharedUser->id,
        'email' => $sharedUser->email,
        'invite_status' => InviteStatus::ACCEPTED,
        'accepted_at' => now(),
    ]);

    expect($sharedUser->can('update', $presentation))->toBeTrue();
});

test('user with pending invitation cannot update presentation', function () {
    $owner = User::factory()->create();
    $invitedUser = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);

    PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $invitedUser->id,
        'email' => $invitedUser->email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    expect($invitedUser->can('update', $presentation))->toBeFalse();
});

test('unrelated user cannot update presentation', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);

    expect($otherUser->can('update', $presentation))->toBeFalse();
});

test('admin can delete any presentation', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $user->id]);

    expect($admin->can('delete', $presentation))->toBeTrue();
});

test('owner can delete their own presentation', function () {
    $user = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $user->id]);

    expect($user->can('delete', $presentation))->toBeTrue();
});

test('shared user with accepted invitation cannot delete presentation', function () {
    $owner = User::factory()->create();
    $sharedUser = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);

    PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $sharedUser->id,
        'email' => $sharedUser->email,
        'invite_status' => InviteStatus::ACCEPTED,
        'accepted_at' => now(),
    ]);

    expect($sharedUser->can('delete', $presentation))->toBeFalse();
});

test('unrelated user cannot delete presentation', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);

    expect($otherUser->can('delete', $presentation))->toBeFalse();
});

test('admin can restore any presentation', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $user->id]);

    expect($admin->can('restore', $presentation))->toBeTrue();
});

test('owner can restore their own presentation', function () {
    $user = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $user->id]);

    expect($user->can('restore', $presentation))->toBeTrue();
});

test('shared user with accepted invitation cannot restore presentation', function () {
    $owner = User::factory()->create();
    $sharedUser = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);

    PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $sharedUser->id,
        'email' => $sharedUser->email,
        'invite_status' => InviteStatus::ACCEPTED,
        'accepted_at' => now(),
    ]);

    expect($sharedUser->can('restore', $presentation))->toBeFalse();
});

test('unrelated user cannot restore presentation', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);

    expect($otherUser->can('restore', $presentation))->toBeFalse();
});

test('admin can force delete any presentation', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $user->id]);

    expect($admin->can('forceDelete', $presentation))->toBeTrue();
});

test('owner can force delete their own presentation', function () {
    $user = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $user->id]);

    expect($user->can('forceDelete', $presentation))->toBeTrue();
});

test('shared user with accepted invitation cannot force delete presentation', function () {
    $owner = User::factory()->create();
    $sharedUser = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);

    PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $sharedUser->id,
        'email' => $sharedUser->email,
        'invite_status' => InviteStatus::ACCEPTED,
        'accepted_at' => now(),
    ]);

    expect($sharedUser->can('forceDelete', $presentation))->toBeFalse();
});

test('unrelated user cannot force delete presentation', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);

    expect($otherUser->can('forceDelete', $presentation))->toBeFalse();
});

test('any authenticated user can view any presentations list', function () {
    $user = User::factory()->create();

    expect($user->can('viewAny', Presentation::class))->toBeTrue();
});

test('any authenticated user can create presentations', function () {
    $user = User::factory()->create();

    expect($user->can('create', Presentation::class))->toBeTrue();
});
