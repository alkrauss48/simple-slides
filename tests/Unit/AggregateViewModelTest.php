<?php

use App\Models\AggregateView;
use App\Models\Presentation;
use App\Models\User;

describe('Instructions', function () {
    test('Aggregate views return true for instructions', function () {
        $view = AggregateView::factory()->create([
            'presentation_id' => null,
            'adhoc_slug' => null,
        ]);

        expect($view)->isInstructions->toBeTrue();
    });

    test('Aggregate views return false for instructions, because it\'s adhoc', function () {
        $view = AggregateView::factory()->create([
            'adhoc_slug' => 'abc',
        ]);

        expect($view)->isInstructions->toBeFalse();
    });

    test('Aggregate views return false for instructions, because it\'s a presentation', function () {
        $view = AggregateView::factory()->create([
            'presentation_id' => Presentation::factory(),
        ]);

        expect($view)->isInstructions->toBeFalse();
    });
});

describe('Adhoc', function () {
    test('Aggregate views return true for adhoc', function () {
        $view = AggregateView::factory()->create([
            'presentation_id' => null,
            'adhoc_slug' => 'abc',
        ]);

        expect($view)->isAdhoc->toBeTrue();
    });

    test('Aggregate views return false for adhoc, because it\'s instructions', function () {
        $view = AggregateView::factory()->create([
            'presentation_id' => null,
            'adhoc_slug' => null,
        ]);

        expect($view)->isAdhoc->toBeFalse();
    });

    test('Aggregate views return false for adhoc, because it\'s a presentation', function () {
        $view = AggregateView::factory()->create([
            'presentation_id' => Presentation::factory(),
        ]);

        expect($view)->isAdhoc->toBeFalse();
    });
});

describe('forUser', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
        $this->user = User::factory()->hasPresentations(1)->create();

        AggregateView::factory()->count(10)->create();

        AggregateView::factory()->count(2)->create([
            'presentation_id' => $this->user->presentations()->first()->id,
        ]);
    });

    test('Admins can see all aggregate views', function () {
        $this->actingAs($this->admin);

        expect(AggregateView::forUser()->count())->toBe(12);
    });

    test('Users can see only their aggregate views', function () {
        $this->actingAs($this->user);

        expect(AggregateView::forUser()->count())->toBe(2);
    });
});
