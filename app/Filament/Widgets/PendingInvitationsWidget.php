<?php

namespace App\Filament\Widgets;

use App\Enums\InviteStatus;
use App\Models\PresentationUser;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class PendingInvitationsWidget extends Widget
{
    protected static string $view = 'filament.widgets.pending-invitations';

    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        $pendingInvitations = PresentationUser::where('email', Auth::user()->email)
            ->where('invite_status', InviteStatus::PENDING)
            ->with(['presentation.user'])
            ->get();

        return [
            'pendingInvitations' => $pendingInvitations,
        ];
    }

    public static function canView(): bool
    {
        // Only show the widget if there are pending invitations
        return PresentationUser::where('email', Auth::user()->email)
            ->where('invite_status', InviteStatus::PENDING)
            ->exists();
    }

    public function acceptInvitation(PresentationUser $invitation): void
    {
        $invitation->accept();

        \Filament\Notifications\Notification::make()
            ->title('Invitation accepted!')
            ->success()
            ->send();

        // Redirect to the presentation edit page
        $this->redirect(route('filament.admin.resources.presentations.edit', [
            'record' => $invitation->presentation_id,
        ]));
    }

    public function rejectInvitation(PresentationUser $invitation): void
    {
        $invitation->reject();

        \Filament\Notifications\Notification::make()
            ->title('Invitation rejected')
            ->success()
            ->send();
    }
}
