<?php

namespace App\Filament\Widgets;

use App\Enums\PresentationFilter;
use App\Models\AggregateView;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;

class TopViews extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Detailed Views in Date Range';

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
            ->emptyStateHeading('No presentations found')
            ->emptyStateDescription(null)
            ->emptyStateActions([
                Action::make('create')
                    ->label('New Presentation')
                    ->url(route('filament.admin.resources.presentations.create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])->actions([
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
