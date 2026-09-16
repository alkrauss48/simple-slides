<?php

use App\Jobs\AggregateDailyViews;
use Illuminate\Support\Facades\Queue;

it('dispatches the daily view aggregation job at midnight', function () {
    Queue::fake([AggregateDailyViews::class]);
    $this->travelTo('2026-09-15 00:00:00');

    $this->artisan('schedule:run')->assertSuccessful();

    Queue::assertPushed(AggregateDailyViews::class);
});

it('does not dispatch the daily view aggregation job at other times of day', function () {
    Queue::fake([AggregateDailyViews::class]);
    $this->travelTo('2026-09-15 12:30:00');

    $this->artisan('schedule:run')->assertSuccessful();

    Queue::assertNotPushed(AggregateDailyViews::class);
});
