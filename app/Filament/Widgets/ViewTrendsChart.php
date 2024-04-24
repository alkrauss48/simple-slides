<?php

namespace App\Filament\Widgets;

use App\Models\AggregateView;
use App\Models\DailyView;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ViewTrendsChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'View Trends';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 5;

    /**
     * Get trend data for today
     *
     * @return array<string, Collection<int, mixed>>
     */
    private function getDataToday(): array
    {
        $totalTrend = Trend::query(
            DailyView::forUser()
                ->stats(presentationId: $this->filters['presentation_id'])
        )->between(
            start: now()->startOfDay(),
            end: now()->endOfDay(),
        )->perHour()
            ->count();

        $uniqueTrend = Trend::query(
            DailyView::forUser()
                ->whereIn('id', function (Builder $query) {
                    $query
                        ->select('id')
                        ->distinct('session_id', 'presentation_id', 'adhoc_slug');
                })->stats(presentationId: $this->filters['presentation_id'])
        )->between(
            start: now()->startOfDay(),
            end: now()->endOfDay(),
        )->perHour()
            ->count();

        return [
            'total' => $totalTrend,
            'unique' => $uniqueTrend,
        ];
    }

    /**
     * Get trend data for a range
     *
     * @return array<string, Collection<int, mixed>>
     */
    private function getDataForRange(): array
    {
        $startDate = isset($this->filters['start_date']) && $this->filters['start_date'] != null
            ? Carbon::parse($this->filters['start_date'])
            : AggregateView::forUser()->oldest()->first()?->created_at;

        $endDate = isset($this->filters['end_date']) && $this->filters['end_date'] != null
            ? Carbon::parse($this->filters['end_date'])
            : now();

        $trend = Trend::query(
            AggregateView::forUser()
                ->stats(presentationId: $this->filters['presentation_id'])
        )->between(
            start: $startDate->startOfDay(),
            end: $endDate->endOfDay(),
        );

        if ($startDate->diffInDays(now()) > 90) {
            $trend->perMonth();
        } else {
            $trend->perDay();
        }

        $totalData = $trend->sum('total_count');
        $uniqueData = $trend->sum('unique_count');

        return [
            'total' => $totalData,
            'unique' => $uniqueData,
        ];
    }

    protected function getData(): array
    {
        $isToday = isset($this->filters['active_stat']) &&
            $this->filters['active_stat'] == 'today';

        $trendData = $isToday ? $this->getDataToday() : $this->getDataForRange();

        return [
            'datasets' => [
                [
                    'label' => 'Total Views',
                    'data' => $trendData['total']->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#9BD0F5',
                ],
                [
                    'label' => 'Unique Views',
                    'data' => $trendData['unique']->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $trendData['total']->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
