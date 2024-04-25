<?php

use App\Jobs\AggregateDailyViews;
use App\Models\AggregateView;
use App\Models\DailyView;
use App\Models\Presentation;

describe('Presentation aggregate views are created from:', function () {
    test('1 presentation, 3 daily views, 1 unique session', function () {
        $presentation = Presentation::factory()->create();

        DailyView::factory()->count(3)->create([
            'presentation_id' => $presentation->id,
            'session_id' => 'abc123',
        ]);

        (new AggregateDailyViews)->handle();

        $this->assertDatabaseHas(AggregateView::class, [
            'presentation_id' => $presentation->id,
            'total_count' => 3,
            'unique_count' => 1,
        ]);
    });

    test('1 presentation, 4 daily views, 2 unique sessions', function () {
        $presentation = Presentation::factory()->create();

        DailyView::factory()->count(2)->create([
            'presentation_id' => $presentation->id,
            'session_id' => 'abc123',
        ]);

        DailyView::factory()->count(2)->create([
            'presentation_id' => $presentation->id,
            'session_id' => 'efg456',
        ]);

        (new AggregateDailyViews)->handle();

        $this->assertDatabaseHas(AggregateView::class, [
            'presentation_id' => $presentation->id,
            'total_count' => 4,
            'unique_count' => 2,
        ]);
    });

    test('3 presentations, various daily views, various unique sessions', function () {
        $presentations = Presentation::factory()->count(3)->create();

        DailyView::factory()->count(2)->create([
            'presentation_id' => $presentations[0]->id,
            'session_id' => 'abc123',
        ]);

        DailyView::factory()->count(3)->create([
            'presentation_id' => $presentations[1]->id,
        ]);

        DailyView::factory()->count(4)->create([
            'presentation_id' => $presentations[2]->id,
            'session_id' => 'efg456',
        ]);

        DailyView::factory()->count(2)->create([
            'presentation_id' => $presentations[2]->id,
        ]);

        (new AggregateDailyViews)->handle();

        $this->assertDatabaseHas(AggregateView::class, [
            'presentation_id' => $presentations[0]->id,
            'total_count' => 2,
            'unique_count' => 1,
        ]);

        $this->assertDatabaseHas(AggregateView::class, [
            'presentation_id' => $presentations[1]->id,
            'total_count' => 3,
            'unique_count' => 3,
        ]);

        $this->assertDatabaseHas(AggregateView::class, [
            'presentation_id' => $presentations[2]->id,
            'total_count' => 6,
            'unique_count' => 3,
        ]);
    });
});

describe('Adhoc Presentation aggregate views are created from:', function () {
    test('1 presentation, 3 daily views, 1 unique session', function () {
        $presentation = Presentation::factory()->create();

        DailyView::factory()->count(3)->create([
            'adhoc_slug' => 'foo',
            'presentation_id' => null,
            'session_id' => 'abc123',
        ]);

        (new AggregateDailyViews)->handle();

        $this->assertDatabaseHas(AggregateView::class, [
            'adhoc_slug' => 'foo',
            'presentation_id' => null,
            'total_count' => 3,
            'unique_count' => 1,
        ]);
    });

    test('1 adhoc presentation, 4 daily views, 2 unique sessions', function () {
        DailyView::factory()->count(2)->create([
            'adhoc_slug' => 'foo',
            'presentation_id' => null,
            'session_id' => 'abc123',
        ]);

        DailyView::factory()->count(2)->create([
            'adhoc_slug' => 'foo',
            'presentation_id' => null,
            'session_id' => 'efg456',
        ]);

        (new AggregateDailyViews)->handle();

        $this->assertDatabaseHas(AggregateView::class, [
            'adhoc_slug' => 'foo',
            'presentation_id' => null,
            'total_count' => 4,
            'unique_count' => 2,
        ]);
    });

    test('3 adhoc presentations, various daily views, various unique sessions', function () {
        DailyView::factory()->count(2)->create([
            'adhoc_slug' => 'foo',
            'presentation_id' => null,
            'session_id' => 'abc123',
        ]);

        DailyView::factory()->count(3)->create([
            'adhoc_slug' => 'bar',
            'presentation_id' => null,
        ]);

        DailyView::factory()->count(4)->create([
            'adhoc_slug' => 'baz',
            'presentation_id' => null,
            'session_id' => 'efg456',
        ]);

        DailyView::factory()->count(2)->create([
            'adhoc_slug' => 'baz',
            'presentation_id' => null,
        ]);

        (new AggregateDailyViews)->handle();

        $this->assertDatabaseHas(AggregateView::class, [
            'adhoc_slug' => 'foo',
            'presentation_id' => null,
            'total_count' => 2,
            'unique_count' => 1,
        ]);

        $this->assertDatabaseHas(AggregateView::class, [
            'adhoc_slug' => 'bar',
            'presentation_id' => null,
            'total_count' => 3,
            'unique_count' => 3,
        ]);

        $this->assertDatabaseHas(AggregateView::class, [
            'adhoc_slug' => 'baz',
            'presentation_id' => null,
            'total_count' => 6,
            'unique_count' => 3,
        ]);
    });
});
