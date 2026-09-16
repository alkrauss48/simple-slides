<?php

namespace App\Filament\Resources\Presentations\Tables;

use App\Models\Presentation;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PresentationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('thumbnail')
                    ->visibility('public')
                    ->collection('thumbnail'),
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('slug')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                ToggleColumn::make('is_published')
                    ->label('Published')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->hidden(fn () => ! auth()->user()->isAdministrator())
                    ->sortable(),
                TextColumn::make('created_at')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('deleted_at')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // v4 defers filters behind an Apply button by default; these are
            // one-click toggles, so keep them applying immediately.
            ->deferFilters(false)
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('View')
                        ->url(fn (Presentation $record): string => route('presentations.show', [
                            'user' => $record->user->username,
                            'slug' => $record->slug,
                        ]))
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->openUrlInNewTab(),
                    EditAction::make(),
                    ReplicateAction::make()
                        ->beforeReplicaSaved(function (Presentation $replica, Presentation $record): void {
                            $replica->title = 'Copy of '.$record->title;
                            $replica->slug = 'copy-of-'.$record->slug;
                            $replica->is_published = false;
                        })
                        ->successNotificationTitle('Presentation replicated'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
