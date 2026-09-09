<?php

namespace App\Filament\Widgets;

use App\Enums\PresentationFilter;
use App\Models\AggregateView;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class TopViews extends BaseWidget
{
    use InteractsWithPageFilters;

    // v4 renders widgets lazily by default; these were eager in v3, and
    // lazy placeholders also hide widget errors from page-level tests.
    protected static bool $isLazy = false;

    protected static ?string $heading = 'Detailed Views in Date Range';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 10;

    public function getTableRecordKey(Model|array $record): string
    {
        // The table query groups by these two columns, so they identify a row.
        if ($record instanceof AggregateView) {
            return $record->presentation_id.$record->adhoc_slug;
        }

        if (is_array($record)) {
            return $record['presentation_id'].$record['adhoc_slug'];
        }

        return parent::getTableRecordKey($record);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AggregateView::forUser()
                    ->stats(
                        presentationId: $this->pageFilters['presentation_id'],
                        startDate: $this->pageFilters['start_date'],
                        endDate: $this->pageFilters['end_date'],
                    )->selectRaw(
                        'presentation_id, '.
                        'adhoc_slug, '.
                        'sum(total_count) as total_count, '.
                        'sum(unique_count) as unique_count'
                    )->groupByRaw('presentation_id, adhoc_slug')
            )
            // v4 appends a primary-key sort by default, which this grouped
            // aggregate query cannot satisfy on Postgres.
            ->defaultKeySort(false)
            ->defaultSort('total_count', 'desc')
            ->columns([
                TextColumn::make('presentation.title')
                    ->badge(fn (AggregateView $record): bool => $record->isInstructions || $record->isAdhoc
                    )->color(function (AggregateView $record): ?string {
                        if ($record->isInstructions) {
                            return 'info';
                        }

                        if ($record->isAdhoc) {
                            return 'success';
                        }

                        return null;
                    })->getStateUsing(function (AggregateView $record): string {
                        if ($record->isInstructions) {
                            return PresentationFilter::INSTRUCTIONS->label();
                        }

                        if ($record->isAdhoc) {
                            return PresentationFilter::ADHOC->label();
                        }

                        return $record->presentation->title;
                    })->searchable(),
                TextColumn::make('total_count')
                    ->sortable(),
                TextColumn::make('unique_count')
                    ->sortable(),
            ])->recordUrl(fn (AggregateView $record): ?string => is_null($record->presentation_id)
                    ? null
                    : route('filament.admin.resources.presentations.edit', [
                        'record' => $record->presentation,
                    ])
            )
            ->emptyStateHeading('None of your presentations were viewed during this time.')
            ->emptyStateDescription('Stay Positive. Maybe that means it\'s time to create your next one!')
            ->emptyStateActions([
                Action::make('create')
                    ->label('New Presentation')
                    ->url(route('filament.admin.resources.presentations.create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])->recordActions([
                Action::make('View')
                    ->url(function (AggregateView $record): string {
                        if ($record->isInstructions) {
                            return route('home');
                        }

                        if ($record->isAdhoc) {
                            return route('adhoc-slides.show', [
                                'slides' => $record->adhoc_slug,
                            ]);
                        }

                        return route('presentations.show', [
                            'user' => $record->presentation->user->username,
                            'slug' => $record->presentation->slug,
                        ]);
                    })
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->openUrlInNewTab(),
            ]);
    }
}
