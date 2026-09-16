<?php

namespace App\Filament\Resources\ImageUploads;

use App\Filament\Resources\ImageUploads\Pages\CreateImageUpload;
use App\Filament\Resources\ImageUploads\Pages\EditImageUpload;
use App\Filament\Resources\ImageUploads\Pages\ListImageUploads;
use App\Filament\Resources\ImageUploads\Schemas\ImageUploadForm;
use App\Filament\Resources\ImageUploads\Tables\ImageUploadsTable;
use App\Filament\Resources\ImageUploads\Widgets\StatsOverview;
use App\Models\ImageUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends resource<ImageUpload>
 */
class ImageUploadResource extends Resource
{
    protected static ?string $label = 'Image';

    protected static ?string $navigationLabel = 'Image Library';

    protected static ?string $model = ImageUpload::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static string|\UnitEnum|null $navigationGroup = 'Extras';

    public static function form(Schema $schema): Schema
    {
        return ImageUploadForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ImageUploadsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListImageUploads::route('/'),
            'create' => CreateImageUpload::route('/create'),
            'edit' => EditImageUpload::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    /**
     * Modify the base eloquent table query.
     *
     * @return Builder<ImageUpload>
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (! auth()->user()->isAdministrator()) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }
}
