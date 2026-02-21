<?php

namespace App\Filament\User\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone_number')
                            ->label('Phone number')
                            ->required()
                            ->maxLength(30)
                            ->unique(User::class, 'phone_number', ignoreRecord: true),
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->unique(User::class, 'email', ignoreRecord: true)
                            ->disabled(fn () => ! auth()->user()?->isMainAdmin())
                            ->dehydrated(fn () => auth()->user()?->isMainAdmin()),
                    ])
                    ->columns(2),
            ]);
    }
}
