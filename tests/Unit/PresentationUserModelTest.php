<?php

use App\Enums\InviteStatus;
use App\Models\Presentation;
use App\Models\PresentationUser;
use App\Models\User;

test('PresentationUser automatically generates invite token on create', function () {
    $presentation = Presentation::factory()->create();
    $user = User::factory()->create();

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $user->id,
        'email' => $user->email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    expect($presentationUser)
        ->invite_token->not->toBeNull()
        ->invite_token->toHaveLength(32);
});

test('PresentationUser can use custom invite token', function () {
    $presentation = Presentation::factory()->create();
    $user = User::factory()->create();
    $customToken = 'custom-token-12345678901234567890';

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $user->id,
        'email' => $user->email,
        'invite_status' => InviteStatus::PENDING,
        'invite_token' => $customToken,
    ]);

    expect($presentationUser)
        ->invite_token->toBe($customToken);
});

test('PresentationUser belongs to a user', function () {
    $presentation = Presentation::factory()->create();
    $user = User::factory()->create();

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $user->id,
        'email' => $user->email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    expect($presentationUser->user)
        ->toBeInstanceOf(User::class)
        ->id->toBe($user->id);
});

test('PresentationUser belongs to a presentation', function () {
    $presentation = Presentation::factory()->create();
    $user = User::factory()->create();

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $user->id,
        'email' => $user->email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    expect($presentationUser->presentation)
        ->toBeInstanceOf(Presentation::class)
        ->id->toBe($presentation->id);
});

test('PresentationUser can be created without user_id for unregistered users', function () {
    $presentation = Presentation::factory()->create();
    $email = 'unregistered@example.com';

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => null,
        'email' => $email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    expect($presentationUser)
        ->user_id->toBeNull()
        ->email->toBe($email)
        ->user->toBeNull();
});

test('isPending attribute returns true for pending invitations', function () {
    $presentation = Presentation::factory()->create();
    $user = User::factory()->create();

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $user->id,
        'email' => $user->email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    expect($presentationUser->isPending)->toBeTrue();
});

test('isPending attribute returns false for non-pending invitations', function () {
    $presentation = Presentation::factory()->create();
    $user = User::factory()->create();

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $user->id,
        'email' => $user->email,
        'invite_status' => InviteStatus::ACCEPTED,
    ]);

    expect($presentationUser->isPending)->toBeFalse();
});

test('isAccepted attribute returns true for accepted invitations', function () {
    $presentation = Presentation::factory()->create();
    $user = User::factory()->create();

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $user->id,
        'email' => $user->email,
        'invite_status' => InviteStatus::ACCEPTED,
    ]);

    expect($presentationUser->isAccepted)->toBeTrue();
});

test('isAccepted attribute returns false for non-accepted invitations', function () {
    $presentation = Presentation::factory()->create();
    $user = User::factory()->create();

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $user->id,
        'email' => $user->email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    expect($presentationUser->isAccepted)->toBeFalse();
});

test('isRejected attribute returns true for rejected invitations', function () {
    $presentation = Presentation::factory()->create();
    $user = User::factory()->create();

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $user->id,
        'email' => $user->email,
        'invite_status' => InviteStatus::REJECTED,
    ]);

    expect($presentationUser->isRejected)->toBeTrue();
});

test('isRejected attribute returns false for non-rejected invitations', function () {
    $presentation = Presentation::factory()->create();
    $user = User::factory()->create();

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $user->id,
        'email' => $user->email,
        'invite_status' => InviteStatus::ACCEPTED,
    ]);

    expect($presentationUser->isRejected)->toBeFalse();
});

test('accept method updates status and sets accepted_at timestamp', function () {
    $presentation = Presentation::factory()->create();
    $user = User::factory()->create();

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $user->id,
        'email' => $user->email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    expect($presentationUser)
        ->invite_status->toBe(InviteStatus::PENDING)
        ->accepted_at->toBeNull();

    $this->actingAs($user);
    $presentationUser->accept();

    $presentationUser->refresh();

    expect($presentationUser)
        ->invite_status->toBe(InviteStatus::ACCEPTED)
        ->accepted_at->not->toBeNull()
        ->accepted_at->toBeInstanceOf(\Carbon\Carbon::class);
});

test('accept method sets user_id for authenticated user when not set', function () {
    $presentation = Presentation::factory()->create();
    $user = User::factory()->create();
    $email = $user->email;

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => null,
        'email' => $email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    expect($presentationUser->user_id)->toBeNull();

    $this->actingAs($user);
    $presentationUser->accept();

    $presentationUser->refresh();

    expect($presentationUser)
        ->user_id->toBe($user->id)
        ->invite_status->toBe(InviteStatus::ACCEPTED);
});

test('accept method does not change existing user_id', function () {
    $presentation = Presentation::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $user1->id,
        'email' => $user1->email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    $this->actingAs($user2);
    $presentationUser->accept();

    $presentationUser->refresh();

    expect($presentationUser)
        ->user_id->toBe($user1->id) // Should still be user1
        ->invite_status->toBe(InviteStatus::ACCEPTED);
});

test('reject method updates status to rejected', function () {
    $presentation = Presentation::factory()->create();
    $user = User::factory()->create();

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $user->id,
        'email' => $user->email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    expect($presentationUser->invite_status)->toBe(InviteStatus::PENDING);

    $presentationUser->reject();

    $presentationUser->refresh();

    expect($presentationUser)
        ->invite_status->toBe(InviteStatus::REJECTED);
});

test('reject method does not set accepted_at timestamp', function () {
    $presentation = Presentation::factory()->create();
    $user = User::factory()->create();

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $user->id,
        'email' => $user->email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    $presentationUser->reject();

    $presentationUser->refresh();

    expect($presentationUser->accepted_at)->toBeNull();
});

test('invite_status is cast to InviteStatus enum', function () {
    $presentation = Presentation::factory()->create();
    $user = User::factory()->create();

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $user->id,
        'email' => $user->email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    expect($presentationUser->invite_status)
        ->toBeInstanceOf(InviteStatus::class)
        ->toBe(InviteStatus::PENDING);
});

test('invited_at and accepted_at are cast to datetime', function () {
    $presentation = Presentation::factory()->create();
    $user = User::factory()->create();
    $now = now();

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $user->id,
        'email' => $user->email,
        'invite_status' => InviteStatus::ACCEPTED,
        'invited_at' => $now,
        'accepted_at' => $now,
    ]);

    expect($presentationUser)
        ->invited_at->toBeInstanceOf(\Carbon\Carbon::class)
        ->accepted_at->toBeInstanceOf(\Carbon\Carbon::class);
});
