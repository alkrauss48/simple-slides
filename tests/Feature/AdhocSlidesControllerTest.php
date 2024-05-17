<?php

use App\Models\DailyView;

describe('Index', function () {
    test('adhoc slides index screen can be rendered', function () {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
    });

    test('adhoc slides index screen generate daily view', function () {
        $response = $this->get(route('home'));

        $this->assertDatabaseHas(DailyView::class, [
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

    test('adhoc slides show screen will 404 with invalid slug', function () {
        $response = $this->get(route('adhoc-slides.show', [
            'slides' => 'foo', // Not base64 encoded
        ]));

        $response->assertStatus(404);
    });

    test('adhoc slides show screen generate daily view', function () {
        $response = $this->get(route('adhoc-slides.show', [
            'slides' => base64_encode('foo'),
        ]));

        $this->assertDatabaseHas(DailyView::class, [
            'adhoc_slug' => base64_encode('foo'),
        ]);
    });

    test('adhoc slides show screen will not generate daily view with invalid slug', function () {
        $response = $this->get(route('adhoc-slides.show', [
            'slides' => 'foo', // Not base64 encoded
        ]));

        $this->assertDatabaseMissing(DailyView::class, [
            'adhoc_slug' => 'foo',
        ]);
    });
});
