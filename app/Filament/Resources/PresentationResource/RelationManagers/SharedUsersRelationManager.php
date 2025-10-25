<?php

namespace App\Filament\Resources\PresentationResource\RelationManagers;

use App\Enums\InviteStatus;
use App\Models\Presentation;
use App\Models\PresentationUser;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Notification as LaravelNotification;
use Illuminate\Support\Facades\RateLimiter;

class SharedUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'presentationUsers';

    protected static ?string $title = 'Collaborating Users';

    protected static ?string $description = 'Collaborating Users';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->email()
                    ->maxLength(255)
                    ->label('Email Address'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('email')
            ->emptyStateHeading(fn () => 'No users have been invited yet.')
            ->emptyStateDescription(fn () => 'Inviting users will allow them to edit this presentation.')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Name')
                    ->placeholder('Not yet registered'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email'),
                Tables\Columns\TextColumn::make('invite_status')
                    ->badge()
                    ->color(fn (InviteStatus $state): string => $state->color()),
                Tables\Columns\TextColumn::make('invited_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('accepted_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not accepted'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('invite_status')
                    ->options(InviteStatus::array())
                    ->multiple(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading('Invite User')
                    ->modalSubmitActionLabel('Invite')
                    ->createAnother(false)
                    ->label('Invite User')
                    ->form([
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->label('Email Address')
                            ->helperText('An invitation will be sent to this email address.'),
                    ])
                    ->using(function (array $data): PresentationUser {
                        // Check if user already exists
                        $user = User::where('email', $data['email'])->first();

                        /** @var Presentation $ownerRecord */
                        $ownerRecord = $this->ownerRecord;

                        // Create the invitation (observer will send notification)
                        $invitation = $ownerRecord->presentationUsers()->create([
                            'email' => $data['email'],
                            'user_id' => $user?->id,
                            'invite_status' => InviteStatus::PENDING,
                        ]);

                        Notification::make()
                            ->title('Invitation sent successfully')
                            ->success()
                            ->send();

                        return $invitation;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('resend')
                    ->label('Resend Invitation')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('gray')
                    ->visible(fn (PresentationUser $record): bool => $record->isPending)
                    ->action(function (PresentationUser $record): void {
                        $key = 'resend-invitation:'.$record->id;

                        // Check if rate limit has been exceeded
                        if (RateLimiter::tooManyAttempts($key, 1)) {
                            $seconds = RateLimiter::availableIn($key);

                            Notification::make()
                                ->title('Please wait before resending')
                                ->body("You can resend this invitation again in {$seconds} seconds.")
                                ->warning()
                                ->send();

                            return;
                        }

                        // Hit the rate limiter (1 attempt per 60 seconds)
                        RateLimiter::hit($key, 60);

                        if ($record->user_id) {
                            $record->user->notify(new \App\Notifications\PresentationUserCreated($record));
                        } else {
                            LaravelNotification::route('mail', $record->email)
                                ->notify(new \App\Notifications\PresentationUserCreated($record));
                        }

                        Notification::make()
                            ->title('Invitation resent successfully')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label('Remove User'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
