<?php

namespace App\Observers;

use App\Enums\PaymentStatus;
use App\Models\EventRegistration;
use App\Notifications\PaymentApprovedNotification;
use App\Notifications\PaymentUploadReminderNotification;
use App\Services\EventRegistrationInvoiceService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class EventRegistrationObserver implements ShouldHandleEventsAfterCommit
{
    public function __construct(
        private readonly EventRegistrationInvoiceService $invoiceService,
    ) {}

    public function created(EventRegistration $registration): void
    {
        $this->invoiceService->regenerate($registration, 'Superseded by newer invoice');

        // Notify booker to upload payment proof
        if ($registration->booker) {
            $registration->booker->notify(new PaymentUploadReminderNotification($registration));
        }
    }

    public function updated(EventRegistration $registration): void
    {
        $this->invoiceService->regenerate($registration, 'Superseded by newer invoice');

        // Notify booker when payment is approved
        if ($registration->wasChanged('payment_status') &&
            $registration->payment_status === PaymentStatus::Verified &&
            $registration->booker
        ) {
            $registration->booker->notify(new PaymentApprovedNotification($registration));
        }
    }
}
