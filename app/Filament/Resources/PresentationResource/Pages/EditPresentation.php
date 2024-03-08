<?php

namespace App\Filament\Resources\PresentationResource\Pages;

use App\Filament\Resources\PresentationResource;
use App\Models\Presentation;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Spatie\Browsershot\Browsershot;

class EditPresentation extends EditRecord
{
    protected static string $resource = PresentationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('View')
                ->url(fn (Presentation $record): string => route('presentations.show', [
                    'user' => $record->user->username,
                    'slug' => $record->slug,
                ]))
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->openUrlInNewTab(),
            Actions\Action::make('Generate Thumbnail')
                ->icon('heroicon-o-camera')
                ->requiresConfirmation()
                ->modalHeading('Generate thumbnail')
                ->modalDescription(
                    'This will overwrite any existing thumbnail that you have '
                    .'set for this presentation. Do you wish to continue?'
                )->modalSubmitActionLabel('Yes, generate it')
                ->action(function (Presentation $record) {
                    $tempPath = storage_path("temp/{$record->slug}-{$record->user->username}.jpg");

                    Browsershot::url(route('presentations.show', [
                        'user' => $record->user->username,
                        'slug' => $record->slug,
                    ]))
                        ->setChromePath('/usr/bin/chromium-browser')
                        ->waitUntilNetworkIdle()
                        ->windowSize(1200, 630)
                        ->setOption('args', ['--disable-web-security'])
                        ->setOption('addStyleTag', json_encode([
                            'content' => '.slide-view { height: 100vh !important; } '
                                .'.browsershot-hide { display: none !important; }',
                        ]))->noSandbox()
                        ->setScreenshotType('jpeg', 90)
                        ->save($tempPath);

                    $record->clearMediaCollection('thumbnail');
                    $record->addMedia($tempPath)->toMediaCollection('thumbnail');

                    Notification::make()
                        ->title('Thumbnail successfully generated. Refresh your page to view the new thumbnail.')
                        ->success()
                        ->send();
                }),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
