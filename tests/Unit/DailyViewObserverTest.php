<?php

use App\Models\DailyView;

test('Daily views add the session_id if one is not provided', function () {
    $view = DailyView::factory()->create([
        'session_id' => null,
    ]);

    expect($view)
        ->session_id->not->toBeNull();
});

test('Daily views do not add the session_id if one is already provided', function () {
    $view = DailyView::factory()->create([
        'session_id' => 'abc123',
    ]);

    expect($view)
        ->session_id->toBe('abc123');
});
