<?php

namespace App\Filament\Pages\Auth;

use App\Filament\Pages\Auth\Traits\RemembersReturnUrl;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class Register extends BaseRegister
{
    use RemembersReturnUrl;

    public function mount(): void
    {
        parent::mount();

        $this->rememberReturnUrl();

        // Pre-fill email if provided in query parameters
        $request = request();
        if ($request->has('email')) {
            $this->form->fill(['email' => $request->get('email')]);
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                TextInput::make('username')
                    ->unique('users', 'username')
                    ->rules(['string', 'lowercase', 'alpha_dash:ascii', 'max:255'])
                    ->required()
                    ->maxLength(255),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }
}
