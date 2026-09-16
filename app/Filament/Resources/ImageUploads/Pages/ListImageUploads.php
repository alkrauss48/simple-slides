<?php

namespace App\Filament\Resources\ImageUploads\Pages;

use App\Filament\Resources\ImageUploads\ImageUploadResource;
use App\Filament\Resources\ImageUploads\Widgets\StatsOverview;
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
