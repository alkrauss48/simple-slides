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
                ->icon('heroicon-o-presentation-chart-bar')
                ->openUrlInNewTab(),
            Actions\Action::make('Generate Thumbnail')
                ->icon('heroicon-o-camera')
                ->action(function (Presentation $record) {
                    // Browsershot::url('https://example.com')
                    Browsershot::url(route('presentations.show', [
                        'user' => $record->user->username,
                        'slug' => $record->slug,
                    ]))
                        ->windowSize(1200, 630)
                        ->noSandbox()
                        ->setScreenshotType('jpeg', 80)
                        ->save(storage_path("app/public/{$record->user->username}-{$record->slug}.jpg"));
                    // $set('price', $state);
                }),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
