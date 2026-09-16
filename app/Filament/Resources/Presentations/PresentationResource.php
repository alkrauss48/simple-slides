<?php

namespace App\Filament\Resources\Presentations;

use App\Filament\Resources\Presentations\Pages\CreatePresentation;
use App\Filament\Resources\Presentations\Pages\EditPresentation;
use App\Filament\Resources\Presentations\Pages\ListPresentations;
use App\Filament\Resources\Presentations\RelationManagers\SharedUsersRelationManager;
use App\Filament\Resources\Presentations\Schemas\PresentationForm;
use App\Filament\Resources\Presentations\Tables\PresentationsTable;
use App\Models\Presentation;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * @extends resource<Presentation>
 */
class PresentationResource extends Resource
{
    protected static ?string $model = Presentation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Main';

    public static function form(Schema $schema): Schema
    {
        return PresentationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PresentationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SharedUsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPresentations::route('/'),
            'create' => CreatePresentation::route('/create'),
            'edit' => EditPresentation::route('/{record}/edit'),
        ];
    }

    /**
     * Modify the base eloquent table query.
     *
     * @return Builder<Presentation>
     */
    public static function getEloquentQuery(): Builder
    {
        return Presentation::query()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->forUser();
    }
}
