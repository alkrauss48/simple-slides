<?php

namespace App\Filament\Pages;

use App\Enums\PresentationFilter;
use App\Models\Presentation;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function getColumns(): int|string|array
    {
        return 2;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Reset Filters')
                ->color('gray')
                ->action(function () {
                    $this->filters = [
                        'presentation_id' => null,
                        'start_date' => now()->subDays(8)->toDateString(),
                        'end_date' => now()->subDay()->toDateString(),
                    ];
                }),
        ];
    }

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('presentation_id')
                            ->label('Presentation')
                            // TODO: Don't preload all simple slides;
                            // figure out if there is a filament bug that is throwing
                            // errors with searchable when there is a table
                            // widget, or if there is something in this code
                            // that needs fixed
                            //
                            // ->searchable()
                            // ->options(
                            //     auth()->user()->isAdministrator()
                            //         ? PresentationFilter::array()
                            //         : []
                            // )
                            // ->getSearchResultsUsing(function (string $search) {
                            //     return Presentation::forUser()
                            //         ->where('title', 'ilike', "%{$search}%")
                            //         ->limit(20)
                            //         ->pluck('title', 'id')
                            //         ->toArray();
                            // })->getOptionLabelUsing(function ($value): ?string {
                            //     return Presentation::forUser()
                            //         ->find(intval($value))?->title;
                            // }),
                            ->options(
                                auth()->user()->isAdministrator()
                                    ? PresentationFilter::array()
                                    : Presentation::forUser()
                                        ->pluck('title', 'id')
                                        ->toArray()
                            ),
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->native(false)
                            ->maxDate(fn (Get $get): ?string => $get('end_date') ?? now()->subDay())
                            ->hintIcon('heroicon-o-information-circle', tooltip: 'Only affects "Date Range" stats')
                            ->default(now()->subDays(8)),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->native(false)
                            ->minDate(fn (Get $get): ?string => $get('start_date'))
                            ->default(now()->subDay())
                            ->hintIcon('heroicon-o-information-circle', tooltip: 'Only affects "Date Range" stats')
                            ->maxDate(now()->subDay()),
                    ])
                    ->columns(3),
            ]);
    }
}
