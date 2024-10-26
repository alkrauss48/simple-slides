<?php

namespace App\Filament\Resources\ImageUploadResource\Pages;

use App\Filament\Resources\ImageUploadResource;
use App\Models\ImageUpload;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Webbingbrasil\FilamentCopyActions\Pages\Actions\CopyAction;

class EditImageUpload extends EditRecord
{
    protected static string $resource = ImageUploadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                CopyAction::make('copyImageUrl')
                    ->label('Copy Image URL')
                    ->copyable(fn (ImageUpload $record): string => $record->getFirstMediaUrl('image')),
                CopyAction::make('copyMarkdownUrl')
                    ->label('Copy Markdown URL')
                    ->copyable(fn (ImageUpload $record): string => $record->markdownUrl),
                Actions\DeleteAction::make(),
            ]),
        ];
    }
}
