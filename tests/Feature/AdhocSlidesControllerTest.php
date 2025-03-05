<?php

use App\Models\DailyView;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

describe('Index', function () {
    test('adhoc slides index screen can be rendered', function () {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
    });

    test('adhoc slides index screen returns the right view and data', function () {
        $response = $this->get(route('home'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('AdhocSlides')
        );
    });

    test('adhoc slides index screen generate daily view', function () {
        $this->get(route('home'));

        $this->assertDatabaseHas(DailyView::class, [
            'adhoc_slug' => null,
        ]);
    });

    test('adhoc slides index screen does not generate daily view for admin user', function () {
        $admin = User::factory()->admin()->create();

        $this
            ->actingAs($admin)
            ->get(route('home'));

        $this->assertDatabaseMissing(DailyView::class, [
            'adhoc_slug' => null,
        ]);
    });
});

describe('Show', function () {
    test('adhoc slides show screen can be rendered', function () {
        $response = $this->get(route('adhoc-slides.show', [
            'slides' => base64_encode('foo'),
        ]));

        $response->assertStatus(200);
    });

    test('adhoc slides show screen returns the right view and data', function () {
        $encodedSlides = base64_encode('foo');

        $response = $this->get(route('adhoc-slides.show', [
            'slides' => $encodedSlides,
        ]));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('AdhocSlides')
            ->where('encodedSlides', $encodedSlides)
            ->has('meta', fn (Assert $page) => $page
                ->where('title', 'My Presentation')
            )
        );
    });

    test('adhoc slides show screen will 404 with invalid slug', function () {
        $response = $this->get(route('adhoc-slides.show', [
            'slides' => 'foo', // Not base64 encoded
        ]));

        $response->assertStatus(404);
    });

    test('adhoc slides show screen generate daily view', function () {
        $this->get(route('adhoc-slides.show', [
            'slides' => base64_encode('foo'),
        ]));

        $this->assertDatabaseHas(DailyView::class, [
            'adhoc_slug' => base64_encode('foo'),
        ]);
    });

    test('adhoc slides show screen will not generate daily view with invalid slug', function () {
        $this->get(route('adhoc-slides.show', [
            'slides' => 'foo', // Not base64 encoded
        ]));

        $this->assertDatabaseMissing(DailyView::class, [
            'adhoc_slug' => 'foo',
        ]);
    });

    test('adhoc slides show screen does not generate daily view for admin user', function () {
        $admin = User::factory()->admin()->create();

        $this
            ->actingAs($admin)
            ->get(route('adhoc-slides.show', [
                'slides' => base64_encode('foo'),
            ]));

        $this->assertDatabaseMissing(DailyView::class, [
            'adhoc_slug' => base64_encode('foo'),
        ]);
    });
});
