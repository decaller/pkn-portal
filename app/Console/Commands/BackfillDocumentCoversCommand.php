<?php

namespace App\Console\Commands;

use App\Jobs\ProcessDocumentCover;
use App\Models\Document;
use Illuminate\Console\Command;

class BackfillDocumentCoversCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:backfill-covers {--force : Regenerate covers even if they look up-to-date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue cover generation for existing documents';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $this->info('Queuing document cover generation...');

        $queued = 0;

        Document::query()
            ->whereNotNull('file_path')
            ->orderBy('id')
            ->chunkById(200, function ($documents) use (&$queued, $force) {
                foreach ($documents as $document) {
                    ProcessDocumentCover::dispatch($document->id, $force);
                    $queued++;
                }
            });

        $this->info("Queued {$queued} document cover jobs.");

        return self::SUCCESS;
    }
}
