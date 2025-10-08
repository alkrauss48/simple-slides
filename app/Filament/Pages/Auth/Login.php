<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    protected function getRedirectUrl(): string
    {
        $request = request();

        // If returnTo parameter is provided, redirect there after successful login
        if ($request->has('returnTo')) {
            return $request->get('returnTo');
        }

        return parent::getRedirectUrl();
    }
}
