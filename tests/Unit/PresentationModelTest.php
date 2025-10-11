<?php

use App\Models\Presentation;
use App\Models\User;
use Illuminate\Support\Str;

test('Presentation automatically sets slug on create', function () {
    $presentation = Presentation::factory()->create(['slug' => null]);

    expect($presentation)
        ->slug->toBe(Str::slug($presentation->title));
});

test('Presentation does not change slug on update', function () {
    $presentation = Presentation::factory()->create();

    $originalSlug = $presentation->slug;

    $presentation->title = 'foo';
    $presentation->save();

    expect($presentation->refresh())
        ->slug->toBe($originalSlug);
});

test('Presentation can create daily view', function () {
    $presentation = Presentation::factory()->create(['slug' => null]);

    $view = $presentation->addDailyView();

    expect($view)
        ->presentation_id->toBe($presentation->id);
});

describe('forUser', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
        $this->user = User::factory()->hasPresentations(2)->create();

        Presentation::factory()->count(10)->create();
    });

    test('Admins can see all presentations', function () {
        $this->actingAs($this->admin);

        expect(Presentation::forUser()->count())->toBe(12);
    });

    test('Users can see only their presentations', function () {
        $this->actingAs($this->user);

        expect(Presentation::forUser()->count())->toBe(2);
    });

    test('Users can see presentations shared with them', function () {
        $otherUser = User::factory()->create();
        $presentation = Presentation::factory()->create(['user_id' => $otherUser->id]);

        // Share presentation with user
        \App\Models\PresentationUser::create([
            'presentation_id' => $presentation->id,
            'user_id' => $this->user->id,
            'email' => $this->user->email,
            'invite_status' => \App\Enums\InviteStatus::ACCEPTED,
            'accepted_at' => now(),
        ]);

        $this->actingAs($this->user);

        // Should see 2 own presentations + 1 shared presentation
        expect(Presentation::forUser()->count())->toBe(3);
    });

    test('Users cannot see presentations with pending invitations', function () {
        $otherUser = User::factory()->create();
        $presentation = Presentation::factory()->create(['user_id' => $otherUser->id]);

        // Create pending invitation
        \App\Models\PresentationUser::create([
            'presentation_id' => $presentation->id,
            'user_id' => $this->user->id,
            'email' => $this->user->email,
            'invite_status' => \App\Enums\InviteStatus::PENDING,
        ]);

        $this->actingAs($this->user);

        // Should only see 2 own presentations, not the pending invitation
        expect(Presentation::forUser()->count())->toBe(2);
    });

    test('Users cannot see presentations with rejected invitations', function () {
        $otherUser = User::factory()->create();
        $presentation = Presentation::factory()->create(['user_id' => $otherUser->id]);

        // Create rejected invitation
        \App\Models\PresentationUser::create([
            'presentation_id' => $presentation->id,
            'user_id' => $this->user->id,
            'email' => $this->user->email,
            'invite_status' => \App\Enums\InviteStatus::REJECTED,
        ]);

        $this->actingAs($this->user);

        // Should only see 2 own presentations, not the rejected invitation
        expect(Presentation::forUser()->count())->toBe(2);
    });
});

describe('Presentation relationships', function () {
    test('Presentation has sharedUsers relationship', function () {
        $presentation = Presentation::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        \App\Models\PresentationUser::create([
            'presentation_id' => $presentation->id,
            'user_id' => $user1->id,
            'email' => $user1->email,
            'invite_status' => \App\Enums\InviteStatus::ACCEPTED,
            'accepted_at' => now(),
        ]);

        \App\Models\PresentationUser::create([
            'presentation_id' => $presentation->id,
            'user_id' => $user2->id,
            'email' => $user2->email,
            'invite_status' => \App\Enums\InviteStatus::PENDING,
        ]);

        expect($presentation->sharedUsers)->toHaveCount(2);
        expect($presentation->sharedUsers->pluck('id')->toArray())
            ->toContain($user1->id)
            ->toContain($user2->id);
    });

    test('Presentation has presentationUsers relationship', function () {
        $presentation = Presentation::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        \App\Models\PresentationUser::create([
            'presentation_id' => $presentation->id,
            'user_id' => $user1->id,
            'email' => $user1->email,
            'invite_status' => \App\Enums\InviteStatus::ACCEPTED,
            'accepted_at' => now(),
        ]);

        \App\Models\PresentationUser::create([
            'presentation_id' => $presentation->id,
            'user_id' => $user2->id,
            'email' => $user2->email,
            'invite_status' => \App\Enums\InviteStatus::PENDING,
        ]);

        expect($presentation->presentationUsers)
            ->toHaveCount(2)
            ->each->toBeInstanceOf(\App\Models\PresentationUser::class);
    });

    test('Presentation has pendingInvitations relationship', function () {
        $presentation = Presentation::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        \App\Models\PresentationUser::create([
            'presentation_id' => $presentation->id,
            'user_id' => $user1->id,
            'email' => $user1->email,
            'invite_status' => \App\Enums\InviteStatus::PENDING,
        ]);

        \App\Models\PresentationUser::create([
            'presentation_id' => $presentation->id,
            'user_id' => $user2->id,
            'email' => $user2->email,
            'invite_status' => \App\Enums\InviteStatus::ACCEPTED,
            'accepted_at' => now(),
        ]);

        \App\Models\PresentationUser::create([
            'presentation_id' => $presentation->id,
            'user_id' => $user3->id,
            'email' => $user3->email,
            'invite_status' => \App\Enums\InviteStatus::REJECTED,
        ]);

        $pendingInvitations = $presentation->pendingInvitations;

        expect($pendingInvitations)
            ->toHaveCount(1)
            ->first()->user_id->toBe($user1->id);
    });
});
