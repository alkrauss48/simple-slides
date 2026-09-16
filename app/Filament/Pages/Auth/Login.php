<?php

namespace App\Filament\Pages\Auth;

use App\Filament\Pages\Auth\Traits\RemembersReturnUrl;
use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    use RemembersReturnUrl;

    public function mount(): void
    {
        parent::mount();

        $this->rememberReturnUrl();
    }
}
