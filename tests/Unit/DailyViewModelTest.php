<?php

use App\Models\DailyView;
use App\Models\User;

test('Daily views can get created for adhoc slugs', function () {
    $view = DailyView::createForAdhocPresentation('abc123');

    expect($view)
        ->adhoc_slug->toBe('abc123');
});

test('Daily views can get created with no adhoc slugs', function () {
    $view = DailyView::createForAdhocPresentation();

    expect($view)
        ->adhoc_slug->toBeNull();
});

describe('forUser', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
        $this->user = User::factory()->hasPresentations(1)->create();

        DailyView::factory()->count(10)->create();

        DailyView::factory()->count(2)->create([
            'presentation_id' => $this->user->presentations()->first()->id,
        ]);
    });

    test('Admins can see all daily views', function () {
        $this->actingAs($this->admin);

        expect(DailyView::forUser()->count())->toBe(12);
    });

    test('Users can see only their daily views', function () {
        $this->actingAs($this->user);

        expect(DailyView::forUser()->count())->toBe(2);
    });
});
