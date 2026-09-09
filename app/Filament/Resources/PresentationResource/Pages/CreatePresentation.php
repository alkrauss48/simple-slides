<?php

namespace App\Filament\Resources\PresentationResource\Pages;

use App\Filament\Resources\PresentationResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreatePresentation extends CreateRecord
{
    protected static string $resource = PresentationResource::class;

    protected static bool $canCreateAnother = false;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->action('create'),
        ];
    }
}
