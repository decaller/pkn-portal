<?php

namespace App\Console\Commands;

use App\Jobs\ProcessDocumentCover;
use App\Models\Document;
use Illuminate\Console\Command;

class QueueMissingDocumentCoversCommand extends Command
{
    protected $signature = 'documents:queue-missing-covers {--force : Regenerate covers even if an image already exists}';

    protected $description = 'Queue cover generation for documents that are missing cover images';

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $queued = 0;

        Document::query()
            ->whereNotNull('file_path')
            ->where(function ($query): void {
                $query->whereNull('cover_image')
                    ->orWhere('cover_image', '');
            })
            ->orderBy('id')
            ->chunkById(200, function ($documents) use (&$queued, $force): void {
                foreach ($documents as $document) {
                    ProcessDocumentCover::dispatch($document->id, $force);
                    $queued++;
                }
            });

        $this->info("Queued {$queued} missing cover job(s).");

        return self::SUCCESS;
    }
}
