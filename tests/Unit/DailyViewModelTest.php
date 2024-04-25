<?php

use App\Enums\PresentationFilter;
use App\Models\DailyView;
use App\Models\Presentation;
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

describe('stats', function () {
    beforeEach(function () {
        $this->presentation = Presentation::factory()->create();

        // Total of 12 DailyView records

        DailyView::factory()->count(6)->presentation()->create();

        DailyView::factory()->count(3)->instructions()->create();
        DailyView::factory()->count(1)->adhoc()->create();

        DailyView::factory()->count(2)->create([
            'presentation_id' => $this->presentation,
        ]);
    });

    test('will show all records with no presentationId set', function () {
        expect(DailyView::stats()->count())->toBe(12);
    });

    test('will show instructions records', function () {
        expect(DailyView::stats(presentationId: PresentationFilter::INSTRUCTIONS->value)
            ->count())->toBe(3);
    });

    test('will show adhoc records', function () {
        expect(DailyView::stats(presentationId: PresentationFilter::ADHOC->value)
            ->count())->toBe(1);
    });

    test('will show specific presentation records', function () {
        expect(DailyView::stats(presentationId: $this->presentation->id)
            ->count())->toBe(2);
    });
});
