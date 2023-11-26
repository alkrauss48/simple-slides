<?php

test('adhoc slides index screen can be rendered', function () {
    $response = $this->get(route('home'));

    $response->assertStatus(200);
});

test('adhoc slides show screen can be rendered', function () {
    $response = $this->get(route('adhoc-slides.show', [
        'slides' => 'foo', // Any string will work
    ]));

    $response->assertStatus(200);
});
