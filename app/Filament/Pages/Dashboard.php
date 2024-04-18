<?php

namespace App\Filament\Pages;

use App\Enums\PresentationFilter;
use App\Models\Presentation;
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

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('presentation_id')
                            ->label('Presentation')
                            ->searchable()
                            ->options(
                                auth()->user()->isAdministrator()
                                    ? PresentationFilter::array()
                                    : []
                            )->getSearchResultsUsing(function (string $search) {
                                return Presentation::forUser()
                                    ->where('title', 'ilike', "%{$search}%")
                                    ->limit(20)
                                    ->pluck('title', 'id')
                                    ->toArray();
                            })->getOptionLabelUsing(function ($value): ?string {
                                return Presentation::forUser()
                                    ->find(intval($value))?->title;
                            }),
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
