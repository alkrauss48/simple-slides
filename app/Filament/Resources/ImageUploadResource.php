<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImageUploadResource\Pages\CreateImageUpload;
use App\Filament\Resources\ImageUploadResource\Pages\EditImageUpload;
use App\Filament\Resources\ImageUploadResource\Pages\ListImageUploads;
use App\Filament\Resources\ImageUploadResource\Widgets\StatsOverview;
use App\Models\ImageUpload;
use App\Models\User;
use Closure;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webbingbrasil\FilamentCopyActions\Actions\CopyAction;

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
        return $schema
            ->components([
                Grid::make()
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image')
                            ->visibility('public')
                            ->columnSpan([
                                'md' => 2,
                            ])
                            ->collection('image')
                            ->required()
                            ->image()
                            ->imageEditor()
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, Closure $fail) {
                                        if (auth()->user()->can('upload', User::class)) {
                                            return;
                                        }

                                        $fail(config('app-upload.limit_exceeded_message'));
                                    };
                                },
                            ]),
                        Section::make('Details')
                            ->description('All the metadata related to your image.')
                            ->columnSpan(1)
                            ->schema([
                                TextInput::make('title')
                                    ->helperText('This is your title for the image. No one else will see this.')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('alt_text')
                                    ->helperText('This is the text that will be read by screen readers, or if the image can\'t be displayed. It\'s required because it\'s the right thing to do.')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->hidden(fn () => ! auth()->user()->isAdministrator())
                                    ->searchable()
                                    ->required(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                SpatieMediaLibraryImageColumn::make('image')
                    ->visibility('public')
                    ->collection('image'),
                TextColumn::make('user.name')
                    ->numeric()
                    ->hidden(fn () => ! auth()->user()->isAdministrator())
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->date()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    CopyAction::make('copyImageUrl')
                        ->label('Copy Image URL')
                        ->copyable(fn (ImageUpload $record): string => $record->getFirstMediaUrl('image')),
                    CopyAction::make('copyMarkdownUrl')
                        ->label('Copy Markdown URL')
                        ->copyable(fn (ImageUpload $record): string => $record->markdownUrl),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
