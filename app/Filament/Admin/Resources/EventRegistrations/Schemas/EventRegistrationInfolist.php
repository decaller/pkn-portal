<?php

namespace App\Filament\Admin\Resources\EventRegistrations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\ViewEntry;
use App\Enums\RegistrationStatus;
use App\Enums\PaymentStatus;
use App\Models\EventRegistration;
use App\Filament\Admin\Resources\Events\EventResource;


class EventRegistrationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Payment Context')
                ->schema([
                    TextEntry::make('booker.name')->label('Booker Name')->weight('bold'),
                    TextEntry::make('organization.name')->label('Organization')->placeholder('Personal Registration'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            RegistrationStatus::Draft => 'gray',
                            RegistrationStatus::PendingPayment => 'warning',
                            RegistrationStatus::Paid => 'success',
                            RegistrationStatus::Closed => 'info',
                            RegistrationStatus::Cancelled => 'danger',
                            default => 'primary',
                        }),
                    TextEntry::make('payment_status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            PaymentStatus::Unpaid => 'danger',
                            PaymentStatus::Submitted => 'warning',
                            PaymentStatus::Verified => 'success',
                            PaymentStatus::Rejected => 'danger',
                            default => 'primary',
                        }),
                    TextEntry::make('total_amount')->money('IDR')->weight('bold')->size('lg'),
                    TextEntry::make('verified_at')->dateTime()->placeholder('Unverified'),
                    TextEntry::make('verifier.name')->label('Verified By')->placeholder('-'),
                ])
                ->columns(3),

            Section::make('Event & Packages')
                ->schema([
                    TextEntry::make('event.title')->label('Event')->url(fn ($record) => EventResource::getUrl('edit', ['record' => $record->event_id])),
                    TextEntry::make('participants_count')->label('Total Participants'),
                ])
                ->columns(2),

            Section::make('Payment Proof')
                ->schema([
                    ImageEntry::make('payment_proof_path')
                        ->label('Payment Document / Receipt')
                        ->hiddenLabel()
                        ->placeholder('No payment proof uploaded yet.')
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
        ]);
    }
}
