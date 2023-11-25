<?php

namespace App\Filament\Resources\ImageUploadResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        if (auth()->user()->isAdministrator()) {
            $size = number_format(
                num: Media::sum('size') / (1000 * 1000),
                decimals: 2,
            );

            return [
                Stat::make('Total Storage Space Used (in MB)', $size),
            ];
        }

        $limit = number_format(
            num: config('app-upload.limit') / (1000 * 1000),
            decimals: 0,
        );

        $size = number_format(
            num: auth()->user()->image_uploaded_size / (1000 * 1000),
            decimals: 2,
        );

        return [
            Stat::make('Storage Space Used (in MB)', "$size / $limit")
                ->description(
                    'This includes images in your library, as well as '.
                    'thumbnails from your presentations. If this goes over '.
                    'the limit, you won\'t be able to upload any more images.'
                ),
        ];
    }
}
