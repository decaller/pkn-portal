<?php

namespace App\Filament\Resources\EventRegistrations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventRegistrationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // Section::make("Registration details")
            //     ->schema([
            //         TextEntry::make("event.title")->label("Event"),
            //         TextEntry::make("booker.name")->label("Booker"),
            //         TextEntry::make("organization.name")
            //             ->label("Organization")
            //             ->placeholder("Personal registration"),
            //         TextEntry::make("package_name")
            //             ->label("Package")
            //             ->placeholder("-"),
            //         TextEntry::make("participant_count")->label(
            //             "Participant qty",
            //         ),
            //         TextEntry::make("unit_price")
            //             ->label("Unit price")
            //             ->money("IDR"),
            //         TextEntry::make("status")->badge(),
            //         TextEntry::make("payment_status")->badge(),
            //         TextEntry::make("total_amount")->money("IDR"),
            //         TextEntry::make("participants_count")->label(
            //             "Participants",
            //         ),
            //         TextEntry::make("payment_proof_path")->placeholder("-"),
            //         TextEntry::make("verified_at")
            //             ->dateTime()
            //             ->placeholder("-"),
            //         TextEntry::make("verifier.name")
            //             ->label("Verified by")
            //             ->placeholder("-"),
            //         TextEntry::make("notes")
            //             ->placeholder("-")
            //             ->columnSpanFull(),
            //         TextEntry::make("created_at")->dateTime(),
            //         TextEntry::make("updated_at")->dateTime(),
            //     ])
            //     ->columns(2),
        ]);
    }
}
