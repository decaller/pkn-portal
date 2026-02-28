<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Models\Organization;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account')
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
                            ->required()
                            ->maxLength(255)
                            ->unique(User::class, 'email', ignoreRecord: true),
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state)),
                        Toggle::make('is_super_admin')
                            ->label('Main admin'),
                    ])
                    ->columns(2),
                Section::make('Organizations')
                    ->schema([
                        Select::make('organizations')
                            ->multiple()
                            ->relationship('organizations', 'name')
                            ->options(Organization::query()->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                    ]),
            ]);
    }
}
