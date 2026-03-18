<?php

namespace App\Filament\User\Resources\Invoices\Pages;

use App\Enums\InvoiceStatus;
use App\Filament\User\Resources\EventRegistrations\EventRegistrationResource;
use App\Filament\User\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use App\Services\Payments\InvoicePaymentService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Throwable;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pay_now')
                ->label(fn (Invoice $record): string => $record->hasActivePaymentAttempt()
                    ? __('Continue Payment')
                    : __('Pay Now'))
                ->icon('heroicon-o-credit-card')
                ->color('success')
                ->visible(fn (Invoice $record): bool => $record->status !== InvoiceStatus::Void && $record->canStartGatewayPayment())
                ->action(function (Invoice $record): void {
                    try {
                        $payment = app(InvoicePaymentService::class)->createOrReuseSnapPayment($record);

                        Notification::make()
                            ->title(__('Payment session created'))
                            ->success()
                            ->send();

                        $this->dispatch('open-midtrans-snap', token: $payment->snap_token);
                    } catch (Throwable) {
                        Notification::make()
                            ->title(__('Unable to start payment'))
                            ->danger()
                            ->send();
                    }
                }),
            Action::make('download')
                ->label(__('Download PDF'))
                ->icon('heroicon-o-document-arrow-down')
                ->url(fn (Invoice $record): string => route('invoices.download', $record))
                ->openUrlInNewTab(),

            Action::make('view_registration')
                ->label(__('View Registration'))
                ->icon('heroicon-o-ticket')
                ->color('gray')
                ->url(fn (Invoice $record): string => EventRegistrationResource::getUrl('view', ['record' => $record->event_registration_id])),
        ];
    }
}
