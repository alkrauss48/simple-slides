<?php

namespace App\Filament\Pages\Auth\Traits;

trait RemembersReturnUrl
{
    /**
     * Stash a `?returnTo=` target as the intended URL, so that Filament's
     * login and registration responses — which both end in
     * `redirect()->intended(...)` — land the user back where they started.
     *
     * Only same-origin URLs are accepted; `returnTo` arrives from the query
     * string, so an unvalidated value would be an open redirect.
     */
    protected function rememberReturnUrl(): void
    {
        $returnTo = request()->string('returnTo')->toString();

        if (blank($returnTo)) {
            return;
        }

        if (! str_starts_with($returnTo, url('/'))) {
            return;
        }

        redirect()->setIntendedUrl($returnTo);
    }
}
