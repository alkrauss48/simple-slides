<?php

use App\Models\Presentation;
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
