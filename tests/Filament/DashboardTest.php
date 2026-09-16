<?php

use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\TopViews;
use App\Models\AggregateView;
use App\Models\Presentation;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->actingAs($this->user);
});

it('renders the dashboard', function () {
    livewire(Dashboard::class)
        ->assertSuccessful();
});

it('renders the top views widget over grouped aggregate data', function () {
    // Two presentations, several rows each, so the widget's query actually has
    // something to GROUP BY. Filament 4 appends a primary-key sort to table
    // queries by default, which a grouped aggregate query cannot satisfy on
    // Postgres — this is what catches that regression.
    $presentations = Presentation::factory()
        ->count(2)
        ->create(['user_id' => $this->user->id]);

    foreach ($presentations as $presentation) {
        AggregateView::factory()->count(3)->create([
            'presentation_id' => $presentation->id,
            'adhoc_slug' => null,
        ]);
    }

    livewire(TopViews::class, [
        'pageFilters' => [
            'presentation_id' => null,
            'start_date' => now()->subDays(8)->toDateString(),
            'end_date' => now()->toDateString(),
        ],
    ])
        ->assertSuccessful()
        ->assertCanSeeTableRecords($presentations->map(
            fn (Presentation $presentation) => AggregateView::query()
                ->where('presentation_id', $presentation->id)
                ->first()
        ));
});

it('renders the dashboard widgets together', function () {
    $presentation = Presentation::factory()->create(['user_id' => $this->user->id]);

    AggregateView::factory()->count(2)->create([
        'presentation_id' => $presentation->id,
        'adhoc_slug' => null,
    ]);

    $this->get(Dashboard::getUrl())
        ->assertSuccessful();
});
