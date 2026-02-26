<?php

namespace App\Filament\Resources\EventRegistrations\Pages;

use App\Filament\Resources\EventRegistrations\EventRegistrationResource;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditEventRegistration extends EditRecord
{
    protected static string $resource = EventRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verify_payment')
                ->label('Verify Payment')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (\App\Models\EventRegistration $record): bool => $record->payment_status === \App\Enums\PaymentStatus::Submitted)
                ->requiresConfirmation()
                ->modalHeading('Verify Payment')
                ->modalDescription('Are you absolutely sure you want to verify this payment? The user will be fully confirmed for this event.')
                ->modalSubmitActionLabel('Yes, mark as Verified')
                ->action(function (\App\Models\EventRegistration $record) {
                    $record->verifyPayment(auth()->user());

                    \Filament\Notifications\Notification::make()
                        ->title('Payment Verified')
                        ->success()
                        ->send();

                    redirect(EventRegistrationResource::getUrl('view', ['record' => $record]));
                }),

            Action::make('view_booker')
                ->label('View Booker Profile')
                ->icon('heroicon-o-user')
                ->color('gray')
                ->url(fn (\App\Models\EventRegistration $record): string => UserResource::getUrl('view', ['record' => $record->booker_user_id])),
        ];
    }
}
