<?php

namespace App\Console\Commands;

use App\Enums\RegistrationStatus;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncPastEventsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:sync-past';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up completed events and append them to the User past_events history array';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting past events sync...');

        // Find all paid registrations for events that occurred in the past
        $registrations = EventRegistration::with(['event', 'booker', 'participants.user'])
            ->where('status', RegistrationStatus::Paid)
            ->whereHas('event', function ($query) {
                $query->where('event_date', '<', now()->startOfDay());
            })
            ->get();

        $processedCount = 0;

        DB::transaction(function () use ($registrations, &$processedCount) {
            foreach ($registrations as $registration) {
                $eventId = $registration->event_id;

                // 1. Add to Booker's past events
                if ($registration->booker) {
                    $this->appendEventToUser($registration->booker, $eventId);
                }

                // 2. Add to all Participants' past events
                foreach ($registration->participants as $participant) {
                    if ($participant->user) {
                        $this->appendEventToUser($participant->user, $eventId);
                    }
                }

                // 3. Mark the registration as Closed so it is filtered out next time
                $registration->update([
                    'status' => RegistrationStatus::Closed,
                ]);

                $processedCount++;
            }
        });

        $this->info("Successfully synced and closed {$processedCount} registrations.");
    }

    private function appendEventToUser(User $user, int $eventId): void
    {
        // Get existing past events or initialize an empty array
        $pastEvents = $user->past_events ?? [];

        // Cast to array if it is unexpectedly something else
        if (! is_array($pastEvents)) {
            $pastEvents = (array) $pastEvents;
        }

        // Add if not already present
        if (! in_array($eventId, $pastEvents, true)) {
            $pastEvents[] = $eventId;
            $user->past_events = array_values(array_unique($pastEvents));
            $user->save();
        }
    }
}
