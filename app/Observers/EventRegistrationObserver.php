<?php

namespace App\Observers;

use App\Models\EventRegistration;
use App\Services\EventRegistrationInvoiceService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class EventRegistrationObserver implements ShouldHandleEventsAfterCommit
{
    public function __construct(
        private readonly EventRegistrationInvoiceService $invoiceService,
    ) {}

    public function created(EventRegistration $registration): void
    {
        $this->invoiceService->regenerate($registration, "Superseded by newer invoice");
    }

    public function updated(EventRegistration $registration): void
    {
        $this->invoiceService->regenerate($registration, "Superseded by newer invoice");
    }
}
