<?php

namespace App\Filament\Resources\Presentations\RelationManagers;

use App\Enums\InviteStatus;
use App\Models\Presentation;
use App\Models\PresentationUser;
use App\Models\User;
use App\Notifications\PresentationUserCreated;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Notification as LaravelNotification;
use Illuminate\Support\Facades\RateLimiter;

class SharedUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'presentationUsers';

    protected static ?string $title = 'Collaborating Users';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
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
                TextColumn::make('user.name')
                    ->label('Name')
                    ->placeholder('Not yet registered'),
                TextColumn::make('email')
                    ->label('Email'),
                TextColumn::make('invite_status')
                    ->badge()
                    ->color(fn (InviteStatus $state): string => $state->color()),
                TextColumn::make('invited_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('accepted_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not accepted'),
            ])
            // v4 defers filters behind an Apply button by default; these are
            // one-click toggles, so keep them applying immediately.
            ->deferFilters(false)
            ->filters([
                SelectFilter::make('invite_status')
                    ->options(InviteStatus::array())
                    ->multiple(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalHeading('Invite User')
                    ->modalSubmitActionLabel('Invite')
                    ->createAnother(false)
                    ->label('Invite User')
                    ->schema([
                        TextInput::make('email')
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
            ->recordActions([
                Action::make('resend')
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
                            $record->user->notify(new PresentationUserCreated($record));
                        } else {
                            LaravelNotification::route('mail', $record->email)
                                ->notify(new PresentationUserCreated($record));
                        }

                        Notification::make()
                            ->title('Invitation resent successfully')
                            ->success()
                            ->send();
                    }),
                DeleteAction::make()
                    ->label('Remove User'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
