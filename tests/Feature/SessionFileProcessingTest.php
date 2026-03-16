<?php

use App\Models\Document;
use App\Models\Event;
use App\Services\TikaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\mock;

uses(RefreshDatabase::class);

it('processes session files and reprocesses on overwrite', function () {
    Storage::fake('public');

    $path = 'events/test-event/sessions/session-1.pdf';
    Storage::disk('public')->put($path, 'first content');

    mock(TikaService::class)
        ->shouldReceive('extractText')
        ->twice()
        ->andReturn(
            [
                'content' => 'first extracted',
                'mime_type' => 'application/pdf',
                'metadata' => ['source' => 'first'],
            ],
            [
                'content' => 'second extracted',
                'mime_type' => 'application/pdf',
                'metadata' => ['source' => 'second'],
            ],
        );

    $event = Event::factory()->create([
        'slug' => 'test-event',
        'rundown' => [
            [
                'title' => 'Session 1',
                'slug' => 'session-1',
                'session_files' => [$path],
            ],
        ],
    ]);

    $document = Document::where('file_path', $path)->first();
    expect($document)->not->toBeNull()
        ->and($document->content)->toBe('first extracted');

    sleep(1);
    Storage::disk('public')->put($path, 'second content');

    $event->update(['title' => 'Test Event Updated']);

    $document->refresh();
    expect($document->content)->toBe('second extracted');
});

it('cleans up documents when session files are removed', function () {
    Storage::fake('public');

    $path = 'events/test-event/sessions/session-2.pdf';
    Storage::disk('public')->put($path, 'content');

    mock(TikaService::class)
        ->shouldReceive('extractText')
        ->once()
        ->andReturn([
            'content' => 'extracted',
            'mime_type' => 'application/pdf',
            'metadata' => ['source' => 'single'],
        ]);

    $event = Event::factory()->create([
        'slug' => 'test-event',
        'rundown' => [
            [
                'title' => 'Session 2',
                'slug' => 'session-2',
                'session_files' => [$path],
            ],
        ],
    ]);

    expect(Document::where('file_path', $path)->exists())->toBeTrue();

    $event->update(['rundown' => []]);

    expect(Document::where('file_path', $path)->exists())->toBeFalse();
});
