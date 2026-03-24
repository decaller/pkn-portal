<?php

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Organization;
use App\Models\User;
use App\Services\Payments\MidtransSnapGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function createPaymentApiRegistration(): EventRegistration
{
    $booker = User::factory()->create();
    $organization = Organization::factory()->create(['admin_user_id' => $booker->getKey()]);
    $organization->users()->attach($booker, ['role' => 'admin']);
    $event = Event::factory()->create();

    return EventRegistration::query()->create([
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
    ])->fresh(['booker', 'latestInvoice']);
}

function fakeApiGateway(): MidtransSnapGateway
{
    return new class extends MidtransSnapGateway
    {
        public function createTransaction(Invoice $invoice, string $orderId, string $idempotencyKey): array
        {
            return [
                'order_id' => $orderId,
                'token' => 'snap-test-token',
                'redirect_url' => 'https://example.test/pay/'.$orderId,
                'gross_amount' => number_format((float) $invoice->total_amount, 2, '.', ''),
                'currency' => 'IDR',
                'expires_at' => now()->addHours(3),
                'raw_response' => [
                    'token' => 'snap-test-token',
                    'redirect_url' => 'https://example.test/pay/'.$orderId,
                ],
            ];
        }
    };
}

function createWebhookAttempt(): InvoicePayment
{
    $registration = createPaymentApiRegistration();
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

function webhookPayload(InvoicePayment $payment, array $overrides = []): array
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

test('authenticated mobile user can charge an unpaid invoice', function () {
    $registration = createPaymentApiRegistration();
    app()->instance(MidtransSnapGateway::class, fakeApiGateway());

    Sanctum::actingAs($registration->booker, ['*']);

    $response = $this->postJson('/api/v1/payments/charge', [
        'invoice_id' => $registration->latestInvoice->getKey(),
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'token' => 'snap-test-token',
        ]);

    expect($registration->latestInvoice->fresh()->status)->toBe(InvoiceStatus::Pending);
});

test('payment webhook acknowledges invalid signatures with http 200', function () {
    $attempt = createWebhookAttempt();
    $payload = webhookPayload($attempt, ['signature_key' => 'invalid']);

    $this->postJson('/api/v1/payments/webhook', $payload)
        ->assertSuccessful()
        ->assertJson(['message' => 'ok']);
});

test('payment webhook updates the invoice status for settlement notifications', function () {
    $attempt = createWebhookAttempt();
    $payload = webhookPayload($attempt, ['transaction_status' => 'settlement']);

    $this->postJson('/api/v1/payments/webhook', $payload)
        ->assertSuccessful()
        ->assertJson(['message' => 'ok']);

    expect($attempt->invoice->fresh()->status)->toBe(InvoiceStatus::Paid);
});
