<?php

namespace App\Services\Payments;

use App\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use DomainException;
use Illuminate\Support\Facades\DB;

class InvoicePaymentService
{
    public function __construct(
        private readonly MidtransSnapGateway $gateway,
    ) {}

    public function createOrReuseSnapPayment(Invoice $invoice): InvoicePayment
    {
        return DB::transaction(function () use ($invoice): InvoicePayment {
            /** @var Invoice $invoice */
            $invoice = Invoice::query()
                ->with(['items', 'registration.booker', 'payments'])
                ->lockForUpdate()
                ->findOrFail($invoice->getKey());

            if ($invoice->status->value === 'void') {
                throw new DomainException('Cannot start payment for a void invoice.');
            }

            if ($invoice->registration?->payment_status?->value === 'verified') {
                throw new DomainException('This registration has already been paid.');
            }

            $activeAttempt = $invoice->payments
                ->sortByDesc('id')
                ->first(fn (InvoicePayment $payment): bool => $payment->isPendingLike()
                    && $payment->snap_token
                    && ($payment->expires_at === null || $payment->expires_at->isFuture()));

            if ($activeAttempt) {
                return $activeAttempt;
            }

            $attemptSequence = $invoice->payments->count() + 1;
            $orderId = sprintf('INV-%d-V%d-A%d', $invoice->getKey(), $invoice->version, $attemptSequence);

            $payment = InvoicePayment::query()->create([
                'invoice_id' => $invoice->getKey(),
                'event_registration_id' => $invoice->event_registration_id,
                'provider' => InvoicePayment::PROVIDER_MIDTRANS,
                'order_id' => $orderId,
                'status' => InvoicePayment::STATUS_PENDING,
                'gross_amount' => $invoice->total_amount,
                'currency' => $invoice->currency ?? 'IDR',
                'metadata' => [
                    'attempt_sequence' => $attemptSequence,
                    'invoice_version' => $invoice->version,
                    'source' => 'user-portal',
                    'invoice_number' => $invoice->invoice_number,
                ],
            ]);

            $snap = $this->gateway->createTransaction($invoice, $orderId, (string) $payment->getKey());

            $payment->forceFill([
                'snap_token' => $snap['token'],
                'snap_redirect_url' => $snap['redirect_url'],
                'raw_snap_response' => $snap['raw_response'],
                'expires_at' => $snap['expires_at'],
                'gross_amount' => $snap['gross_amount'],
                'currency' => $snap['currency'],
            ])->save();

            $invoice->registration?->markPaymentPendingFromGateway([
                'invoice_payment_id' => $payment->getKey(),
            ]);

            return $payment->fresh();
        });
    }

    public function handleMidtransNotification(array $payload): InvoicePayment
    {
        return DB::transaction(function () use ($payload): InvoicePayment {
            /** @var InvoicePayment $payment */
            $payment = InvoicePayment::query()
                ->with(['registration', 'invoice'])
                ->lockForUpdate()
                ->where('order_id', $payload['order_id'])
                ->firstOrFail();

            $normalizedStatus = $this->mapMidtransStatus(
                $payload['transaction_status'],
                $payload['fraud_status'] ?? null,
            );

            $wasPaid = $payment->isPaid();
            $wasPending = $payment->isPendingLike();

            $payment->forceFill([
                'status' => $normalizedStatus,
                'gross_amount' => $payload['gross_amount'] ?? $payment->gross_amount,
                'currency' => $payload['currency'] ?? $payment->currency,
                'midtrans_transaction_id' => $payload['transaction_id'] ?? $payment->midtrans_transaction_id,
                'midtrans_transaction_status' => $payload['transaction_status'],
                'midtrans_payment_type' => $payload['payment_type'] ?? $payment->midtrans_payment_type,
                'midtrans_fraud_status' => $payload['fraud_status'] ?? $payment->midtrans_fraud_status,
                'raw_notification_payload' => $payload,
                'last_notified_at' => now(),
                'paid_at' => $normalizedStatus === InvoicePayment::STATUS_PAID
                    ? ($payment->paid_at ?? now())
                    : $payment->paid_at,
            ])->save();

            if ($normalizedStatus === InvoicePayment::STATUS_PAID && ! $wasPaid) {
                $payment->registration?->markPaidFromGateway([
                    'verified_at' => now(),
                    'invoice_payment_id' => $payment->getKey(),
                ]);
            }

            if (
                $normalizedStatus === InvoicePayment::STATUS_PENDING &&
                ! $wasPaid &&
                $payment->registration?->payment_status !== PaymentStatus::Submitted
            ) {
                $payment->registration?->markPaymentPendingFromGateway([
                    'invoice_payment_id' => $payment->getKey(),
                ]);
            }

            if ($normalizedStatus === InvoicePayment::STATUS_FAILED && ! $wasPaid) {
                $payment->registration?->markPaymentFailedFromGateway([
                    'invoice_payment_id' => $payment->getKey(),
                ]);
            }

            return $payment->fresh();
        });
    }

    private function mapMidtransStatus(string $transactionStatus, ?string $fraudStatus): string
    {
        return match ($transactionStatus) {
            'settlement' => InvoicePayment::STATUS_PAID,
            'capture' => $fraudStatus === 'accept'
                ? InvoicePayment::STATUS_PAID
                : InvoicePayment::STATUS_PENDING,
            'pending' => InvoicePayment::STATUS_PENDING,
            'deny', 'expire', 'cancel', 'failure' => InvoicePayment::STATUS_FAILED,
            default => InvoicePayment::STATUS_FAILED,
        };
    }
}
