<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                TextInput::make('username')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->rules(['string', 'lowercase', 'alpha_dash:ascii', 'max:255'])
                    ->helperText(
                        'NOTE: Changing this will change the URL of your '
                        .'presentations, so be careful here. '
                        .'i.e. /{username}/{presentation}'
                    ),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getCurrentPasswordFormComponent(),
            ]);
    }
}
