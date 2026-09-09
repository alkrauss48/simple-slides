<?php

namespace App\Filament\Resources\PresentationResource\Pages;

use App\Filament\Resources\PresentationResource;
use App\Models\Presentation;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Webbingbrasil\FilamentCopyActions\Actions\CopyAction;

class EditPresentation extends EditRecord
{
    protected static string $resource = PresentationResource::class;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save changes')
                ->action('save'),
            ActionGroup::make([
                Action::make('view')
                    ->label('View')
                    ->color('gray')
                    ->url(fn (Presentation $record): string => route('presentations.show', [
                        'user' => $record->user->username,
                        'slug' => $record->slug,
                    ]))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->openUrlInNewTab(),
                CopyAction::make('copyShareUrl')
                    ->label('Copy Share URL')
                    ->disabled(fn (Presentation $record) => ! $record->is_published)
                    ->color('gray')
                    ->copyable(fn (Presentation $record) => route('presentations.show', [
                        'user' => $record->user->username,
                        'slug' => $record->slug,
                    ])),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
                ->color('gray')
                ->button()
                ->label('More'),
        ];
    }
}
