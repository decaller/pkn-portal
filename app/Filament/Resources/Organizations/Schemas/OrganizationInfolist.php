<?php

namespace App\Filament\Resources\Organizations\Schemas;

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
            Section::make("Organization")
                ->schema([
                    TextEntry::make("name"),
                    TextEntry::make("slug"),
                    ImageEntry::make("logo")
                        ->disk("public")
                        ->label("Logo")
                        ->circular(),
                    TextEntry::make("admin.name")->label("Admin"),
                    TextEntry::make("users_count")->label("Members"),
                    TextEntry::make("users")
                        ->label("User list")
                        ->state(
                            fn(Organization $record): string => $record->users
                                ->map(
                                    fn($user): string => $user->name .
                                        " [" .
                                        $user->pivot->role .
                                        "]",
                                )
                                ->join(", "),
                        )
                        ->placeholder("-"),
                ])
                ->columns(2),
        ]);
    }
}
