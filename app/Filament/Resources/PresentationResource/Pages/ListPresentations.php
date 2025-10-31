<?php

namespace App\Filament\Resources\PresentationResource\Pages;

use App\Filament\Resources\PresentationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPresentations extends ListRecords
{
    protected static string $resource = PresentationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('View Public Profile')
                ->label('View Public Profile')
                ->url(fn (): string => route('profile.show', ['user' => auth()->user()?->username]))
                ->color('gray')
                ->icon('heroicon-o-eye')
                ->openUrlInNewTab(),
            Actions\CreateAction::make()
                ->icon('heroicon-m-plus')
                ->label('New Presentation'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\PendingInvitationsWidget::class,
        ];
    }
}
