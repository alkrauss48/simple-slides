<?php

namespace App\Filament\Widgets;

use App\Models\PresentationUser;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class PendingInvitationsWidget extends Widget
{
    // v4 renders widgets lazily by default; these were eager in v3, and
    // lazy placeholders also hide widget errors from page-level tests.
    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.pending-invitations';

    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        $pendingInvitations = PresentationUser::pendingForCurrentUser()
            ->with(['presentation.user'])
            ->get();

        return [
            'pendingInvitations' => $pendingInvitations,
        ];
    }

    public static function canView(): bool
    {
        // Only show the widget if there are pending invitations
        return PresentationUser::pendingForCurrentUser()->exists();
    }

    public function acceptInvitation(PresentationUser $invitation): void
    {
        $this->authorizeInvitation($invitation);

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
        $this->authorizeInvitation($invitation);

        $invitation->reject();

        Notification::make()
            ->title('Invitation rejected')
            ->success()
            ->send();
    }

    /**
     * Livewire resolves these arguments straight from a client-supplied ID, so
     * every entry point has to re-check that the invitation is one the current
     * user was actually sent — otherwise accept() would claim it for them.
     */
    protected function authorizeInvitation(PresentationUser $invitation): void
    {
        abort_unless(
            PresentationUser::pendingForCurrentUser()
                ->whereKey($invitation->getKey())
                ->exists(),
            403,
        );
    }
}
