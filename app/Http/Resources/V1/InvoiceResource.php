<?php

namespace App\Http\Resources\V1;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Invoice $resource
 */
class InvoiceResource extends JsonResource
{
    private function mobileStatus(): string
    {
        $status = $this->resource->status instanceof InvoiceStatus
            ? $this->resource->status
            : InvoiceStatus::tryFrom((string) $this->resource->status);

        if ($status === InvoiceStatus::Void || $status === InvoiceStatus::Cancelled) {
            return 'cancelled';
        }

        if ($status === InvoiceStatus::Paid || $this->resource->registration?->payment_status?->value === 'verified') {
            return 'paid';
        }

        if ($status === InvoiceStatus::Expired) {
            return 'expired';
        }

        if ($status === InvoiceStatus::Pending || $this->resource->latestPayment?->status === 'pending') {
            return 'pending';
        }

        if ($this->resource->due_at?->isPast()) {
            return 'expired';
        }

        return 'unpaid';
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'invoice_number' => $this->resource->invoice_number,
            'registration_id' => $this->resource->event_registration_id,
            'status' => $this->mobileStatus(),
            'invoice_status' => $this->resource->status instanceof InvoiceStatus
                ? $this->resource->status->value
                : $this->resource->status,
            'gross_amount' => (float) $this->resource->total_amount,
            'subtotal' => (float) $this->resource->subtotal,
            'discount_amount' => (float) $this->resource->discount_amount,
            'tax_amount' => (float) $this->resource->tax_amount,
            'currency' => $this->resource->currency,
            'due_date' => $this->resource->due_at?->toDateString(),
            'issued_at' => $this->resource->issued_at?->toIso8601String(),
            'created_at' => $this->resource->created_at?->toIso8601String(),
            'updated_at' => $this->resource->updated_at?->toIso8601String(),
            'event' => $this->when(
                $this->resource->relationLoaded('registration') && $this->resource->registration?->relationLoaded('event'),
                fn () => [
                    'id' => $this->resource->registration?->event?->id,
                    'title' => $this->resource->registration?->event?->title,
                    'slug' => $this->resource->registration?->event?->slug,
                ],
            ),
            'items' => $this->whenLoaded('items', fn () => $this->resource->items->map(fn ($item) => [
                'id' => $item->id,
                'description' => $item->package_name,
                'quantity' => $item->participant_count,
                'price' => (float) $item->unit_price,
                'line_total' => (float) $item->line_total,
            ])->values()),
            'payments' => $this->whenLoaded('payments', fn () => $this->resource->payments->map(fn ($payment) => [
                'id' => $payment->id,
                'provider' => $payment->provider,
                'status' => $payment->status,
                'order_id' => $payment->order_id,
                'gross_amount' => (float) $payment->gross_amount,
                'currency' => $payment->currency,
                'payment_type' => $payment->midtrans_payment_type,
                'transaction_status' => $payment->midtrans_transaction_status,
                'redirect_url' => $payment->snap_redirect_url,
                'token' => $payment->snap_token,
                'expires_at' => $payment->expires_at?->toIso8601String(),
                'paid_at' => $payment->paid_at?->toIso8601String(),
            ])->values()),
        ];
    }
}
