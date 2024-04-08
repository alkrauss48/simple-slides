<?php

namespace App\Filament\Resources\PresentationResource\Pages;

use App\Filament\Resources\PresentationResource;
use App\Jobs\GenerateThumbnail;
use App\Models\Presentation;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;
use Webbingbrasil\FilamentCopyActions\Pages\Actions\CopyAction;

class EditPresentation extends EditRecord
{
    protected static string $resource = PresentationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CopyAction::make('Copy Share URL')
                ->label('Copy Share URL')
                ->color('gray')
                ->copyable(fn (Presentation $record) => route('presentations.show', [
                    'user' => $record->user->username,
                    'slug' => $record->slug,
                ])),
            Actions\Action::make('View')
                ->color('gray')
                ->url(fn (Presentation $record): string => route('presentations.show', [
                    'user' => $record->user->username,
                    'slug' => $record->slug,
                ]))
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->openUrlInNewTab(),
            Actions\Action::make('Generate Thumbnail')
                ->icon('heroicon-o-camera')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Generate a thumbnail of your first slide')
                ->modalIcon('heroicon-o-camera')
                ->modalIconColor('info')
                ->modalDescription(new HtmlString(
                    'This will overwrite any existing thumbnail that you have '
                    .'set for this presentation. Do you wish to continue?'
                    .'<br><br><strong>Note:</strong> Your presentation must first be published.'
                ))->modalSubmitActionLabel('Generate it')
                ->action(function (Presentation $record) {
                    if (! $record->is_published) {
                        Notification::make()
                            ->title('You must publish your presentation to generate a thumbnail.')
                            ->danger()
                            ->send();

                        return;
                    }

                    // Note: If in the future we implement a websocket server,
                    // then we can implement an initial notification like this.
                    //
                    // Notification::make()
                    //     ->title('Hang tight for just a sec, your thumbnail is being generated.')
                    //     ->info()
                    //     ->send();

                    GenerateThumbnail::dispatch(
                        presentation: $record,
                        user: auth()->user(),
                    );
                }),
            Actions\Action::make('save')
                ->label('Save changes')
                ->action('save'),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
