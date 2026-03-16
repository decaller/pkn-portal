<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\Event;
use App\Services\TikaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessDocumentTika implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function uniqueId(): string
    {
        return $this->filePath;
    }

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $eventId,
        public string $filePath,
        public string $sessionTitle,
        public ?string $sessionSlug = null,
        public string $source = 'session',
    ) {}

    /**
     * Execute the job.
     */
    public function handle(TikaService $tika): void
    {
        $event = Event::find($this->eventId);

        if (! $event) {
            return;
        }

        $disk = 'public';

        if (! Storage::disk($disk)->exists($this->filePath)) {
            // Retry later if file not found (might still be moving)
            $this->release(10);

            return;
        }

        $existingDocument = Document::where('file_path', $this->filePath)->first();
        $lastModified = Storage::disk($disk)->lastModified($this->filePath);
        if ($existingDocument && $existingDocument->updated_at?->timestamp >= $lastModified) {
            return;
        }

        $fileContent = Storage::disk($disk)->get($this->filePath);

        if (! $fileContent) {
            Log::error("Tika Job Error: Could not retrieve file content for {$this->filePath}");

            return;
        }

        // Scan with Tika
        $extraction = $tika->extractText($fileContent) ?? [
            'content' => null,
            'mime_type' => null,
            'metadata' => [],
        ];

        $payload = [
            'event_id' => $event->id,
            'session_slug' => $this->sessionSlug ?? Str::slug($this->sessionTitle),
            'title' => $this->sessionTitle.' Attachment',
            'file_path' => $this->filePath,
            'original_filename' => basename($this->filePath),
            'content' => $extraction['content'] ?? null,
            'mime_type' => $extraction['mime_type'] ?? null,
            'metadata' => $extraction['metadata'] ?? [],
            'description' => "Auto-extracted from {$this->source}: ".($this->sessionTitle ?? 'Untitled'),
        ];

        if ($existingDocument) {
            $existingDocument->fill($payload)->save();
        } else {
            $payload['slug'] = Str::slug($event->title.'-'.Str::random(5));
            Document::create($payload);
        }

        Log::info("Auto-processed document via Job for file: {$this->filePath}");
    }
}
