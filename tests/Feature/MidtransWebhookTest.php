<?php

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\InvoicePayment;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\PaymentApprovedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

function createMidtransRegistration(): EventRegistration
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

    return $registration->fresh(['booker', 'latestInvoice']);
}

function createMidtransAttempt(EventRegistration $registration): InvoicePayment
{
    $invoice = $registration->latestInvoice;

    return InvoicePayment::query()->create([
        'invoice_id' => $invoice->getKey(),
        'event_registration_id' => $registration->getKey(),
        'provider' => InvoicePayment::PROVIDER_MIDTRANS,
        'order_id' => sprintf('INV-%d-V%d-A1', $invoice->getKey(), $invoice->version),
        'status' => InvoicePayment::STATUS_PENDING,
        'gross_amount' => $invoice->total_amount,
        'currency' => 'IDR',
        'snap_token' => 'snap-token',
        'snap_redirect_url' => 'https://example.test/pay',
        'expires_at' => now()->addHour(),
    ]);
}

function midtransPayload(InvoicePayment $payment, array $overrides = []): array
{
    $payload = array_merge([
        'order_id' => $payment->order_id,
        'status_code' => '200',
        'gross_amount' => number_format((float) $payment->gross_amount, 2, '.', ''),
        'transaction_status' => 'pending',
        'transaction_id' => 'txn-123',
        'payment_type' => 'bank_transfer',
        'fraud_status' => 'accept',
    ], $overrides);

    $payload['signature_key'] = hash(
        'sha512',
        $payload['order_id'].$payload['status_code'].$payload['gross_amount'].config('services.midtrans.server_key'),
    );

    return $payload;
}

beforeEach(function (): void {
    config()->set('services.midtrans.server_key', 'midtrans-test-server-key');
});

it('rejects a webhook with an invalid signature', function () {
    $registration = createMidtransRegistration();
    $attempt = createMidtransAttempt($registration);

    $payload = midtransPayload($attempt);
    $payload['signature_key'] = 'invalid-signature';

    $this->postJson(route('payments.midtrans.notifications'), $payload)
        ->assertForbidden();
});

it('returns not found for an unknown order id', function () {
    $payload = midtransPayload(new InvoicePayment([
        'order_id' => 'INV-999-V1-A1',
        'gross_amount' => '250000.00',
    ]));

    $this->postJson(route('payments.midtrans.notifications'), $payload)
        ->assertNotFound();
});

it('marks the payment and registration as pending for a pending notification', function () {
    Notification::fake();

    $registration = createMidtransRegistration();
    $attempt = createMidtransAttempt($registration);
    $payload = midtransPayload($attempt, [
        'transaction_status' => 'pending',
    ]);

    $this->postJson(route('payments.midtrans.notifications'), $payload)
        ->assertSuccessful()
        ->assertJson(['message' => 'ok']);

    $attempt->refresh();
    $registration->refresh();

    expect($attempt->status)->toBe(InvoicePayment::STATUS_PENDING)
        ->and($attempt->midtrans_transaction_status)->toBe('pending')
        ->and($registration->payment_status)->toBe(PaymentStatus::Submitted)
        ->and($registration->status)->toBe(RegistrationStatus::PendingPayment);

    Notification::assertNothingSent();
});

it('marks the payment as paid and sends the approval notification on settlement', function () {
    Notification::fake();

    $registration = createMidtransRegistration();
    $attempt = createMidtransAttempt($registration);
    $payload = midtransPayload($attempt, [
        'transaction_status' => 'settlement',
    ]);

    $this->postJson(route('payments.midtrans.notifications'), $payload)
        ->assertSuccessful();

    $attempt->refresh();
    $registration->refresh();

    expect($attempt->status)->toBe(InvoicePayment::STATUS_PAID)
        ->and($attempt->paid_at)->not->toBeNull()
        ->and($registration->payment_status)->toBe(PaymentStatus::Verified)
        ->and($registration->status)->toBe(RegistrationStatus::Paid);

    Notification::assertSentTo($registration->booker, PaymentApprovedNotification::class);
});

it('handles duplicate settlement notifications idempotently', function () {
    Notification::fake();

    $registration = createMidtransRegistration();
    $attempt = createMidtransAttempt($registration);
    $payload = midtransPayload($attempt, [
        'transaction_status' => 'settlement',
    ]);

    $this->postJson(route('payments.midtrans.notifications'), $payload)->assertSuccessful();
    $this->postJson(route('payments.midtrans.notifications'), $payload)->assertSuccessful();

    $attempt->refresh();
    $registration->refresh();

    expect($attempt->status)->toBe(InvoicePayment::STATUS_PAID)
        ->and($registration->payment_status)->toBe(PaymentStatus::Verified);

    Notification::assertSentToTimes($registration->booker, PaymentApprovedNotification::class, 1);
});

it('marks failed notifications as failed unless already paid', function (string $transactionStatus) {
    Notification::fake();

    $registration = createMidtransRegistration();
    $attempt = createMidtransAttempt($registration);
    $payload = midtransPayload($attempt, [
        'transaction_status' => $transactionStatus,
    ]);

    $this->postJson(route('payments.midtrans.notifications'), $payload)
        ->assertSuccessful();

    $attempt->refresh();
    $registration->refresh();

    expect($attempt->status)->toBe(InvoicePayment::STATUS_FAILED)
        ->and($registration->payment_status)->toBe(PaymentStatus::Rejected)
        ->and($registration->status)->toBe(RegistrationStatus::Draft);

    Notification::assertNothingSent();
})->with(['deny', 'expire', 'cancel', 'failure']);
