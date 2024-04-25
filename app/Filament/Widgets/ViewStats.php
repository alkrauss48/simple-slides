<?php

namespace App\Filament\Widgets;

use App\Models\AggregateView;
use App\Models\DailyView;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ViewStats extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            $this->aggregateViews(withinRange: true),
            $this->dailyViews(),
            $this->aggregateViews(),
        ];
    }

    private function dailyViews(): Stat
    {
        $views = DailyView::forUser()
            ->stats(presentationId: $this->filters['presentation_id'])
            ->get();

        $totalviews = $views->count();
        $uniqueviews = $views->unique(function (DailyView $item) {
            return $item->presentation_id.$item->session_id;
        })->count();

        $percentUniqueviews = $totalviews == 0
            ? 0
            : round(($uniqueviews / $totalviews) * 100);

        return Stat::make('Total Views Today', $totalviews)
            ->description($percentUniqueviews."% ($uniqueviews) Unique Views");
    }

    private function aggregateViews(bool $withinRange = false): Stat
    {
        $views = AggregateView::forUser()
            ->stats(
                presentationId: $this->filters['presentation_id'],
                startDate: $withinRange ? $this->filters['start_date'] : null,
                endDate: $withinRange ? $this->filters['end_date'] : null,
            )->get();

        $totalviews = $views->sum('total_count');
        $uniqueviews = $views->sum('unique_count');

        $percentUniqueviews = $totalviews == 0
            ? 0
            : round(($uniqueviews / $totalviews) * 100);

        $heading = $withinRange ? 'Total Views in Date Range' : 'Total Lifetime Views';

        return Stat::make($heading, $totalviews)
            ->description($percentUniqueviews."% ($uniqueviews) Unique Views");
    }
}
