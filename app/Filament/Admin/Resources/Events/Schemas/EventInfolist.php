<?php

namespace App\Filament\Admin\Resources\Events\Schemas;

use App\Filament\Shared\Schemas\EventInfolist as SharedEventInfolist;
use App\Models\Event;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions as SchemaActions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventInfolist
{
    private static function registrationUrl(int|string|null $registrationId): ?string
    {
        if (blank($registrationId)) {
            return null;
        }

        return route('filament.admin.resources.event-registrations.view', [
            'tenant' => request()->route('tenant'),
            'record' => $registrationId,
        ]);
    }

    public static function participantsTableRows(Event $record): array
    {
        return $record->registrations()
            ->with(['participants.user', 'organization', 'booker'])
            ->get()
            ->flatMap(function ($registration) {
                $rows = [];
                $participants = $registration->participants;

                foreach ($participants as $participant) {
                    $name = $participant->name ?: $participant->user?->name;
                    $email = $participant->email ?: $participant->user?->email;
                    $phone = $participant->phone ?: $participant->user?->phone_number;
                    $isDetailMissing = blank($name) || blank($phone);

                    $rows[] = [
                        'participant_name' => $isDetailMissing ? __('participant detail havent been added') : $name,
                        'participant_email' => $email,
                        'participant_phone' => $isDetailMissing ? __('participant detail havent been added') : $phone,
                        'organization_name' => $registration->organization?->name,
                        'booker_name' => $registration->booker?->name,
                        'registration_id' => $registration->id,
                    ];
                }

                $quota = (int) collect($registration->package_breakdown ?? [])->sum(
                    fn (array $row): int => (int) ($row['participant_count'] ?? 0),
                );
                $placeholderCount = $quota > 0 ? max($quota - $participants->count(), 0) : 0;

                if ($participants->isEmpty()) {
                    $placeholderCount = max($placeholderCount, 1);
                }

                for ($i = 0; $i < $placeholderCount; $i++) {
                    $rows[] = [
                        'participant_name' => __('participant detail havent been added'),
                        'participant_email' => null,
                        'participant_phone' => __('participant detail havent been added'),
                        'organization_name' => $registration->organization?->name,
                        'booker_name' => $registration->booker?->name,
                        'registration_id' => $registration->id,
                    ];
                }

                return $rows;
            })
            ->values()
            ->all();
    }

    public static function configure(Schema $schema): Schema
    {
        // 1. Pass a fresh Schema instance to your shared class
        // and extract the configured components as an array.
        $sharedComponents = SharedEventInfolist::configure(Schema::make())->getComponents(withHidden: true);

        // 2. Spread those extracted components into your new schema
        return $schema
            ->components([
                ...$sharedComponents,

                // 3. Add your new Admin-specific items right below it
                // Section::make(__('Admin Extras'))->schema([
                //     TextEntry::make('admin_notes')->label(__('Admin Notes')),
                //     TextEntry::make('created_at')->label(__('Created At'))->dateTime(),
                // ]),

                Section::make(__('Event Participants'))
                    ->schema([
                        SchemaActions::make([
                            Action::make('download_all_participants')
                                ->label(__('Download all participants'))
                                ->icon('heroicon-m-arrow-down-tray')
                                ->color('primary')
                                ->url(fn (Event $record): string => route('admin.events.participants.download', ['event' => $record])),
                        ]),
                        RepeatableEntry::make('participants_list')
                            ->hiddenLabel()
                            ->getStateUsing(fn (Event $record): array => self::participantsTableRows($record))
                            ->table([
                                TableColumn::make(__('Name')),
                                TableColumn::make(__('Email')),
                                TableColumn::make(__('Phone')),
                                TableColumn::make(__('Organization')),
                                TableColumn::make(__('Booked By')),
                                TableColumn::make(__('Registration')),
                            ])
                            ->schema([
                                TextEntry::make('participant_name')
                                    ->url(function ($state, $record): ?string {
                                        $registrationId = is_array($record)
                                            ? ($record['registration_id'] ?? null)
                                            : ($record->registration_id ?? null);

                                        return self::registrationUrl($registrationId);
                                    })
                                    ->color('primary')
                                    ->openUrlInNewTab()
                                    ->placeholder('-'),
                                TextEntry::make('participant_email')->placeholder('-'),
                                TextEntry::make('participant_phone')->placeholder('-'),
                                TextEntry::make('organization_name')->placeholder(__('Personal Registration')),
                                TextEntry::make('booker_name')->placeholder('-'),
                                TextEntry::make('registration_id')
                                    ->formatStateUsing(fn ($state): string => __('View #:id', ['id' => $state]))
                                    ->url(fn ($state): ?string => self::registrationUrl($state))
                                    ->color('primary')
                                    ->openUrlInNewTab(),
                            ]),
                    ])
                    ->visible(fn (Event $record): bool => $record->registrations()->exists())
                    ->columnSpanFull(),
            ]);
    }
}
