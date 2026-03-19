<?php

namespace App\Filament\User\Resources\EventRegistrations\Tables;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Filament\User\Resources\EventRegistrations\EventRegistrationResource;
use App\Models\EventRegistration;
use App\Services\Payments\InvoicePaymentService;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Throwable;

class EventRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->recordUrl(
                fn ($record): string => EventRegistrationResource::getUrl('view', [
                    'record' => $record,
                ]),
            )
            ->columns([
                TextColumn::make('event.title')
                    ->label(__('Event'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('organization.name')
                    ->label(__('Organization'))
                    ->placeholder(__('Personal')),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (RegistrationStatus|string|null $state): string => $state instanceof RegistrationStatus ? $state->getLabel() : RegistrationStatus::tryFrom((string) $state)?->getLabel() ?? '-')
                    ->color(fn (RegistrationStatus|string|null $state): string => $state instanceof RegistrationStatus ? $state->getColor() : RegistrationStatus::tryFrom((string) $state)?->getColor() ?? 'gray'),
                TextColumn::make('payment_status')
                    ->badge()
                    ->formatStateUsing(fn (PaymentStatus|string|null $state): string => $state instanceof PaymentStatus ? $state->getLabel() : PaymentStatus::tryFrom((string) $state)?->getLabel() ?? '-')
                    ->color(fn (PaymentStatus|string|null $state): string => $state instanceof PaymentStatus ? $state->getColor() : PaymentStatus::tryFrom((string) $state)?->getColor() ?? 'gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('pay_now')
                    ->label(fn (EventRegistration $record): string => ($record->latestInvoice?->hasActivePaymentAttempt() ?? false)
                        ? __('Continue Payment')
                        : __('Pay Now'))
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->visible(fn (EventRegistration $record): bool => $record->latestInvoice?->canStartGatewayPayment() ?? false)
                    ->action(function (EventRegistration $record, $livewire): void {
                        try {
                            $invoice = $record->latestInvoice;

                            if (! $invoice) {
                                throw new \RuntimeException('No invoice found');
                            }

                            $payment = app(InvoicePaymentService::class)->createOrReuseSnapPayment($invoice);

                            Notification::make()
                                ->title(__('Payment session created'))
                                ->success()
                                ->send();

                            $livewire->dispatch('open-midtrans-snap', token: $payment->snap_token);
                        } catch (Throwable) {
                            Notification::make()
                                ->title(__('Unable to start payment'))
                                ->danger()
                                ->send();
                        }
                    }),
                ViewAction::make(),
            ]);
    }
}
