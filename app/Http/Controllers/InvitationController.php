<?php

namespace App\Http\Controllers;

use App\Enums\InviteStatus;
use App\Models\PresentationUser;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class InvitationController extends Controller
{
    /**
     * How long an emailed invitation link stays valid.
     */
    private const EXPIRY_DAYS = 7;

    /**
     * Show the invitation acceptance page.
     */
    public function show(Request $request, string $token): RedirectResponse
    {
        $invitation = PresentationUser::where('invite_token', $token)
            ->where('invite_status', InviteStatus::PENDING)
            ->first();

        if (! $invitation) {
            abort(404, 'Invitation not found or has already been processed.');
        }

        // Check if user is already logged in
        if (Auth::check()) {
            return $this->acceptInvitation($invitation);
        }

        // Check if user exists with the invitation email
        $user = User::where('email', $invitation->email)->first();

        if ($user) {
            // User exists but is not logged in - redirect to login with return URL
            return redirect()->route('filament.admin.auth.login', [
                'returnTo' => $this->signedAcceptUrl($request, $token),
            ])->with('error', 'Please log in to accept this invitation.');
        }

        // User doesn't exist - redirect to registration with return URL
        return redirect()->route('filament.admin.auth.register', [
            'email' => $invitation->email,
            'returnTo' => $this->signedAcceptUrl($request, $token),
        ])->with('info', 'Please create an account to accept this invitation.');
    }

    /**
     * Accept the invitation.
     */
    public function accept(Request $request, string $token): RedirectResponse
    {
        $invitation = PresentationUser::where('invite_token', $token)
            ->where('invite_status', InviteStatus::PENDING)
            ->first();

        if (! $invitation) {
            abort(404, 'Invitation not found or has already been processed.');
        }

        // If user is not logged in, redirect to login with return URL
        if (! Auth::check()) {
            return redirect()->route('filament.admin.auth.login', [
                'returnTo' => $this->signedAcceptUrl($request, $token),
            ])->with('error', 'Please log in to accept this invitation.');
        }

        // Check if the logged-in user's email matches the invitation email
        if (Auth::user()->email !== $invitation->email) {
            return redirect()->route('filament.admin.auth.login', [
                'returnTo' => $this->signedAcceptUrl($request, $token),
            ])->with('error', 'Please log in with the email address that received this invitation.');
        }

        return $this->acceptInvitation($invitation);
    }

    /**
     * Accept the invitation and redirect to the presentation edit page.
     */
    private function acceptInvitation(PresentationUser $invitation): RedirectResponse
    {
        // Accept the invitation
        $invitation->accept();

        // Redirect to the presentation edit page
        return redirect()->route('filament.admin.resources.presentations.edit', [
            'record' => $invitation->presentation_id,
        ])->with('success', 'Invitation accepted! You now have access to this presentation.');
    }

    /**
     * Generate a signed URL for accepting an invitation.
     */
    public static function generateAcceptUrl(PresentationUser $invitation): string
    {
        return URL::signedRoute('invitations.show', [
            'token' => $invitation->invite_token,
        ], now()->addDays(self::EXPIRY_DAYS));
    }

    /**
     * Build the accept URL handed to the login and registration pages.
     *
     * invitations.accept verifies signatures just like invitations.show, so a
     * bare route() URL here would bounce the user into a 403 the moment they
     * finished logging in. The expiry is carried over from the incoming request
     * rather than restarted, so detouring through login cannot extend an
     * invitation past the window the email promised.
     */
    private function signedAcceptUrl(Request $request, string $token): string
    {
        $expires = $request->query('expires');

        return URL::signedRoute(
            'invitations.accept',
            ['token' => $token],
            is_numeric($expires)
                ? Carbon::createFromTimestamp($expires)
                : now()->addDays(self::EXPIRY_DAYS),
        );
    }
}
