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
});
