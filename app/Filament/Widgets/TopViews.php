<?php

namespace App\Filament\Widgets;

use App\Models\AggregateView;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;

class TopViews extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Top Views in Date Range';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 10;

    public function getTableRecordKey(mixed $record): string
    {
        return $record->presentation_id.$record->adhoc_slug;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AggregateView::forUser()
                    ->stats(
                        presentationId: $this->filters['presentation_id'],
                        startDate: $this->filters['start_date'],
                        endDate: $this->filters['end_date'],
                    )->selectRaw(
                        'presentation_id, '.
                        'adhoc_slug, '.
                        'sum(total_count) as total_count, '.
                        'sum(unique_count) as unique_count'
                    )->groupByRaw('presentation_id, adhoc_slug')
            )
            ->defaultSort('total_count', 'desc')
            ->columns([
                TextColumn::make('presentation.title')
                    ->searchable(),
                TextColumn::make('adhoc_slug'),
                TextColumn::make('total_count')
                    ->sortable(),
                TextColumn::make('unique_count')
                    ->sortable(),
            ]);
    }
}
