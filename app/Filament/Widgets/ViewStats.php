<?php

namespace App\Filament\Widgets;

use App\Models\AggregateView;
use App\Models\DailyView;
use Filament\Support\Enums\IconPosition;
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
            $this->inRangeViews(),
            $this->dailyViews(),
            $this->lifetimeViews(),
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

        $isActive = isset($this->filters['active_stat']) &&
            $this->filters['active_stat'] == 'today';

        $color = $isActive ? 'success' : null;
        $icon = $isActive ? 'heroicon-o-star' : null;

        return Stat::make('Total Views Today', $totalviews)
            ->url($this->setUrl(activeStat: 'today'))
            ->color($color)
            ->descriptionIcon($icon, position: IconPosition::Before)
            ->description($percentUniqueviews."% ($uniqueviews) Unique Views");
    }

    private function inRangeViews(): Stat
    {
        $views = AggregateView::forUser()
            ->stats(
                presentationId: $this->filters['presentation_id'],
                startDate: $this->filters['start_date'],
                endDate: $this->filters['end_date'],
            )->get();

        $totalviews = $views->sum('total_count');
        $uniqueviews = $views->sum('unique_count');

        $percentUniqueviews = $totalviews == 0
            ? 0
            : round(($uniqueviews / $totalviews) * 100);

        $isActive = isset($this->filters['active_stat']) &&
            $this->filters['active_stat'] == 'range';

        $color = $isActive ? 'success' : null;
        $icon = $isActive ? 'heroicon-o-star' : null;

        return Stat::make('Total Views in Date Range', $totalviews)
            ->url($this->setUrl(activeStat: 'range'))
            ->color($color)
            ->descriptionIcon($icon, position: IconPosition::Before)
            ->description($percentUniqueviews."% ($uniqueviews) Unique Views");
    }

    private function lifetimeViews(): Stat
    {
        $views = AggregateView::forUser()
            ->stats(
                presentationId: $this->filters['presentation_id'],
                startDate: null,
                endDate: null,
            )->get();

        $totalviews = $views->sum('total_count');
        $uniqueviews = $views->sum('unique_count');

        $percentUniqueviews = $totalviews == 0
            ? 0
            : round(($uniqueviews / $totalviews) * 100);

        $isActive = isset($this->filters['active_stat']) &&
            $this->filters['active_stat'] == 'lifetime';

        $color = $isActive ? 'success' : null;
        $icon = $isActive ? 'heroicon-o-star' : null;

        return Stat::make('Total Lifetime Views', $totalviews)
            ->url($this->setUrl(activeStat: 'lifetime'))
            ->color($color)
            ->descriptionIcon($icon, position: IconPosition::Before)
            ->description($percentUniqueviews."% ($uniqueviews) Unique Views");
    }

    private function setUrl(string $activeStat): string
    {
        return '/admin?filters[presentation_id]='
            .(isset($this->filters['presentation_id']) ? $this->filters['presentation_id'] : null)
            .'&filters[start_date]='
            .(isset($this->filters['start_date']) ? $this->filters['start_date'] : null)
            .'&filters[end_date]='
            .(isset($this->filters['end_date']) ? $this->filters['end_date'] : null)
            .'&filters[active_stat]='
            .$activeStat;

    }
}
