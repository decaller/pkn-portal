<?php

namespace App\Filament\User\Resources\Users\Schemas;

use App\Models\Event;
use App\Models\RegistrationParticipant;
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
                                ->map(fn ($organization): string => $organization->name.' ('.$organization->pivot->role.')')
                                ->join(', ')
                            )
                            ->placeholder('-'),
                        TextEntry::make('joined_events')
                            ->label('Joined Events')
                            ->state(function (User $record): string {
                                $eventIds = collect($record->past_events ?? []);

                                $participantEventIds = RegistrationParticipant::where('user_id', $record->getKey())
                                    ->with('registration')
                                    ->get()
                                    ->pluck('registration.event_id');

                                $allEventIds = $eventIds->merge($participantEventIds)->unique()->filter();

                                if ($allEventIds->isEmpty()) {
                                    return null;
                                }

                                return Event::whereIn('id', $allEventIds)->pluck('title')->join(', ');
                            })
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
