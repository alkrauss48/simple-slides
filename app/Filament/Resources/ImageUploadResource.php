<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImageUploadResource\Pages;
use App\Models\ImageUpload;
use App\Models\User;
use Closure;
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
use Webbingbrasil\FilamentCopyActions\Tables\Actions\CopyAction;

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
            ->defaultSort('updated_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                SpatieMediaLibraryImageColumn::make('image')
                    ->collection('image'),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->hidden(fn () => ! auth()->user()->isAdministrator())
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->date()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    CopyAction::make('copyImageUrl')
                        ->label('Copy Image URL')
                        ->copyable(fn (ImageUpload $record): string => $record->getFirstMediaUrl('image')),
                    CopyAction::make('copyMarkdownUrl')
                        ->label('Copy Markdown URL')
                        ->copyable(fn (ImageUpload $record): string => $record->markdownUrl),
                ]),
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

    public static function getWidgets(): array
    {
        return [
            ImageUploadResource\Widgets\StatsOverview::class,
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
