<?php

namespace App\Filament\Resources\ImageUploadResource\Pages;

use App\Filament\Resources\ImageUploadResource;
use App\Filament\Resources\ImageUploadResource\Widgets\StatsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListImageUploads extends ListRecords
{
    protected static string $resource = ImageUploadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }
}
