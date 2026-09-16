<?php

namespace App\Filament\Resources\ImageUploads\Pages;

use App\Filament\Resources\ImageUploads\ImageUploadResource;
use App\Models\ImageUpload;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Webbingbrasil\FilamentCopyActions\Actions\CopyAction;

class EditImageUpload extends EditRecord
{
    protected static string $resource = ImageUploadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                CopyAction::make('copyImageUrl')
                    ->label('Copy Image URL')
                    ->copyable(fn (ImageUpload $record): string => $record->getFirstMediaUrl('image')),
                CopyAction::make('copyMarkdownUrl')
                    ->label('Copy Markdown URL')
                    ->copyable(fn (ImageUpload $record): string => $record->markdownUrl),
                DeleteAction::make(),
            ]),
        ];
    }
}
