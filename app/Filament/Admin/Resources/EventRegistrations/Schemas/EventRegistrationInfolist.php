<?php

namespace App\Filament\Admin\Resources\EventRegistrations\Schemas;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\EventRegistration;
use App\Models\RegistrationParticipant;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions as SchemaActions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EventRegistrationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('Registration details'))
                ->schema([
                    TextEntry::make('booker.name')
                        ->label(__('Booked by')),
                    TextEntry::make('event.title')->label(__('Event')),
                    TextEntry::make('organization.name')
                        ->label(__('Organization'))
                        ->placeholder(__('Personal registration')),
                    TextEntry::make('created_at')->dateTime(),
                    TextEntry::make('status')
                        ->badge()
                        ->formatStateUsing(fn (RegistrationStatus|string|null $state): string => $state instanceof RegistrationStatus ? $state->getLabel() : RegistrationStatus::tryFrom((string) $state)?->getLabel() ?? '-')
                        ->color(fn (RegistrationStatus|string|null $state): string => $state instanceof RegistrationStatus ? $state->getColor() : RegistrationStatus::tryFrom((string) $state)?->getColor() ?? 'gray'),
                    TextEntry::make('payment_status')
                        ->badge()
                        ->formatStateUsing(fn (PaymentStatus|string|null $state): string => $state instanceof PaymentStatus ? $state->getLabel() : PaymentStatus::tryFrom((string) $state)?->getLabel() ?? '-')
                        ->color(fn (PaymentStatus|string|null $state): string => $state instanceof PaymentStatus ? $state->getColor() : PaymentStatus::tryFrom((string) $state)?->getColor() ?? 'gray'),

                    TextEntry::make('verifier.name')->label(__('Verified By'))->placeholder('-'),
                    TextEntry::make('participants_count')
                        ->label(__('Total Participants'))
                        ->getStateUsing(function (EventRegistration $record): string {
                            $added = (int) ($record->participants_count ?? $record->participants()->count());
                            $max = (int) collect($record->package_breakdown ?? [])->sum('participant_count');

                            return "{$added} of {$max}";
                        }),
                    TextEntry::make('total_amount')->money('IDR'),
                    TextEntry::make('notes')->placeholder('-'),
                ])
                ->columns(2),
            Section::make(__('Payment Proof'))
                ->schema([
                    ImageEntry::make('payment_proof_path')
                        ->label('Payment Document / Receipt')
                        ->hiddenLabel()
                        ->placeholder(__('No payment proof uploaded yet.'))
                        ->columnSpanFull()
                        ->size(400)
                        ->visible(fn ($record) => ! $record->payment_proof_path || ! str_ends_with(strtolower((string) $record->payment_proof_path), '.pdf')),

                    ViewEntry::make('payment_proof_pdf')
                        ->statePath('payment_proof_path')
                        ->label('Payment Document / Receipt')
                        ->hiddenLabel()
                        ->view('filament.components.pdf-viewer')
                        ->columnSpanFull()
                        ->visible(fn ($record) => $record->payment_proof_path && str_ends_with(strtolower((string) $record->payment_proof_path), '.pdf')),
                ]),
            Section::make(function (EventRegistration $record): string {
                    $added = (int) ($record->participants_count ?? $record->participants()->count());
                    $max = (int) collect($record->package_breakdown ?? [])->sum('participant_count');

                    return "Participants ({$added} of {$max})";
                })
                ->schema([
                    RepeatableEntry::make('participants')
                        ->label('')
                        ->schema([
                            TextEntry::make('name')->weight('bold'),
                            TextEntry::make('phone')
                                ->label(__('Phone'))
                                ->placeholder('-'),
                            TextEntry::make('email')
                                ->label(__('Email'))
                                ->placeholder('-'),
                            SchemaActions::make([
                                ActionGroup::make([
                                    Action::make('copy_password')
                                        ->label(__('Reset & copy credentials'))
                                        ->icon('heroicon-o-key')
                                        ->color('warning')
                                        ->requiresConfirmation()
                                        ->modalHeading(__('Reset participant password'))
                                        ->modalDescription(__('This will generate a new random password for this participant and copy their full credentials to your clipboard.'))
                                        ->visible(fn ($record): bool => $record->registration && auth()->user()->can('manageParticipants', $record->registration))
                                        ->action(function (RegistrationParticipant $record, $livewire): void {
                                            $user = $record->user;

                                            if (! $user) {
                                                Notification::make()
                                                    ->title(__('No user account linked to this participant.'))
                                                    ->warning()
                                                    ->send();

                                                return;
                                            }

                                            $newPassword = Str::random(12);
                                            $user->update(['password' => Hash::make($newPassword)]);

                                            $details = implode("\n", array_filter([
                                                "Name: {$user->name}",
                                                $user->phone_number ? "Phone: {$user->phone_number}" : null,
                                                $user->email && ! str_ends_with($user->email, '@participant.local')
                                                    ? "Email: {$user->email}"
                                                    : null,
                                                "Password: {$newPassword}",
                                            ]));

                                            // $escaped = json_encode($details);
                                            $escaped = str_replace(
                                                ["\r", "\n", "'"],
                                                ['', '\\n', "\\'"],
                                                $details
                                            );
                                            $livewire->js("navigator.clipboard.writeText('{$escaped}').catch(() => {})");

                                            Notification::make()
                                                ->title(__('Credentials copied to clipboard'))
                                                ->body("Password reset for **{$user->name}**. Full credentials have been copied.")
                                                ->success()
                                                ->persistent()
                                                ->send();
                                        }),

                                    Action::make('edit_participant')
                                        ->label(__('Edit details'))
                                        ->icon('heroicon-o-pencil-square')
                                        ->color('info')
                                        ->visible(fn ($record): bool => $record->registration && auth()->user()->can('manageParticipants', $record->registration))
                                        ->fillForm(function (RegistrationParticipant $record): array {
                                            $user = $record->user;

                                            return [
                                                'name' => $user?->name ?? $record->name ?? '',
                                                'phone_number' => $user?->phone_number ?? $record->phone ?? '',
                                                'email' => $user?->email ?? $record->email ?? '',
                                            ];
                                        })
                                        ->form([
                                            TextInput::make('name')
                                                ->label(__('Full name'))
                                                ->required()
                                                ->maxLength(255),
                                            TextInput::make('phone_number')
                                                ->label(__('Phone number'))
                                                ->maxLength(30),
                                            TextInput::make('email')
                                                ->label(__('Email'))
                                                ->email()
                                                ->maxLength(255),
                                        ])
                                        ->action(function (RegistrationParticipant $record, array $data): void {
                                            $user = $record->user;

                                            if ($user) {
                                                $user->update([
                                                    'name' => $data['name'],
                                                    'phone_number' => $data['phone_number'] ?: null,
                                                ]);
                                            }

                                            // Update the participant snapshot too
                                            $record->update([
                                                'name' => $data['name'],
                                                'phone' => $data['phone_number'] ?: null,
                                                'email' => $data['email'] ?: null,
                                            ]);

                                            Notification::make()
                                                ->title(__('Participant details updated.'))
                                                ->success()
                                                ->send();
                                        }),

                                    Action::make('delete_participant')
                                        ->label(__('Remove from event'))
                                        ->icon('heroicon-o-trash')
                                        ->color('danger')
                                        ->requiresConfirmation()
                                        ->visible(fn ($record): bool => $record->registration && auth()->user()->can('manageParticipants', $record->registration))
                                        ->modalHeading(__('Remove participant'))
                                        ->modalDescription(__('Are you sure you want to remove this participant from the event? This cannot be undone.'))
                                        ->action(function (RegistrationParticipant $record): void {
                                            $record->delete();

                                            Notification::make()
                                                ->title(__('Participant removed from event.'))
                                                ->success()
                                                ->send();
                                        }),
                                ])
                                    ->label(__('Actions'))
                                    ->icon('heroicon-m-chevron-down')
                                    ->iconPosition('after')
                                    ->button()
                                    ->color('gray')
                                    ->size('sm'),
                            ]),
                        ])
                        ->columns(4)
                        ->columnSpanFull(),
                ])->columnSpanFull(),

            Section::make(__('Invoices'))
                ->visible(fn ($record): bool => auth()->user()->can('viewInvoice', $record))
                ->schema([
                    RepeatableEntry::make('invoices')
                        ->label('')
                        ->schema([
                            TextEntry::make('invoice_number')
                                ->label(__('Invoice #'))
                                ->weight('bold'),
                            TextEntry::make('status')
                                ->badge()
                                ->formatStateUsing(
                                    fn (
                                        InvoiceStatus|string|null $state,
                                    ): string => $state instanceof InvoiceStatus
                                        ? $state->getLabel()
                                        : InvoiceStatus::tryFrom(
                                            (string) $state,
                                        )?->getLabel() ?? '-',
                                )
                                ->color(
                                    fn (
                                        InvoiceStatus|string|null $state,
                                    ): string|array|null => $state instanceof InvoiceStatus
                                        ? $state->getColor()
                                        : InvoiceStatus::tryFrom(
                                            (string) $state,
                                        )?->getColor(),
                                ),
                            TextEntry::make('total_amount')
                                ->label(__('Total'))
                                ->money('IDR'),
                            TextEntry::make('issued_at')
                                ->label(__('Issued'))
                                ->dateTime(),
                            TextEntry::make('id')
                                ->label('PDF')
                                ->formatStateUsing(
                                    fn (): string => __('Download PDF'),
                                )
                                ->badge()
                                ->color('success')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->url(
                                    fn ($record): string => route(
                                        'invoices.download',
                                        ['invoice' => $record],
                                    ),
                                )
                                ->openUrlInNewTab(),
                        ])
                        ->columns(5)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

        ]);
    }
}
