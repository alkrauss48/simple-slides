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
            Actions\Action::make('save')
                ->label('Save changes')
                ->action('save'),
            Actions\ActionGroup::make([
                Actions\Action::make('View')
                    ->color('gray')
                    ->url(fn (Presentation $record): string => route('presentations.show', [
                        'user' => $record->user->username,
                        'slug' => $record->slug,
                    ]))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->openUrlInNewTab(),
                CopyAction::make('Copy Share URL')
                    ->label('Copy Share URL')
                    ->disabled(fn (Presentation $record) => ! $record->is_published)
                    ->color('gray')
                    ->copyable(fn (Presentation $record) => route('presentations.show', [
                        'user' => $record->user->username,
                        'slug' => $record->slug,
                    ])),
                Actions\Action::make('Generate Thumbnail')
                    ->icon('heroicon-o-camera')
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

                        Notification::make()
                            ->title(
                                'Hang tight, your thumbnail is being generated in '
                                .'the background. Please refresh your browser in 5-10 '
                                .'seconds.'
                            )->info()
                            ->send();

                        GenerateThumbnail::dispatch(
                            presentation: $record,
                            user: auth()->user(),
                        );
                    }),
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
