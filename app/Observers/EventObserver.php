<?php

namespace App\Observers;

use App\Models\Document;
use App\Models\Event;
use App\Models\User;
use App\Notifications\NewEventOpenForRegistrationNotification;
use App\Notifications\PastEventPostedOrUpdatedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventObserver
{
    public function __construct() {}

    /**
     * Handle the Event "created" event.
     */
    public function created(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "updated" event.
     */
    public function updated(Event $event): void
    {
        // Notify all users when registration opens
        if ($event->wasChanged('allow_registration') && $event->allow_registration) {
            $this->notifyAllUsers(new NewEventOpenForRegistrationNotification($event));
        }

        // Notify all users when a past event is published or updated while published
        if ($event->event_date?->isPast()) {
            if ($event->is_published && ($event->wasChanged('is_published') || $event->wasChanged('title') || $event->wasChanged('description') || $event->wasChanged('photos') || $event->wasChanged('documentation'))) {
                $this->notifyAllUsers(new PastEventPostedOrUpdatedNotification($event));
            }
        }
    }

    /**
     * Handle the Event "deleted" event.
     */
    public function deleted(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "restored" event.
     */
    public function restored(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "force deleted" event.
     */
    public function forceDeleted(Event $event): void
    {
        //
    }

    public function saved(Event $event): void
    {
        Log::debug("EventObserver@saved triggered for Event ID: {$event->id}");
        $sessionFilePaths = [];

        // 1. Process rundown (sessions) files
        if (! empty($event->rundown)) {
            foreach ($event->rundown as $session) {
                // Builder blocks store data inside a 'data' array
                $data = $session['data'] ?? $session;
                $files = $data['session_files'] ?? [];

                if (! empty($files)) {
                    foreach ($files as $filePath) {
                        $sessionFilePaths[] = $filePath;

                        Log::debug("Dispatching Tika job for file: {$filePath}");
                        dispatch(new \App\Jobs\ProcessDocumentTika(
                            eventId: $event->id,
                            filePath: $filePath,
                            sessionTitle: $data['title'] ?? 'Untitled',
                            sessionSlug: $data['slug'] ?? null,
                            source: 'session'
                        ));
                    }
                }
            }
        }

        Log::debug("Found " . count($sessionFilePaths) . " total session files in rundown.");

        // Clean up documents for removed session files.
        $sessionFilePaths = array_values(array_unique($sessionFilePaths));
        $staleSessionDocs = Document::query()
            ->where('event_id', $event->id)
            ->where('file_path', 'like', 'events/%/sessions/%');
        if (! empty($sessionFilePaths)) {
            $staleSessionDocs->whereNotIn('file_path', $sessionFilePaths);
        }
        $staleSessionDocs->delete();

        // 2. Skip root event documentation files (not processed into documents)
    }

    private function notifyAllUsers(object $notification): void
    {
        User::query()->whereHas('organizations')->chunkById(100, function ($users) use ($notification): void {
            foreach ($users as $user) {
                try {
                    $user->notify(clone $notification);
                } catch (\Exception $e) {
                    Log::error("Failed to send notification to user {$user->id}: ".$e->getMessage());
                }
            }
        });
    }
}
