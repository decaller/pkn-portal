<?php

namespace App\Jobs;

use App\Models\Document;
use App\Services\DocumentCoverService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessDocumentCover implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $documentId,
        public bool $force = false,
    ) {}

    public function uniqueId(): string
    {
        return (string) $this->documentId;
    }

    public function handle(DocumentCoverService $covers): void
    {
        $document = Document::find($this->documentId);
        if (! $document) {
            return;
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($document->file_path)) {
            Log::warning("Cover generation skipped: missing source file for document {$document->id}.");

            return;
        }

        $lastModified = $disk->lastModified($document->file_path);
        $covers->ensureCover($document, $lastModified, $this->force);
    }
}
