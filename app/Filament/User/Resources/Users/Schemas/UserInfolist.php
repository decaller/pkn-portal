<?php

namespace App\Filament\User\Resources\Users\Schemas;

use App\Models\User;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User details')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('phone_number')->label('Phone number'),
                        TextEntry::make('email'),
                        TextEntry::make('organizations')
                            ->label('Organizations')
                            ->state(fn (User $record): string => $record->organizations
                                ->map(fn ($organization): string => $organization->name . ' (' . $organization->pivot->role . ')')
                                ->join(', ')
                            )
                            ->placeholder('-'),
                    ])
                    ->columns(2),
            ]);
    }
}
