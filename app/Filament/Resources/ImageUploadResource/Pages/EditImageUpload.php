<?php

namespace App\Filament\Resources\ImageUploadResource\Pages;

use App\Filament\Resources\ImageUploadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImageUpload extends EditRecord
{
    protected static string $resource = ImageUploadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
