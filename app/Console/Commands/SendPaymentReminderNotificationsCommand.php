<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Models\EventRegistration;
use App\Notifications\PaymentUploadReminderNotification;
use Illuminate\Console\Command;

class SendPaymentReminderNotificationsCommand extends Command
{
    protected $signature = 'notifications:send-payment-reminders';

    protected $description = 'Send reminders every 3 days to users who have not uploaded payment proof';

    public function handle(): void
    {
        $this->info('Sending payment upload reminders...');

        $sent = 0;

        EventRegistration::query()
            ->with(['booker', 'event'])
            ->where('payment_status', PaymentStatus::Unpaid)
            ->whereHas('event', function ($query): void {
                $query->whereDate('event_date', '>=', now()->startOfDay());
            })
            ->chunkById(100, function ($registrations) use (&$sent): void {
                foreach ($registrations as $registration) {
                    if (! $registration->booker) {
                        continue;
                    }

                    // Skip if a reminder was already sent in the past 3 days for this registration
                    $alreadyNotified = $registration->booker->notifications()
                        ->where('type', PaymentUploadReminderNotification::class)
                        ->where('created_at', '>=', now()->subDays(3))
                        ->whereRaw("(data::jsonb)->>'registration_id' = ?", [(string) $registration->id])
                        ->exists();

                    if ($alreadyNotified) {
                        continue;
                    }

                    $registration->booker->notify(new PaymentUploadReminderNotification($registration));
                    $sent++;
                }
            });

        $this->info("Sent {$sent} payment upload reminder notifications.");
    }
}
