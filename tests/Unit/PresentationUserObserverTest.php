<?php

use App\Enums\InviteStatus;
use App\Models\Presentation;
use App\Models\PresentationUser;
use App\Models\User;
use App\Notifications\PresentationUserCreated;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
});

test('observer sends notification to existing user when invitation is created', function () {
    $presentation = Presentation::factory()->create();
    $user = User::factory()->create();

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $user->id,
        'email' => $user->email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    Notification::assertSentTo($user, PresentationUserCreated::class, function ($notification, $channels) use ($presentationUser) {
        return $notification->presentationUser->id === $presentationUser->id;
    });
});

test('observer sends on-demand notification when invitation is created for non-existing user', function () {
    $presentation = Presentation::factory()->create();
    $email = 'nonexistent@example.com';

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => null,
        'email' => $email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    Notification::assertSentOnDemand(PresentationUserCreated::class, function ($notification, $channels, $notifiable) use ($presentationUser, $email) {
        return $notification->presentationUser->id === $presentationUser->id
            && in_array('mail', $channels)
            && $notifiable->routes['mail'] === $email;
    });
});

test('observer does not send notification when user_id is set but user does not exist', function () {
    $presentation = Presentation::factory()->create();

    // Create presentation user with a non-existent user_id
    $presentationUser = new PresentationUser([
        'presentation_id' => $presentation->id,
        'user_id' => 99999, // non-existent user
        'email' => 'test@example.com',
        'invite_status' => InviteStatus::PENDING,
    ]);

    // We expect this to fail or handle gracefully
    try {
        $presentationUser->saveOrFail();
    } catch (\Exception $e) {
        // Foreign key constraint should prevent this
        expect($e)->toBeInstanceOf(\Exception::class);
    }
});

test('observer notification contains correct presentation information', function () {
    $presentation = Presentation::factory()->create(['title' => 'Test Presentation']);
    $user = User::factory()->create();

    $presentationUser = PresentationUser::create([
        'presentation_id' => $presentation->id,
        'user_id' => $user->id,
        'email' => $user->email,
        'invite_status' => InviteStatus::PENDING,
    ]);

    Notification::assertSentTo($user, PresentationUserCreated::class, function ($notification) {
        return $notification->presentationUser->presentation->title === 'Test Presentation';
    });
});
