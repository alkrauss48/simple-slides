<?php

use App\Models\DailyView;

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

test('adhoc slides show screen can be rendered', function () {
    $response = $this->get(route('adhoc-slides.show', [
        'slides' => 'foo', // Any string will work
    ]));

    $response->assertStatus(200);
});

test('adhoc slides show screen generate daily view', function () {
    $response = $this->get(route('adhoc-slides.show', [
        'slides' => 'foo',
    ]));

    $this->assertDatabaseHas(DailyView::class, [
        'adhoc_slug' => 'foo',
    ]);
});
