<?php

namespace App\Filament\Resources\PresentationResource\Pages;

use App\Filament\Resources\PresentationResource;
use App\Filament\Widgets\PendingInvitationsWidget;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPresentations extends ListRecords
{
    protected static string $resource = PresentationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('View Public Profile')
                ->label('View Public Profile')
                ->url(fn (): string => route('profile.show', ['user' => auth()->user()?->username]))
                ->color('gray')
                ->icon('heroicon-o-eye')
                ->openUrlInNewTab(),
            CreateAction::make()
                ->icon('heroicon-m-plus')
                ->label('New Presentation'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PendingInvitationsWidget::class,
        ];
    }
}
