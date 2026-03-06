<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Models\EventRegistration;
use App\Notifications\EmptyParticipantSpotReminderNotification;
use Illuminate\Console\Command;

class SendParticipantSlotRemindersCommand extends Command
{
    protected $signature = 'notifications:send-participant-slot-reminders';

    protected $description = 'Send reminders every 3 days to users whose registration has empty participant slots';

    public function handle(): void
    {
        $this->info('Sending participant slot reminders...');

        $sent = 0;

        EventRegistration::query()
            ->with(['booker', 'event', 'participants'])
            ->whereNotIn('payment_status', [PaymentStatus::Verified])
            ->whereHas('event', function ($query): void {
                $query->whereDate('event_date', '>=', now()->startOfDay());
            })
            ->whereNotNull('package_breakdown')
            ->chunkById(100, function ($registrations) use (&$sent): void {
                foreach ($registrations as $registration) {
                    if (! $registration->booker || ! is_array($registration->package_breakdown)) {
                        continue;
                    }

                    // Calculate the expected number of participants from package_breakdown
                    $expectedParticipants = collect($registration->package_breakdown)
                        ->sum(fn (array $pkg): int => (int) ($pkg['quantity'] ?? 0));

                    $actualParticipants = $registration->participants->count();

                    if ($actualParticipants >= $expectedParticipants) {
                        continue;
                    }

                    // Skip if a reminder was already sent in the past 3 days for this registration
                    $alreadyNotified = $registration->booker->notifications()
                        ->where('type', EmptyParticipantSpotReminderNotification::class)
                        ->where('created_at', '>=', now()->subDays(3))
                        ->whereRaw("(data::jsonb)->>'registration_id' = ?", [(string) $registration->id])
                        ->exists();

                    if ($alreadyNotified) {
                        continue;
                    }

                    $registration->booker->notify(new EmptyParticipantSpotReminderNotification($registration));
                    $sent++;
                }
            });

        $this->info("Sent {$sent} participant slot reminder notifications.");
    }
}
