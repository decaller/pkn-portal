<?php

namespace App\Filament\User\Resources\Users\Schemas;

use App\Models\Organization;
use App\Models\User;
use Filament\Facades\Filament;
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
                            ->helperText(__('This email is used for communication and platform login.'))
                            ->maxLength(255)
                            ->unique(User::class, 'email', ignoreRecord: true),
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state)),
                        Toggle::make('is_super_admin')
                            ->label(__('Main admin'))
                            ->visible(fn (): bool => auth()->user()?->isMainAdmin()),
                    ])
                    ->columns(2),
                Section::make(__('Organizations'))
                    ->schema([
                        Select::make('organizations')
                            ->multiple()
                            ->relationship('organizations', 'name')
                            ->options(function () {
                                $user = auth()->user();
                                if ($user?->isMainAdmin()) {
                                    return Organization::query()->orderBy('name')->pluck('name', 'id');
                                }

                                return $user?->organizations()
                                    ->wherePivot('role', 'admin')
                                    ->pluck('name', 'organization_id');
                            })
                            ->default(fn () => [Filament::getTenant()?->getKey()])
                            ->searchable()
                            ->preload(),
                    ]),
            ]);
    }
}
