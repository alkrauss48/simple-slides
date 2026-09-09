<?php

namespace App\Filament\Widgets;

use App\Enums\InviteStatus;
use App\Models\PresentationUser;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class PendingInvitationsWidget extends Widget
{
    // v4 renders widgets lazily by default; these were eager in v3, and
    // lazy placeholders also hide widget errors from page-level tests.
    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.pending-invitations';

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

        Notification::make()
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

        Notification::make()
            ->title('Invitation rejected')
            ->success()
            ->send();
    }
}
