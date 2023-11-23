<?php

namespace App\Livewire;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Jeffgreco13\FilamentBreezy\Livewire\PersonalInfo;

class UsernameComponent extends PersonalInfo
{
    protected string $view = 'livewire.username-component';

    /**
     * The order that this panel appears in the Breezy panel list.
     * Used in the PersonalInfo class.
     *
     * @var int
     */
    public static $sort = 11;

    /**
     * The fields that are used and saved in this component. Used in the
     * PersonalInfo class.
     *
     * @var array<int, string>
     */
    public array $only = ['username'];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('username')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->rules(['string', 'lowercase', 'alpha_dash:ascii', 'max:255'])
                    ->helperText(
                        'NOTE: Changing this will change the URL of your '
                        .'presentations, so be careful here. '
                        .'i.e. /{username}/{presentation}'
                    ),
            ])
            ->statePath('data');
    }
}
