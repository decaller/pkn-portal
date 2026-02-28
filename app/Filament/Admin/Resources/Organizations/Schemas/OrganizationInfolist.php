<?php

namespace App\Filament\Admin\Resources\Organizations\Schemas;

use App\Models\Organization;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrganizationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('Organization'))
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('slug'),
                    ImageEntry::make('logo')
                        ->disk('public')
                        ->label(__('Logo'))
                        ->circular(),
                    TextEntry::make('admin.name')->label(__('Admin')),
                    TextEntry::make('users_count')->label(__('Members')),
                    TextEntry::make('users')
                        ->label(__('User list'))
                        ->state(
                            fn (Organization $record): string => $record->users
                                ->map(
                                    fn ($user): string => $user->name.
                                        ' ['.
                                        $user->pivot->role.
                                        ']',
                                )
                                ->join(', '),
                        )
                        ->placeholder('-'),
                ])
                ->columns(2),
        ]);
    }
}
