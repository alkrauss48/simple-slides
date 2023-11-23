<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImageUploadResource\Pages;
use App\Models\ImageUpload;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ImageUploadResource extends Resource
{
    protected static ?string $label = 'Image';

    protected static ?string $navigationLabel = 'Image Library';

    protected static ?string $model = ImageUpload::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Extras';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->columns(3)
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image')
                            ->columnSpan([
                                'md' => 2,
                            ])
                            ->collection('image')
                            ->required()
                            ->image()
                            ->imageEditor()
                            ->preserveFilenames(),
                        Section::make('Details')
                            ->description('All the metadata related to your image.')
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->helperText('This is your title for the image. No one else will see this.')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('alt_text')
                                    ->helperText('This is the text that will be read by screen readers, or if the image can\'t be displayed. It\'s required because it\'s the right thing to do.')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('user_id')
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
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                SpatieMediaLibraryImageColumn::make('image')->collection('image'),
                Tables\Columns\TextColumn::make('foo')
                    ->label('Markdown URL')
                    ->badge()
                    ->getStateUsing(function (ImageUpload $record) {
                        return 'Copy Markdown URL';
                    })
                    ->copyable()
                    ->copyableState(fn (ImageUpload $record): string => $record->markdownUrl),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->hidden(fn () => ! auth()->user()->isAdministrator())
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListImageUploads::route('/'),
            'create' => Pages\CreateImageUpload::route('/create'),
            'edit' => Pages\EditImageUpload::route('/{record}/edit'),
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
