<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\User;
use App\Notifications\NewEventOpenForRegistrationNotification;
use Illuminate\Console\Command;

class SendEventRegistrationRemindersCommand extends Command
{
    protected $signature = 'notifications:send-registration-reminders';

    protected $description = 'Send weekly reminders to users who have not registered for open events';

    public function handle(): void
    {
        $this->info('Sending registration reminders...');

        $openEvents = Event::query()
            ->where('allow_registration', true)
            ->whereDate('event_date', '>=', now()->startOfDay())
            ->get();

        $sent = 0;

        foreach ($openEvents as $event) {
            // Get IDs of users who already have a registration for this event (via their organization)
            $registeredOrganizationIds = $event->registrations()->pluck('organization_id');

            User::query()
                ->whereHas('organizations', function ($query) use ($registeredOrganizationIds): void {
                    $query->whereNotIn('organizations.id', $registeredOrganizationIds);
                })
                ->chunkById(100, function ($users) use ($event, &$sent): void {
                    foreach ($users as $user) {
                        // Skip if already notified in the past 7 days for this event
                        $alreadyNotified = $user->notifications()
                            ->where('type', NewEventOpenForRegistrationNotification::class)
                            ->where('created_at', '>=', now()->subDays(7))
                            ->whereRaw("(data::jsonb)->>'event_id' = ?", [(string) $event->id])
                            ->exists();

                        if ($alreadyNotified) {
                            continue;
                        }

                        $user->notify(new NewEventOpenForRegistrationNotification($event));
                        $sent++;
                    }
                });
        }

        $this->info("Sent {$sent} registration reminder notifications.");
    }
}
