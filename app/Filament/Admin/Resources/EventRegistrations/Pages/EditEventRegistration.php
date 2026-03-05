<?php

namespace App\Filament\Admin\Resources\EventRegistrations\Pages;

use App\Filament\Admin\Resources\EventRegistrations\EventRegistrationResource;
use App\Filament\Admin\Resources\Users\UserResource;
use App\Models\EventRegistration;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditEventRegistration extends EditRecord
{
    protected static string $resource = EventRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verify_payment')
                ->label(__('Verify Payment'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (EventRegistration $record): bool => $record->payment_status === \App\Enums\PaymentStatus::Submitted)
                ->requiresConfirmation()
                ->modalHeading(__('Verify Payment'))
                ->modalDescription(__('Are you absolutely sure you want to verify this payment? The user will be fully confirmed for this event.'))
                ->modalSubmitActionLabel(__('Yes, mark as Verified'))
                ->action(function (EventRegistration $record) {
                    $record->verifyPayment(auth()->user());

                    \Filament\Notifications\Notification::make()
                        ->title(__('Payment Verified'))
                        ->success()
                        ->send();

                    redirect(EventRegistrationResource::getUrl('view', ['record' => $record]));
                }),

            Action::make('view_booker')
                ->label(__('View Booker Profile'))
                ->icon('heroicon-o-user')
                ->color('gray')
                ->url(fn (EventRegistration $record): string => UserResource::getUrl('view', ['record' => $record->booker_user_id])),
        ];
    }
}
