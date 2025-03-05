<?php

use App\Models\DailyView;
use App\Models\Presentation;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('published presentation', function () {
    beforeEach(function () {
        $this->publishedPresentation = Presentation::factory()->create([
            'is_published' => true,
            'user_id' => $this->user->id,
        ]);
    });

    test('show screen can be rendered for unauthenticated user', function () {
        $response = $this->get(route('presentations.show', [
            'user' => $this->user->username,
            'slug' => $this->publishedPresentation->slug,
        ]));

        $response->assertStatus(200);
    });

    test('show action generates daily view for unauthenticated user', function () {
        $response = $this->get(route('presentations.show', [
            'user' => $this->user->username,
            'slug' => $this->publishedPresentation->slug,
        ]));

        $this->assertDatabaseHas(DailyView::class, [
            'presentation_id' => $this->publishedPresentation->id,
        ]);
    });

    test('show action does not generate daily view for admin user', function () {
        $adminUser = User::factory()->create(['is_admin' => true]);

        $response = $this
            ->actingAs($adminUser)
            ->get(route('presentations.show', [
                'user' => $this->user->username,
                'slug' => $this->publishedPresentation->slug,
            ]));

        $this->assertDatabaseMissing(DailyView::class, [
            'presentation_id' => $this->publishedPresentation->id,
        ]);
    });

    test('show action does not generate daily view for creating user', function () {
        $response = $this
            ->actingAs($this->user)
            ->get(route('presentations.show', [
                'user' => $this->user->username,
                'slug' => $this->publishedPresentation->slug,
            ]));

        $this->assertDatabaseMissing(DailyView::class, [
            'presentation_id' => $this->publishedPresentation->id,
        ]);
    });

    test('show action does generate daily view for non-creating user', function () {
        $response = $this
            ->actingAs(User::factory()->create())
            ->get(route('presentations.show', [
                'user' => $this->user->username,
                'slug' => $this->publishedPresentation->slug,
            ]));

        $this->assertDatabaseHas(DailyView::class, [
            'presentation_id' => $this->publishedPresentation->id,
        ]);
    });

    test('show screen returns the right view and data', function () {
        $response = $this->get(route('presentations.show', [
            'user' => $this->user->username,
            'slug' => $this->publishedPresentation->slug,
        ]));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Presentation')
            ->has('presentation', fn (Assert $page) => $page
                ->where('id', $this->publishedPresentation->id)
                ->where('content', $this->publishedPresentation->content)
                ->where('slide_delimiter', $this->publishedPresentation->slide_delimiter)
                ->where('is_published', $this->publishedPresentation->is_published)
            )
            ->has('meta', fn (Assert $page) => $page
                ->where('title', $this->publishedPresentation->title)
                ->where('description', $this->publishedPresentation->description)
                ->where('imageUrl', $this->publishedPresentation->getFirstMediaUrl('thumbnail'))
            )
        );
    });

    test('non-existing username for user shows 404', function () {
        $response = $this->get(route('presentations.show', [
            'user' => 'foo',
            'slug' => $this->publishedPresentation->slug,
        ]));

        $response->assertStatus(404);
    });
});

describe('draft presentation', function () {
    beforeEach(function () {
        $this->draftPresentation = Presentation::factory()->create([
            'is_published' => false,
            'user_id' => $this->user->id,
        ]);
    });

    test('show screen is not rendered for unauthenticated user', function () {
        $response = $this->get(route('presentations.show', [
            'user' => $this->user->username,
            'slug' => $this->draftPresentation->slug,
        ]));

        $response->assertStatus(403);
    });

    test('show action does not generate daily view', function () {
        $response = $this->get(route('presentations.show', [
            'user' => $this->user->username,
            'slug' => $this->draftPresentation->slug,
        ]));

        $this->assertDatabaseMissing(DailyView::class, [
            'presentation_id' => $this->draftPresentation->id,
        ]);
    });

    test('show screen can be rendered for author', function () {
        $response = $this
            ->actingAs($this->user)
            ->get(route('presentations.show', [
                'user' => $this->user->username,
                'slug' => $this->draftPresentation->slug,
            ]));

        $response->assertStatus(200);
    });

    test('show screen is not rendered for non-author', function () {
        $response = $this
            ->actingAs(User::factory()->create())
            ->get(route('presentations.show', [
                'user' => $this->user->username,
                'slug' => $this->draftPresentation->slug,
            ]));

        $response->assertStatus(403);
    });
});

test('non-existing slug for presentation shows 404', function () {
    $response = $this->get(route('presentations.show', [
        'user' => $this->user->username,
        'slug' => 'foo',
    ]));

    $response->assertStatus(404);
});
