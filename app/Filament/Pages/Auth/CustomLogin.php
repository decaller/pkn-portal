<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;


class CustomLogin extends BaseLogin
{
    protected string $view = 'filament-panels::pages.auth.login';
    /**
     * This replaces the 'email' input with a 'phone_number' input.
     */
    protected function getNameFormComponent(): Component
    {
        return TextInput::make('phone_number')
            ->label('Phone Number')
            ->placeholder('0812...')
            ->tel() // Shows the number pad on mobile devices
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['name' => 'phone_number']);
    }

    /**
     * This tells Laravel how to pull the credentials from the form
     * and match them against your User model.
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'phone_number' => $data['phone_number'],
            'password' => $data['password'],
        ];
    }
}