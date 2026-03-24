<?php

namespace App\Http\Resources\V1;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (! $this->resource) {
            return [];
        }

        $packageBreakdown = is_array($this->resource->package_breakdown) ? $this->resource->package_breakdown : [];
        $firstPackage = $packageBreakdown[0] ?? null;

        $status = $this->resource->status instanceof RegistrationStatus ? $this->resource->status : RegistrationStatus::tryFrom((string) $this->resource->status);
        $paymentStatus = $this->resource->payment_status instanceof PaymentStatus ? $this->resource->payment_status : PaymentStatus::tryFrom((string) $this->resource->payment_status);

        return [
            'id' => $this->resource->id,
            'event_id' => $this->resource->event_id,
            'organization_id' => $this->resource->organization_id,
            'booker_user_id' => $this->resource->booker_user_id,
            'status' => $this->resource->status,
            'status_label' => $status?->getLabel() ?? '-',
            'status_color' => $status?->getColor() ?? 'gray',
            'payment_status' => $this->resource->payment_status,
            'payment_status_label' => $paymentStatus?->getLabel() ?? '-',
            'payment_status_color' => $paymentStatus?->getColor() ?? 'gray',
            'total_amount' => $this->resource->total_amount,
            'payment_proof_path' => $this->resource->payment_proof_path,
            'payment_proof_url' => $this->resource->payment_proof_path ? asset('storage/'.$this->resource->payment_proof_path) : null,
            'notes' => $this->resource->notes,
            'verified_by_user_id' => $this->resource->verified_by_user_id,
            'verified_at' => $this->resource->verified_at,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,

            // Computed fields for mobile UI
            'package_name' => $firstPackage['package_name'] ?? ($firstPackage['name'] ?? null),
            'participant_count' => $this->resource->participants_count ?? count($this->whenLoaded('participants') ?? []),
            'unit_price' => $firstPackage['unit_price'] ?? ($firstPackage['price'] ?? '0.00'),
            'package_breakdown' => collect($packageBreakdown)->map(fn ($item) => [
                'package_id' => $item['package_id'] ?? null,
                'name' => $item['name'] ?? ($item['package_name'] ?? 'Selected Package'),
                'count' => $item['count'] ?? ($item['participant_count'] ?? ($item['quantity'] ?? 0)),
                'price' => $item['price'] ?? ($item['unit_price'] ?? 0),
                'subtotal' => $item['subtotal'] ?? 0,
            ])->toArray(),

            'organization' => $this->whenLoaded('organization', fn () => [
                'id' => $this->resource->organization->id,
                'name' => $this->resource->organization->name,
            ]),

            'booker' => $this->whenLoaded('booker', fn () => [
                'id' => $this->resource->booker->id,
                'name' => $this->resource->booker->name,
            ]),

            'latest_invoice' => $this->whenLoaded('invoices', function () {
                $invoice = $this->resource->latestInvoice;
                if (! $invoice) {
                    return null;
                }

                $invoiceStatus = $invoice->status instanceof InvoiceStatus ? $invoice->status : InvoiceStatus::tryFrom((string) $invoice->status);

                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'status' => $invoice->status,
                    'status_label' => $invoiceStatus?->getLabel() ?? '-',
                    'status_color' => $invoiceStatus?->getColor() ?? 'gray',
                    'total_amount' => $invoice->total_amount,
                    'issued_at' => $invoice->issued_at,
                    'download_url' => route('invoices.download', ['invoice' => $invoice]),
                    'can_pay_now' => $invoice->canStartGatewayPayment(),
                    'has_active_payment_attempt' => $invoice->hasActivePaymentAttempt(),
                    'latest_payment' => $invoice->latestPayment ? [
                        'id' => $invoice->latestPayment->id,
                        'order_id' => $invoice->latestPayment->order_id,
                        'midtrans_payment_type' => $invoice->latestPayment->midtrans_payment_type,
                        'midtrans_transaction_status' => $invoice->latestPayment->midtrans_transaction_status,
                        'midtrans_transaction_id' => $invoice->latestPayment->midtrans_transaction_id,
                        'status' => $invoice->latestPayment->status,
                        'paid_at' => $invoice->latestPayment->paid_at,
                        'expires_at' => $invoice->latestPayment->expires_at,
                    ] : null,
                ];
            }),

            'event' => new EventResource($this->whenLoaded('event')),
            'participants' => ParticipantResource::collection($this->whenLoaded('participants')),
        ];
    }
}
