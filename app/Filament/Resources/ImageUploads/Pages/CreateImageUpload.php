<?php

namespace App\Filament\Resources\ImageUploads\Pages;

use App\Filament\Resources\ImageUploads\ImageUploadResource;
use Filament\Resources\Pages\CreateRecord;

class CreateImageUpload extends CreateRecord
{
    protected static string $resource = ImageUploadResource::class;
}
