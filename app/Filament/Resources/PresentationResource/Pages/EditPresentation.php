<?php

namespace App\Filament\Resources\PresentationResource\Pages;

use App\Filament\Resources\PresentationResource;
use App\Models\Presentation;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Webbingbrasil\FilamentCopyActions\Pages\Actions\CopyAction;

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
            Actions\Action::make('save')
                ->label('Save changes')
                ->action('save'),
            Actions\ActionGroup::make([
                Actions\Action::make('view')
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
                Actions\DeleteAction::make(),
                Actions\ForceDeleteAction::make(),
                Actions\RestoreAction::make(),
            ])
                ->color('gray')
                ->button()
                ->label('More'),
        ];
    }
}
