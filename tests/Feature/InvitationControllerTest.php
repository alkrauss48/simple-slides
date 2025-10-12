<?php

use App\Enums\InviteStatus;
use App\Models\Presentation;
use App\Models\PresentationUser;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
});

test('show redirects authenticated user to accept invitation automatically', function () {
    $owner = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);
    $invitedUser = User::factory()->create(['email' => 'invited@example.com']);

    $invitation = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $invitedUser->id,
        'email' => $invitedUser->email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    $this->actingAs($invitedUser);

    $response = $this->get(route('invitations.show', ['token' => $invitation->invite_token]));

    $response->assertRedirect(route('filament.admin.resources.presentations.edit', ['record' => $presentation->id]));
    $response->assertSessionHas('success');

    expect($invitation->refresh())
        ->invite_status->toBe(InviteStatus::ACCEPTED)
        ->accepted_at->not->toBeNull();
});

test('show redirects existing user to login when not authenticated', function () {
    $owner = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);

    $invitation = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $existingUser->id,
        'email' => $existingUser->email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    $response = $this->get(route('invitations.show', ['token' => $invitation->invite_token]));

    $response->assertRedirect(route('filament.admin.auth.login', [
        'returnTo' => route('invitations.accept', $invitation->invite_token),
    ]));
    $response->assertSessionHas('error');
});

test('show redirects non-existing user to registration', function () {
    $owner = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);
    $email = 'newuser@example.com';

    $invitation = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => null,
        'email' => $email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    $response = $this->get(route('invitations.show', ['token' => $invitation->invite_token]));

    $response->assertRedirect(route('filament.admin.auth.register', [
        'email' => $email,
        'returnTo' => route('invitations.accept', $invitation->invite_token),
    ]));
    $response->assertSessionHas('info');
});

test('show returns 404 for invalid token', function () {
    $response = $this->get(route('invitations.show', ['token' => 'invalid-token']));

    $response->assertNotFound();
});

test('show returns 404 for already accepted invitation', function () {
    $owner = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);
    $invitedUser = User::factory()->create(['email' => 'invited@example.com']);

    $invitation = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $invitedUser->id,
        'email' => $invitedUser->email,
        'invite_status' => InviteStatus::ACCEPTED,
        'accepted_at' => now(),
    ]);

    $response = $this->get(route('invitations.show', ['token' => $invitation->invite_token]));

    $response->assertNotFound();
});

test('show returns 404 for rejected invitation', function () {
    $owner = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);
    $invitedUser = User::factory()->create(['email' => 'invited@example.com']);

    $invitation = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $invitedUser->id,
        'email' => $invitedUser->email,
        'invite_status' => InviteStatus::REJECTED,
    ]);

    $response = $this->get(route('invitations.show', ['token' => $invitation->invite_token]));

    $response->assertNotFound();
});

test('accept redirects unauthenticated user to login', function () {
    $owner = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);
    $invitedUser = User::factory()->create(['email' => 'invited@example.com']);

    $invitation = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $invitedUser->id,
        'email' => $invitedUser->email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    $response = $this->get(route('invitations.accept', ['token' => $invitation->invite_token]));

    $response->assertRedirect(route('filament.admin.auth.login', [
        'returnTo' => route('invitations.accept', $invitation->invite_token),
    ]));
    $response->assertSessionHas('error');
});

test('accept works for authenticated user with matching email', function () {
    $owner = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);
    $invitedUser = User::factory()->create(['email' => 'invited@example.com']);

    $invitation = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $invitedUser->id,
        'email' => $invitedUser->email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    $this->actingAs($invitedUser);

    $response = $this->get(route('invitations.accept', ['token' => $invitation->invite_token]));

    $response->assertRedirect(route('filament.admin.resources.presentations.edit', ['record' => $presentation->id]));
    $response->assertSessionHas('success');

    expect($invitation->refresh())
        ->invite_status->toBe(InviteStatus::ACCEPTED)
        ->accepted_at->not->toBeNull()
        ->user_id->toBe($invitedUser->id);
});

test('accept rejects authenticated user with non-matching email', function () {
    $owner = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);
    $invitedUser = User::factory()->create(['email' => 'invited@example.com']);
    $otherUser = User::factory()->create(['email' => 'other@example.com']);

    $invitation = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $invitedUser->id,
        'email' => $invitedUser->email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    $this->actingAs($otherUser);

    $response = $this->get(route('invitations.accept', ['token' => $invitation->invite_token]));

    $response->assertRedirect(route('filament.admin.auth.login', [
        'returnTo' => route('invitations.accept', $invitation->invite_token),
    ]));
    $response->assertSessionHas('error');

    expect($invitation->refresh())
        ->invite_status->toBe(InviteStatus::PENDING);
});

test('accept returns 404 for invalid token', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('invitations.accept', ['token' => 'invalid-token']));

    $response->assertNotFound();
});

test('accept returns 404 for already accepted invitation', function () {
    $owner = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);
    $invitedUser = User::factory()->create(['email' => 'invited@example.com']);

    $invitation = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $invitedUser->id,
        'email' => $invitedUser->email,
        'invite_status' => InviteStatus::ACCEPTED,
        'accepted_at' => now(),
    ]);

    $this->actingAs($invitedUser);

    $response = $this->get(route('invitations.accept', ['token' => $invitation->invite_token]));

    $response->assertNotFound();
});

test('accept sets user_id for invitation without user_id when accepted', function () {
    $owner = User::factory()->create();
    $presentation = Presentation::factory()->create(['user_id' => $owner->id]);
    $newUser = User::factory()->create(['email' => 'newuser@example.com']);

    $invitation = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => null,
        'email' => $newUser->email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    expect($invitation->user_id)->toBeNull();

    $this->actingAs($newUser);

    $response = $this->get(route('invitations.accept', ['token' => $invitation->invite_token]));

    $response->assertRedirect(route('filament.admin.resources.presentations.edit', ['record' => $presentation->id]));

    expect($invitation->refresh())
        ->invite_status->toBe(InviteStatus::ACCEPTED)
        ->user_id->toBe($newUser->id)
        ->accepted_at->not->toBeNull();
});
