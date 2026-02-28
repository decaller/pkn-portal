<?php

declare(strict_types=1);

namespace App\Filament\Helpers;

use App\Models\Event;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

/**
 * Shared helper utilities for event registration forms.
 *
 * - Provides a single-line debug logger (appends with " | ").
 * - Encapsulates package lookup / pricing logic.
 * - Encapsulates repeater-related helpers used by form callbacks.
 *
 * Note: All methods are static for convenient use in closure callbacks.
 */
final class EventRegistrationHelpers
{
    // Debug logging removed from helpers.
    // Previously the helper appended debug lines into a `debug_log` field and dispatched
    // livewire events. Debugging is now disabled here to keep form payloads clean.

    /**
     * Safely load registration packages for an event.
     *
     * Returns [] when the event is missing or packages are not an array.
     */
    public static function packagesForEvent(?int $eventId): array
    {
        if (! $eventId) {
            return [];
        }

        /** @var Event|null $event */
        $event = Event::query()->find($eventId);

        if (! $event) {
            return [];
        }

        $packages = $event->registration_packages;

        return is_array($packages) ? $packages : [];
    }

    /**
     * Build Select options for packages (human readable with formatted price).
     *
     * Returns a simple ["General" => "General (IDR 0)"] fallback when no packages.
     *
     * @return array<string,string>
     */
    public static function packageOptions(?int $eventId): array
    {
        $packages = self::packagesForEvent($eventId);

        if ($packages === []) {
            return ['General' => 'General (IDR 0)'];
        }

        $options = [];

        foreach ($packages as $package) {
            $name = (string) ($package['name'] ?? 'General');
            $price = (float) ($package['price'] ?? 0);

            $options[$name] = sprintf(
                '%s (IDR %s)',
                $name,
                number_format($price, 0, ',', '.'),
            );
        }

        return $options;
    }

    /**
     * Get raw price (per participant) for a named package on an event.
     */
    public static function packagePrice(
        ?int $eventId,
        ?string $packageName,
    ): float {
        $packages = self::packagesForEvent($eventId);

        if ($packages === []) {
            return 0.0;
        }

        foreach ($packages as $package) {
            if ((string) ($package['name'] ?? '') !== (string) $packageName) {
                continue;
            }

            return (float) ($package['price'] ?? 0);
        }

        return 0.0;
    }

    /**
     * Refresh repeater rows when the event changes: clears selected package_name and price
     * but preserves participant counts.
     *
     * This updates the `package_breakdown` repeater and the aggregated total via syncTotalAmount.
     */
    public static function refreshPackageRowsForEvent(
        Get $get,
        Set $set,
        $livewire,
    ): void {
        $eventId = $get('event_id');
        $rows = $get('package_breakdown') ?? [];

        if ($rows === []) {
            return;
        }

        $refreshed = [];

        foreach ($rows as $row) {
            $qty = max(1, (int) ($row['participant_count'] ?? 1));
            $refreshed[] = [
                'package_name' => null,
                'participant_count' => $qty,
                'unit_price' => 0,
            ];
        }

        // debug removed: refreshed packages for event (reselect required): {eventId}

        $set('package_breakdown', $refreshed);
        self::syncTotalAmount($refreshed, $set);
    }

    /**
     * Seed the first repeater row with the first available package for the event.
     */
    public static function seedFirstPackageRow(
        Get $get,
        Set $set,
        $livewire,
    ): void {
        $eventId = $get('event_id');
        $options = self::packageOptions($eventId);
        $firstPackage = array_key_first($options);

        if (! $firstPackage) {
            return;
        }

        $rows = [
            [
                'package_name' => $firstPackage,
                'participant_count' => 1,
                'unit_price' => self::packagePrice($eventId, $firstPackage),
            ],
        ];

        // debug removed: seeded first package row

        $set('package_breakdown', $rows);
        self::syncTotalAmount($rows, $set);
    }

    /**
     * Compute and set total_amount and also mirror the first repeater row into
     * flat hidden fields for easy inspection.
     *
     * @param  array<int, array<string,mixed>>  $rows
     */
    public static function syncTotalAmount(array $rows, Set $set): void
    {
        $total = 0.0;

        foreach ($rows as $row) {
            $total += (float) ($row['unit_price'] ?? 0);
        }

        $first = $rows[0] ?? null;

        if ($first) {
            $set('package_name', (string) ($first['package_name'] ?? ''));
            $set(
                'participant_count',
                max(0, (int) ($first['participant_count'] ?? 0)),
            );
            $set('unit_price', (float) ($first['unit_price'] ?? 0));
        } else {
            $set('package_name', null);
            $set('participant_count', 0);
            $set('unit_price', 0);
        }

        $set('total_amount', $total);
    }

    /**
     * Compute a near-immediate total by merging a provided current-row partial
     * with the repeater snapshot available through Get. Useful for per-field callbacks
     * that can't rely on the repeater's up-to-date $state parameter.
     *
     * This function is intentionally conservative: it merges the partial into the
     * first row (the seeded primary row) to avoid relying on repeater internals.
     *
     * @param  array<string,mixed>  $currentRowPartial
     */
    public static function computeTotalWithCurrentRow(
        Get $get,
        array $currentRowPartial = [],
    ): float {
        $rows = $get('../../package_breakdown') ?? [];

        // Merge partial into the first row (pragmatic approach for immediate feedback)
        $first = $rows[0] ?? [];
        if ($currentRowPartial) {
            $mergedFirst = array_merge($first, $currentRowPartial);
            $rows[0] = $mergedFirst;
        }

        $total = 0.0;
        foreach ($rows as $row) {
            $total += (float) ($row['unit_price'] ?? 0);
        }

        return $total;
    }

    /**
     * Convenience check for main admin.
     */
    public static function isMainAdmin(): bool
    {
        return (bool) auth()->user()?->isMainAdmin();
    }

    /**
     * Legacy pricing summary used by admin form: multiply qty * unit for every row.
     *
     * @param  array<int, array<string,mixed>>  $rows
     */
    public static function syncPricingSummary(array $rows, Set $set): void
    {
        $total = 0.0;

        foreach ($rows as $row) {
            $qty = max(1, (int) ($row['participant_count'] ?? 1));
            $unit = (float) ($row['unit_price'] ?? 0);
            $total += $qty * $unit;
        }

        $first = $rows[0] ?? [];

        $set('package_name', (string) ($first['package_name'] ?? ''));
        $set(
            'participant_count',
            max(0, (int) ($first['participant_count'] ?? 0)),
        );
        $set('unit_price', (float) ($first['unit_price'] ?? 0));
        $set('total_amount', $total);
    }
}
