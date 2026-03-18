<?php

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Filament\User\Resources\Invoices\Pages\ListInvoices;
use App\Filament\User\Resources\Invoices\Pages\ViewInvoice;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Organization;
use App\Models\User;
use App\Services\Payments\InvoicePaymentService;
use App\Services\Payments\MidtransSnapGateway;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function createInvoiceFlowRegistration(): EventRegistration
{
    $booker = User::factory()->create();
    $organization = Organization::factory()->create(['admin_user_id' => $booker->getKey()]);
    $organization->users()->attach($booker, ['role' => 'admin']);
    $event = Event::factory()->create();

    $registration = EventRegistration::query()->create([
        'event_id' => $event->getKey(),
        'organization_id' => $organization->getKey(),
        'booker_user_id' => $booker->getKey(),
        'package_breakdown' => [[
            'package_name' => 'General',
            'participant_count' => 1,
            'unit_price' => 250000,
        ]],
        'total_amount' => 250000,
        'status' => RegistrationStatus::Draft,
        'payment_status' => PaymentStatus::Unpaid,
    ]);

    return $registration->fresh(['booker', 'organization', 'latestInvoice']);
}

function fakeGateway(): MidtransSnapGateway
{
    return new class extends MidtransSnapGateway
    {
        public int $calls = 0;

        public function createTransaction(Invoice $invoice, string $orderId, string $idempotencyKey): array
        {
            $this->calls++;

            return [
                'order_id' => $orderId,
                'token' => 'snap-token-'.$this->calls,
                'redirect_url' => 'https://example.test/pay/'.$orderId,
                'gross_amount' => number_format((float) $invoice->total_amount, 2, '.', ''),
                'currency' => 'IDR',
                'expires_at' => now()->addHours(3),
                'raw_response' => [
                    'token' => 'snap-token-'.$this->calls,
                    'redirect_url' => 'https://example.test/pay/'.$orderId,
                ],
            ];
        }
    };
}

it('creates a snap payment attempt for an unpaid invoice', function () {
    $registration = createInvoiceFlowRegistration();
    $gateway = fakeGateway();
    app()->instance(MidtransSnapGateway::class, $gateway);

    $payment = app(InvoicePaymentService::class)->createOrReuseSnapPayment($registration->latestInvoice);

    expect($payment->status)->toBe(InvoicePayment::STATUS_PENDING)
        ->and($payment->snap_token)->toBe('snap-token-1')
        ->and($payment->order_id)->toBe(sprintf('INV-%d-V%d-A1', $registration->latestInvoice->getKey(), $registration->latestInvoice->version))
        ->and($gateway->calls)->toBe(1);

    $registration->refresh();

    expect($registration->payment_status)->toBe(PaymentStatus::Submitted)
        ->and($registration->status)->toBe(RegistrationStatus::PendingPayment);
});

it('reuses an active pending attempt instead of creating a new one', function () {
    $registration = createInvoiceFlowRegistration();
    $gateway = fakeGateway();
    app()->instance(MidtransSnapGateway::class, $gateway);

    $firstAttempt = app(InvoicePaymentService::class)->createOrReuseSnapPayment($registration->latestInvoice);
    $secondAttempt = app(InvoicePaymentService::class)->createOrReuseSnapPayment($registration->latestInvoice->fresh());

    expect($firstAttempt->is($secondAttempt))->toBeTrue()
        ->and($gateway->calls)->toBe(1)
        ->and(InvoicePayment::query()->count())->toBe(1);
});

it('does not allow a new payment attempt for a paid registration', function () {
    $registration = createInvoiceFlowRegistration();
    $registration->markPaidFromGateway();
    app()->instance(MidtransSnapGateway::class, fakeGateway());

    expect(fn () => app(InvoicePaymentService::class)->createOrReuseSnapPayment($registration->latestInvoice->fresh()))
        ->toThrow(DomainException::class);
});

it('does not regenerate invoices for payment-only updates', function () {
    $registration = createInvoiceFlowRegistration();
    $invoice = $registration->latestInvoice;

    $registration->markPaymentPendingFromGateway();

    expect($registration->fresh()->invoices()->count())->toBe(1)
        ->and($registration->fresh()->latestInvoice->is($invoice))->toBeTrue();
});

it('shows the pay now action for an unpaid invoice in the user list page', function () {
    $registration = createInvoiceFlowRegistration();

    $this->actingAs($registration->booker);
    Filament::setCurrentPanel(Filament::getPanel('user'));
    Filament::setTenant($registration->organization);

    Livewire::test(ListInvoices::class)
        ->assertTableActionExists('pay_now', record: $registration->latestInvoice);
});

it('hides the pay now action for a paid invoice in the user invoice page', function () {
    $registration = createInvoiceFlowRegistration();
    $registration->markPaidFromGateway();

    $this->actingAs($registration->booker);
    Filament::setCurrentPanel(Filament::getPanel('user'));
    Filament::setTenant($registration->organization);

    Livewire::test(ViewInvoice::class, ['record' => $registration->latestInvoice->getRouteKey()])
        ->assertActionHidden('pay_now');
});

it('has indonesian translations for the new midtrans payment strings', function () {
    $keys = [
        'Pay Now',
        'Continue Payment',
        'Pending Payment',
        'Paid',
        'Failed',
        'Payment method',
        'Order ID',
        'Waiting for Midtrans confirmation',
        'Payment window closed',
        'Payment session created',
        'Unable to start payment',
        'Payment completed',
        'Midtrans payment',
        'Legacy payment proof',
    ];

    $english = json_decode(file_get_contents(lang_path('en.json')), true, 512, JSON_THROW_ON_ERROR);
    $indonesian = json_decode(file_get_contents(lang_path('id.json')), true, 512, JSON_THROW_ON_ERROR);

    foreach ($keys as $key) {
        expect($english)->toHaveKey($key);
        expect($indonesian)->toHaveKey($key);
    }
});
