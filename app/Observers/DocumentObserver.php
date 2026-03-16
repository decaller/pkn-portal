<?php

namespace App\Observers;

use App\Jobs\ProcessDocumentTika;
use App\Models\Document;
use Illuminate\Support\Facades\Log;

class DocumentObserver
{
    /**
     * Handle the Document "saved" event.
     */
    public function saved(Document $document): void
    {
        // Only trigger Tika if file_path is present
        if (! $document->file_path) {
            return;
        }

        // If it was just created, file was changed, OR content was manually cleared
        $shouldProcess = $document->wasRecentlyCreated
            || $document->wasChanged('file_path')
            || ($document->wasChanged('content') && empty($document->content));

        // But only if it doesn't have content yet (to avoid loops when the job updates it)
        if ($shouldProcess && empty($document->content)) {
            Log::debug("DocumentObserver: Dispatching Tika job for document: {$document->file_path}");

            dispatch_sync(new ProcessDocumentTika(
                eventId: $document->event_id,
                filePath: $document->file_path,
                sessionTitle: $document->title ?? 'Manual Upload',
                sessionSlug: $document->session_slug,
                source: 'manual_upload'
            ));
        }
    }
}
