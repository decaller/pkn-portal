<?php

namespace App\Observers;

use App\Models\Event;
use App\Models\Document;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

use App\Services\TikaService;
use Illuminate\Support\Facades\Storage;

class EventObserver
{

    public function __construct(protected TikaService $tika) {}
    
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
        //
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
        // 1. Check if there is a rundown (sessions)
        if (empty($event->rundown)) {
            return;
        }

        // 2. Loop through every session in the JSON
        foreach ($event->rundown as $session) {
            
            // Check if this session has files
            if (!empty($session['session_files'])) {
                
                // Filament stores files as an array of paths: ['events/graduation/file.pdf']
                foreach ($session['session_files'] as $filePath) {

                    // 1. Check if record exists in DB already
                    if (Document::where('file_path', $filePath)->exists()) continue;

                    $disk = 'public';

                    // SAFETY: If the file isn't there yet, wait 500ms and try one more time
                    if (!Storage::disk($disk)->exists($filePath)) {
                        usleep(500000); 
                    }
                    
                    // 3. THE MAGIC: Check if this file is already in Documents
                    // We use the file path as the unique ID
                    $exists = Document::where('file_path', $filePath)->exists();

                    // 1. Get file from Storage
                    $fileContent = Storage::disk($disk)->get($filePath);

                    if (!$fileContent) {
                        Log::error("Tika Error: Could not retrieve file content for {$filePath}");
                        continue;
                    }

                    // 2. Scan with Tika
                    $extraction = $this->tika->extractText($fileContent);

                    if (!$exists) {
                        // 4. Create the Document automatically
                        Document::create([
                            'event_id'          => $event->id,
                            'session_slug'      => $session['slug'] ?? Str::slug($session['title']),
                            'title'             => $session['title'] . ' Attachment',
                            'slug'              => Str::slug($event->title . '-' . Str::random(5)),
                            'file_path'         => $filePath,
                            'original_filename' => basename($filePath),
                            
                            // Tika Data
                            'content'           => $extraction['content'] ?? null,
                            'mime_type'         => $extraction['mime_type'] ?? null,
                            'metadata'          => $extraction['metadata'] ?? [],
                            'description'       => "Auto-extracted from Session: " . ($session['title'] ?? 'Untitled'),
                        ]);
                        
                        Log::info("Auto-created document for file: {$filePath}");
                    }
                }
            }
        }
    }
}
