<?php

namespace App\Filament\User\Resources\EventRegistrations\Pages;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Filament\User\Resources\EventRegistrations\EventRegistrationResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ViewRecord;

class ViewEventRegistration extends ViewRecord
{
    protected static string $resource = EventRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make("upload_payment_proof")
                ->label("Upload Payment")
                ->icon("heroicon-o-arrow-up-tray")
                ->color("success")
                ->visible(
                    fn(): bool => $this->record->payment_status !==
                        PaymentStatus::Verified,
                )
                ->form([
                    FileUpload::make("payment_proof_path")
                        ->label("Payment proof")
                        ->disk("public")
                        ->visibility("public")
                        ->directory("payment-proofs")
                        ->maxSize(4096)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $this->record->submitPaymentProof(
                        $data["payment_proof_path"],
                    );

                    $this->redirect(
                        static::getResource()::getUrl("view", [
                            "record" => $this->record,
                        ]),
                    );
                }),
            EditAction::make()->visible(
                fn(): bool => !(
                    $this->record->status === RegistrationStatus::Paid ||
                    $this->record->payment_status === PaymentStatus::Verified
                ),
            ),
        ];
    }
}
