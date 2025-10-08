<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{
    public function mount(): void
    {
        parent::mount();

        // Pre-fill email if provided in query parameters
        $request = request();
        if ($request->has('email')) {
            $this->form->fill(['email' => $request->get('email')]);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                TextInput::make('username')
                    ->unique('users', 'username')
                    ->rules('required|string|lowercase|alpha_dash:ascii|max:255')
                    ->required()
                    ->maxLength(255),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function getRedirectUrl(): string
    {
        $request = request();

        // If returnTo parameter is provided, redirect there after successful registration
        if ($request->has('returnTo')) {
            return $request->get('returnTo');
        }

        return parent::getRedirectUrl();
    }
}
