<?php

namespace App\Filament\Resources\PresentationResource\RelationManagers;

use App\Enums\InviteStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SharedUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'sharedUsers';

    protected static ?string $title = 'Collaborating Users';

    protected static ?string $description = 'Collaborating Users';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('invite_status')
                    ->badge()
                    ->color(fn (InviteStatus $state): string => $state->color()),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Invite User')
                    ->multiple(),

            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label('Remove User'),
            ])
            ->bulkActions([
                //
            ]);
    }
}
