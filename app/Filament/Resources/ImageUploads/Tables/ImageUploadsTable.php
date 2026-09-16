<?php

namespace App\Filament\Resources\ImageUploads\Tables;

use App\Models\ImageUpload;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webbingbrasil\FilamentCopyActions\Actions\CopyAction;

class ImageUploadsTable
{
    public static function configure(Table $table): Table
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
}
