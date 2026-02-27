<?php

namespace App\Filament\Admin\Resources\EventRegistrations\Pages;

use App\Filament\Admin\Resources\EventRegistrations\EventRegistrationResource;
use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use App\Models\EventRegistration;

class ViewEventRegistration extends ViewRecord
{
    protected static string $resource = EventRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verify_payment')
                ->label('Verify Payment')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (EventRegistration $record): bool => $record->payment_status === \App\Enums\PaymentStatus::Submitted)
                ->requiresConfirmation()
                ->modalHeading('Verify Payment')
                ->modalDescription('Are you absolutely sure you want to verify this payment? The user will be fully confirmed for this event.')
                ->modalSubmitActionLabel('Yes, mark as Verified')
                ->action(function (EventRegistration $record) {
                    $record->verifyPayment(auth()->user());

                    \Filament\Notifications\Notification::make()
                        ->title('Payment Verified')
                        ->success()
                        ->send();
                }),

            Action::make('view_booker')
                ->label('View Booker Profile')
                ->icon('heroicon-o-user')
                ->color('gray')
                ->url(fn (EventRegistration $record): string => UserResource::getUrl('view', ['record' => $record->booker_user_id])),

            EditAction::make(),
        ];
    }
}
