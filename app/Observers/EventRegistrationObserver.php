<?php

namespace App\Observers;

use App\Models\EventRegistration;
use App\Services\EventRegistrationInvoiceService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class EventRegistrationObserver implements ShouldHandleEventsAfterCommit
{
    private const INVOICE_AFFECTING_FIELDS = [
        'event_id',
        'organization_id',
        'package_breakdown',
        'total_amount',
        'notes',
    ];

    public function __construct(
        private readonly EventRegistrationInvoiceService $invoiceService,
    ) {}

    public function created(EventRegistration $registration): void
    {
        $this->invoiceService->regenerate($registration, 'Superseded by newer invoice');

        $this->checkAndMarkFreeRegistration($registration);
    }

    public function updated(EventRegistration $registration): void
    {
        if ($this->shouldRegenerateInvoice($registration)) {
            $this->invoiceService->regenerate($registration, 'Superseded by newer invoice');
        }

        $this->checkAndMarkFreeRegistration($registration);
    }

    private function shouldRegenerateInvoice(EventRegistration $registration): bool
    {
        return collect($registration->getChanges())
            ->keys()
            ->intersect(self::INVOICE_AFFECTING_FIELDS)
            ->isNotEmpty();
    }

    private function checkAndMarkFreeRegistration(EventRegistration $registration): void
    {
        if ($registration->total_amount <= 0 && $registration->payment_status !== \App\Enums\PaymentStatus::Verified) {
            $registration->markPaidFromGateway([
                'verified_at' => now(),
            ]);
        }
    }
}
