<?php

use App\Models\DailyView;

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
