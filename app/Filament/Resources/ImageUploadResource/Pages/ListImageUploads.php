<?php

namespace App\Filament\Resources\ImageUploadResource\Pages;

use App\Filament\Resources\ImageUploadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImageUploads extends ListRecords
{
    protected static string $resource = ImageUploadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ImageUploadResource\Widgets\StatsOverview::class,
        ];
    }
}
