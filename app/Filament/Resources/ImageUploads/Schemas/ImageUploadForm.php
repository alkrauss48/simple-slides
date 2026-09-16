<?php

namespace App\Filament\Resources\ImageUploads\Schemas;

use App\Models\User;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ImageUploadForm
{
    public static function configure(Schema $schema): Schema
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
}
