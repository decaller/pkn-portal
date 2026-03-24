<?php

use App\Jobs\ProcessDocumentTika;
use App\Models\Document;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('event rundown session files are automatically queued for processing', function () {
    Queue::fake();
    Storage::fake('public');

    // Create a dummy file in storage
    $filePath = 'events/test-event/sessions/test-file.pdf';
    Storage::disk('public')->put($filePath, 'dummy content');

    // Create an event with a rundown session file
    $event = Event::factory()->create([
        'slug' => 'test-event',
        'rundown' => [
            [
                'type' => 'advanced',
                'data' => [
                    'title' => 'Test Session',
                    'session_files' => [
                        $filePath,
                    ],
                ],
            ],
        ],
    ]);

    // Assert that the job was dispatched via the observer
    Queue::assertPushed(ProcessDocumentTika::class, function ($job) use ($event, $filePath) {
        return $job->eventId === $event->id && $job->filePath === $filePath;
    });
});

test('stale documents are removed when session files are removed from rundown', function () {
    Queue::fake();
    Storage::fake('public');

    $filePath = 'events/test-event/sessions/test-file.pdf';
    Storage::disk('public')->put($filePath, 'dummy content');

    $event = Event::factory()->create([
        'slug' => 'test-event',
        'rundown' => [
            [
                'type' => 'advanced',
                'data' => [
                    'title' => 'Test Session',
                    'session_files' => [$filePath],
                ],
            ],
        ],
    ]);

    // Manually create a document to simulate it being processed
    Document::create([
        'event_id' => $event->id,
        'file_path' => $filePath,
        'title' => 'test-file.pdf',
        'slug' => 'test-doc',
        'mime_type' => 'application/pdf',
    ]);

    expect(Document::where('file_path', $filePath)->exists())->toBeTrue();

    // Update event to remove the session file
    $event->update([
        'rundown' => [],
    ]);

    // Assert that the document was deleted
    expect(Document::where('file_path', $filePath)->exists())->toBeFalse();
});
