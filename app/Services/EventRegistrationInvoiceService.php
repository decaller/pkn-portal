<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\EventRegistration;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class EventRegistrationInvoiceService
{
    public function regenerate(
        EventRegistration $registration,
        string $reason,
    ): Invoice {
        return DB::transaction(function () use (
            $registration,
            $reason,
        ): Invoice {
            /** @var EventRegistration $registration */
            $registration = EventRegistration::query()
                ->with(["event", "organization", "booker"])
                ->lockForUpdate()
                ->findOrFail($registration->getKey());

            Invoice::query()
                ->where("event_registration_id", $registration->getKey())
                ->where("status", "!=", InvoiceStatus::Void->value)
                ->update([
                    "status" => InvoiceStatus::Void->value,
                    "voided_at" => now(),
                    "void_reason" => $reason,
                    "updated_at" => now(),
                ]);

            $version =
                ((int) Invoice::query()
                    ->where("event_registration_id", $registration->getKey())
                    ->max("version")) + 1;

            $rows = $this->normalizeRows($registration);
            [$items, $subtotal] = $this->buildInvoiceItems(
                $rows,
                (float) $registration->total_amount,
            );

            $invoice = Invoice::create([
                "event_registration_id" => $registration->getKey(),
                "invoice_number" => $this->invoiceNumber(
                    $registration->getKey(),
                    $version,
                ),
                "version" => $version,
                "status" => InvoiceStatus::Issued,
                "issued_at" => now(),
                "due_at" => now()->addDays(7)->toDateString(),
                "currency" => "IDR",
                "event_snapshot" => [
                    "id" => $registration->event?->getKey(),
                    "title" => $registration->event?->title,
                    "date" => $registration->event?->event_date?->toDateString(),
                    "type" => $registration->event?->event_type?->value,
                    "slug" => $registration->event?->slug,
                ],
                "organization_snapshot" => [
                    "id" => $registration->organization?->getKey(),
                    "name" => $registration->organization?->name,
                    "slug" => $registration->organization?->slug,
                ],
                "booker_snapshot" => [
                    "id" => $registration->booker?->getKey(),
                    "name" => $registration->booker?->name,
                    "email" => $registration->booker?->email,
                ],
                "subtotal" => $subtotal,
                "discount_amount" => 0,
                "tax_amount" => 0,
                "total_amount" => $subtotal,
                "notes" => $registration->notes,
            ]);

            foreach ($items as $item) {
                $invoice->items()->create([
                    "package_name" => $item["package_name"],
                    "participant_count" => $item["participant_count"],
                    "unit_price" => $item["unit_price"],
                    "line_total" => $item["line_total"],
                    "metadata" => null,
                ]);
            }

            return $invoice->load("items");
        });
    }

    private function normalizeRows(EventRegistration $registration): array
    {
        $rows = is_array($registration->package_breakdown)
            ? $registration->package_breakdown
            : [];

        if ($rows !== []) {
            return array_values(
                array_filter($rows, fn($row): bool => is_array($row)),
            );
        }

        return [
            [
                "package_name" => $registration->package_name ?: "General",
                "participant_count" =>
                    (int) ($registration->participant_count ?: 1),
                "unit_price" => (float) ($registration->unit_price ?: 0),
            ],
        ];
    }

    private function buildInvoiceItems(array $rows, float $expectedTotal): array
    {
        $sumAsPerUnit = 0.0;
        $sumAsLineTotal = 0.0;

        foreach ($rows as $row) {
            $qty = max(1, (int) ($row["participant_count"] ?? 1));
            $unit = (float) ($row["unit_price"] ?? 0);
            $sumAsPerUnit += $qty * $unit;
            $sumAsLineTotal += $unit;
        }

        // User form stores `unit_price` as line total; admin form stores it as per-participant.
        // Pick the interpretation that best matches registration total.
        $useLineTotalInput = $this->isCloserToExpected(
            $sumAsLineTotal,
            $sumAsPerUnit,
            $expectedTotal,
        );

        $items = [];
        $subtotal = 0.0;

        foreach ($rows as $row) {
            $qty = max(1, (int) ($row["participant_count"] ?? 1));
            $raw = (float) ($row["unit_price"] ?? 0);

            $lineTotal = $useLineTotalInput ? $raw : $qty * $raw;
            $unitPrice = $useLineTotalInput ? $lineTotal / $qty : $raw;

            $items[] = [
                "package_name" => (string) ($row["package_name"] ?? "General"),
                "participant_count" => $qty,
                "unit_price" => $unitPrice,
                "line_total" => $lineTotal,
            ];
            $subtotal += $lineTotal;
        }

        return [$items, $subtotal];
    }

    private function isCloserToExpected(
        float $candidateLineTotal,
        float $candidatePerUnit,
        float $expectedTotal,
    ): bool {
        if ($expectedTotal <= 0) {
            return false;
        }

        $lineDiff = abs($candidateLineTotal - $expectedTotal);
        $perUnitDiff = abs($candidatePerUnit - $expectedTotal);

        return $lineDiff < $perUnitDiff;
    }

    private function invoiceNumber(int $registrationId, int $version): string
    {
        return sprintf(
            "INV/%s/REG%06d/V%02d",
            now()->format("Ym"),
            $registrationId,
            $version,
        );
    }
}
