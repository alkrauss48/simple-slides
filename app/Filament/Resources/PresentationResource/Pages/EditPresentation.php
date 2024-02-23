<?php

namespace App\Filament\Resources\PresentationResource\Pages;

use App\Filament\Resources\PresentationResource;
use App\Models\Presentation;
use Filament\Actions;
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
                ->action(function (Presentation $record) {
                    Browsershot::url(route('presentations.show', [
                        'user' => $record->user->username,
                        'slug' => $record->slug,
                    ]))

                        ->setDelay(250)
                        ->windowSize(1200, 630)
                        ->setOption('addStyleTag', json_encode([
                            'content' => '.slide-view { height: 100vh !important; } '
                                .'.browsershot-hide { display: none !important; }',
                        ]))->noSandbox()
                        ->setScreenshotType('jpeg', 90)
                        ->save(storage_path("app/public/{$record->user->username}-{$record->slug}.jpg"));
                }),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
