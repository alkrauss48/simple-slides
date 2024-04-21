<?php

namespace App\Filament\Widgets;

use App\Models\AggregateView;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class ViewTrendsChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'View Trends in Date Range';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $trend = Trend::query(
            AggregateView::forUser()
                ->stats(presentationId: $this->filters['presentation_id'])
        )->between(
            start: Carbon::parse($this->filters['start_date'])->startOfDay(),
            end: Carbon::parse($this->filters['end_date'])->endOfDay(),
        )
            ->perDay();

        $totalData = $trend->sum('total_count');
        $uniqueData = $trend->sum('unique_count');

        return [
            'datasets' => [
                [
                    'label' => 'Total Views',
                    'data' => $totalData->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#9BD0F5',
                ],
                [
                    'label' => 'Unique Views',
                    'data' => $uniqueData->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $totalData->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
