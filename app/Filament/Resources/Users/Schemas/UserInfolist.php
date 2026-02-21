<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('phone_number')->label('Phone number'),
                        TextEntry::make('email'),
                        IconEntry::make('is_super_admin')
                            ->label('Main admin')
                            ->boolean(),
                        TextEntry::make('organizations')
                            ->label('Organizations')
                            ->state(fn (User $record): string => $record->organizations
                                ->map(fn ($organization): string => $organization->name)
                                ->join(', '))
                            ->placeholder('-'),
                    ])
                    ->columns(2),
            ]);
    }
}
