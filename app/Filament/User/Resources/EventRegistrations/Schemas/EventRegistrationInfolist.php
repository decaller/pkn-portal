<?php

namespace App\Filament\User\Resources\EventRegistrations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventRegistrationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Registration details')
                    ->schema([
                        TextEntry::make('event.title')->label('Event'),
                        TextEntry::make('organization.name')->label('Organization')->placeholder('Personal registration'),
                        TextEntry::make('status'),
                        TextEntry::make('payment_status'),
                        TextEntry::make('total_amount')->money('IDR'),
                        TextEntry::make('notes')->placeholder('-'),
                        TextEntry::make('created_at')->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }
}
