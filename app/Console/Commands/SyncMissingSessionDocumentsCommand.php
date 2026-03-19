<?php

namespace App\Console\Commands;

use App\Jobs\ProcessDocumentTika;
use App\Models\Document;
use App\Models\Event;
use App\Services\EventSessionFileService;
use Illuminate\Console\Command;

class SyncMissingSessionDocumentsCommand extends Command
{
    protected $signature = 'documents:sync-missing-session-files {--force : Queue every session file even when a document already exists}';

    protected $description = 'Queue document extraction for session files that are missing from the documents table';

    public function handle(EventSessionFileService $sessionFiles): int
    {
        $force = (bool) $this->option('force');
        $queued = 0;

        Event::query()
            ->each(function (Event $event) use ($sessionFiles, $force, &$queued): void {
                foreach ($sessionFiles->entries($event) as $entry) {
                    if (! $force && Document::query()->where('file_path', $entry['file_path'])->exists()) {
                        continue;
                    }

                    ProcessDocumentTika::dispatch(
                        eventId: $event->id,
                        filePath: $entry['file_path'],
                        sessionTitle: $entry['session_title'],
                        sessionSlug: $entry['session_slug'],
                        source: 'session'
                    );

                    $queued++;
                }
            });

        $this->info("Queued {$queued} session document job(s).");

        return self::SUCCESS;
    }
}
