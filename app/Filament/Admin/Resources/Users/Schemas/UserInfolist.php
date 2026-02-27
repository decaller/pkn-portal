<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

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
                    ->description('Personal details and roles.')
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

                Section::make('Event Involvement')
                    ->description('Events this user is currently registered for or has attended in the past.')
                    ->schema([
                        TextEntry::make('registered_events')
                            ->label('Active Registrations')
                            ->state(function (User $record): string {
                                // We are looking for registrations where this user is the booker,
                                // OR where they are listed as a participant.
                                $events = \App\Models\Event::whereHas('registrations', function ($query) use ($record) {
                                    $query->where('booker_user_id', $record->id)
                                        ->orWhereHas('participants', function ($participantQuery) use ($record) {
                                            $participantQuery->where('user_id', $record->id);
                                        });
                                })->get();

                                if ($events->isEmpty()) {
                                    return '-';
                                }

                                return $events->map(fn ($event) => $event->title)->join(', ');
                            })
                            ->placeholder('-'),

                        TextEntry::make('past_events')
                            ->label('Previously Attended')
                            ->state(function (User $record): string {
                                if (empty($record->past_events)) {
                                    return '-';
                                }

                                $pastEvents = \App\Models\Event::whereIn('id', $record->past_events)->get();

                                return $pastEvents->map(fn ($event) => $event->title)->join(', ');
                            })
                            ->placeholder('-'),
                    ])->columns(2),
            ]);
    }
}
