<?php

use App\Enums\InviteStatus;
use App\Models\Presentation;
use App\Models\PresentationUser;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\TestResponse;

beforeEach(function () {
    Notification::fake();
});

/**
 * Both invitation routes verify signatures, so tests have to travel the same
 * signed URLs the invitation email hands out.
 */
function signedInvitationUrl(string $name, string $token, ?DateTimeInterface $expiresAt = null): string
{
    return URL::signedRoute($name, ['token' => $token], $expiresAt ?? now()->addDays(7));
}

/**
 * The returnTo target is a signed invitations.accept URL, and its signature is
 * not reproducible from the test side, so assert on the parts that matter.
 */
function assertRedirectToLoginReturningTo(TestResponse $response, string $token): void
{
    $response->assertRedirectContains(route('filament.admin.auth.login'));

    expect(returnToFrom($response))
        ->toStartWith(route('invitations.accept', ['token' => $token]))
        ->toContain('signature=');
}

function assertRedirectToRegisterReturningTo(TestResponse $response, string $email, string $token): void
{
    $response->assertRedirectContains(route('filament.admin.auth.register'));
    $response->assertRedirectContains('email='.urlencode($email));

    expect(returnToFrom($response))
        ->toStartWith(route('invitations.accept', ['token' => $token]))
        ->toContain('signature=');
}

function returnToFrom(TestResponse $response): string
{
    parse_str(parse_url($response->headers->get('Location'), PHP_URL_QUERY) ?? '', $query);

    return $query['returnTo'] ?? '';
}

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

    $response = $this->get(signedInvitationUrl('invitations.show', $invitation->invite_token));

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

    $response = $this->get(signedInvitationUrl('invitations.show', $invitation->invite_token));

    assertRedirectToLoginReturningTo($response, $invitation->invite_token);
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

    $response = $this->get(signedInvitationUrl('invitations.show', $invitation->invite_token));

    assertRedirectToRegisterReturningTo($response, $email, $invitation->invite_token);
    $response->assertSessionHas('info');
});

test('show returns 404 for invalid token', function () {
    $response = $this->get(signedInvitationUrl('invitations.show', 'invalid-token'));

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

    $response = $this->get(signedInvitationUrl('invitations.show', $invitation->invite_token));

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

    $response = $this->get(signedInvitationUrl('invitations.show', $invitation->invite_token));

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

    $response = $this->get(signedInvitationUrl('invitations.accept', $invitation->invite_token));

    assertRedirectToLoginReturningTo($response, $invitation->invite_token);
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

    $response = $this->get(signedInvitationUrl('invitations.accept', $invitation->invite_token));

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

    $response = $this->get(signedInvitationUrl('invitations.accept', $invitation->invite_token));

    assertRedirectToLoginReturningTo($response, $invitation->invite_token);
    $response->assertSessionHas('error');

    expect($invitation->refresh())
        ->invite_status->toBe(InviteStatus::PENDING);
});

test('accept returns 404 for invalid token', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(signedInvitationUrl('invitations.accept', 'invalid-token'));

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

    $response = $this->get(signedInvitationUrl('invitations.accept', $invitation->invite_token));

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

    $response = $this->get(signedInvitationUrl('invitations.accept', $invitation->invite_token));

    $response->assertRedirect(route('filament.admin.resources.presentations.edit', ['record' => $presentation->id]));

    expect($invitation->refresh())
        ->invite_status->toBe(InviteStatus::ACCEPTED)
        ->user_id->toBe($newUser->id)
        ->accepted_at->not->toBeNull();
});

test('an unsigned invitation URL is rejected', function () {
    // The raw token used to be enough on its own, which made the signature and
    // the 7-day expiry on the emailed link purely decorative.
    $invitation = pendingInvitation();

    $this->actingAs(User::factory()->create(['email' => $invitation->email]))
        ->get(route('invitations.show', ['token' => $invitation->invite_token]))
        ->assertForbidden();

    expect($invitation->refresh()->invite_status)->toBe(InviteStatus::PENDING);
});

test('an expired invitation URL is rejected', function () {
    $invitation = pendingInvitation();

    $url = signedInvitationUrl('invitations.show', $invitation->invite_token, now()->subMinute());

    $this->actingAs(User::factory()->create(['email' => $invitation->email]))
        ->get($url)
        ->assertForbidden();

    expect($invitation->refresh()->invite_status)->toBe(InviteStatus::PENDING);
});

test('a tampered invitation URL is rejected', function () {
    $victim = pendingInvitation();
    $other = pendingInvitation();

    // Swap in another invitation's token while keeping the original signature.
    $url = str_replace(
        $victim->invite_token,
        $other->invite_token,
        signedInvitationUrl('invitations.show', $victim->invite_token),
    );

    $this->actingAs(User::factory()->create(['email' => $other->email]))
        ->get($url)
        ->assertForbidden();

    expect($other->refresh()->invite_status)->toBe(InviteStatus::PENDING);
});

test('a rejected invitation URL explains itself instead of showing a bare 403', function () {
    $invitation = pendingInvitation();

    $this->get(route('invitations.show', ['token' => $invitation->invite_token]))
        ->assertForbidden()
        ->assertSee('This invitation link is no longer valid');
});

test('the accept URL handed to the login page survives a round trip', function () {
    // The returnTo URL is generated by the app, not the email, so it has to be
    // signed too or the user lands on a 403 the moment they finish logging in.
    $invitation = pendingInvitation();
    $user = User::factory()->create(['email' => $invitation->email]);

    $returnTo = returnToFrom(
        $this->get(signedInvitationUrl('invitations.show', $invitation->invite_token))
    );

    $this->actingAs($user)
        ->get($returnTo)
        ->assertRedirect(route('filament.admin.resources.presentations.edit', [
            'record' => $invitation->presentation_id,
        ]));

    expect($invitation->refresh()->invite_status)->toBe(InviteStatus::ACCEPTED);
});

test('the accept URL inherits the original expiry rather than restarting it', function () {
    $invitation = pendingInvitation();
    $expiresAt = now()->addMinutes(5);

    $returnTo = returnToFrom(
        $this->get(signedInvitationUrl('invitations.show', $invitation->invite_token, $expiresAt))
    );

    parse_str(parse_url($returnTo, PHP_URL_QUERY) ?? '', $query);

    expect((int) $query['expires'])->toBe($expiresAt->getTimestamp());
});

function pendingInvitation(): PresentationUser
{
    return PresentationUser::create([
        'presentation_id' => Presentation::factory()->create()->id,
        'user_id' => null,
        'email' => fake()->unique()->safeEmail(),
        'invite_status' => InviteStatus::PENDING,
    ]);
}
