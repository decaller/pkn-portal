<?php

namespace App\Services\Payments;

use App\Models\Invoice;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransSnapGateway
{
    public function createTransaction(Invoice $invoice, string $orderId, string $idempotencyKey): array
    {
        $invoice->loadMissing(['items', 'registration']);

        $this->configure($idempotencyKey);

        $expiresAt = now()->addMinutes((int) config('services.midtrans.payment_expiry_minutes', 180));
        $grossAmount = (int) round((float) $invoice->total_amount);
        $booker = $invoice->booker_snapshot ?? [];

        $response = Snap::createTransaction([
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => array_filter([
                'first_name' => $booker['name'] ?? null,
                'email' => $booker['email'] ?? null,
            ], fn ($value): bool => filled($value)),
            'item_details' => $invoice->items->map(function ($item, int $index): array {
                return [
                    'id' => (string) ($item->getKey() ?? $index + 1),
                    'price' => (int) round((float) $item->line_total),
                    'quantity' => 1,
                    'name' => sprintf(
                        '%s (%d participant%s)',
                        $item->package_name,
                        (int) $item->participant_count,
                        (int) $item->participant_count === 1 ? '' : 's',
                    ),
                ];
            })->all(),
            'expiry' => [
                'unit' => 'minute',
                'duration' => (int) config('services.midtrans.payment_expiry_minutes', 180),
            ],
            'custom_field1' => (string) $invoice->getKey(),
            'custom_field2' => (string) $invoice->event_registration_id,
            'custom_field3' => (string) $invoice->invoice_number,
        ]);

        return [
            'order_id' => $orderId,
            'token' => $response->token,
            'redirect_url' => $response->redirect_url,
            'gross_amount' => number_format((float) $invoice->total_amount, 2, '.', ''),
            'currency' => $invoice->currency ?? 'IDR',
            'expires_at' => $expiresAt,
            'raw_response' => json_decode(json_encode($response, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR),
        ];
    }

    private function configure(string $idempotencyKey): void
    {
        Config::$serverKey = (string) config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production', false);
        Config::$isSanitized = (bool) config('services.midtrans.is_sanitized', true);
        Config::$is3ds = (bool) config('services.midtrans.is_3ds', true);
        Config::$paymentIdempotencyKey = $idempotencyKey;
    }
}
