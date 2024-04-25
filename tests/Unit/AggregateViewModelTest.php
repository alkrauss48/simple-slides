<?php

use App\Enums\PresentationFilter;
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

describe('stats', function () {
    beforeEach(function () {
        $this->presentation = Presentation::factory()->create();

        // Total of 12 AggregateView records

        AggregateView::factory()->count(5)->presentation()->create([
            'created_at' => now()->subDays(6),
        ]);

        AggregateView::factory()->count(3)->instructions()->create([
            'created_at' => now()->subDays(4),
        ]);

        AggregateView::factory()->adhoc()->create([
            'created_at' => now()->subDays(2),
        ]);

        AggregateView::factory()->create([
            'presentation_id' => $this->presentation,
            'created_at' => now()->subDays(4),
        ]);

        AggregateView::factory()->create([
            'presentation_id' => $this->presentation,
            'created_at' => now()->subDays(2),
        ]);

        // Created now
        AggregateView::factory()->create([
            'presentation_id' => $this->presentation,
        ]);
    });

    test('will show all records with no filters set', function () {
        expect(AggregateView::stats()->count())->toBe(12);
    });

    test('will show all records from start date', function () {
        expect(AggregateView::stats(startDate: now()->subDays(5)->toDateString())
            ->count())->toBe(7);
    });

    test('will show all records until end date', function () {
        expect(AggregateView::stats(endDate: now()->subDays(3)->toDateString())
            ->count())->toBe(9);
    });

    test('will show all records between start and end date', function () {
        expect(AggregateView::stats(
            startDate: now()->subDays(5)->toDateString(),
            endDate: now()->subDays(3)->toDateString(),
        )
            ->count())->toBe(4);
    });

    test('will show all records between start and end date for a presentation', function () {
        expect(AggregateView::stats(
            startDate: now()->subDays(5)->toDateString(),
            endDate: now()->subDays(3)->toDateString(),
            presentationId: $this->presentation->id,
        )
            ->count())->toBe(1);
    });

    test('will show instructions records', function () {
        expect(AggregateView::stats(presentationId: PresentationFilter::INSTRUCTIONS->value)
            ->count())->toBe(3);
    });

    test('will show adhoc records', function () {
        expect(AggregateView::stats(presentationId: PresentationFilter::ADHOC->value)
            ->count())->toBe(1);
    });

    test('will show specific presentation records', function () {
        expect(AggregateView::stats(presentationId: $this->presentation->id)
            ->count())->toBe(3);
    });
});
