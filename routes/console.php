<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Console\Commands\QueueMissingDocumentCoversCommand;
use App\Console\Commands\SendEventRegistrationRemindersCommand;
use App\Console\Commands\SendParticipantSlotRemindersCommand;
use App\Console\Commands\SyncMissingSessionDocumentsCommand;
use App\Console\Commands\SyncPastEventsCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SyncPastEventsCommand::class)->dailyAt('01:00');
Schedule::command(SyncMissingSessionDocumentsCommand::class)->hourlyAt(5)->withoutOverlapping();
Schedule::command(QueueMissingDocumentCoversCommand::class)->hourlyAt(20)->withoutOverlapping();

Schedule::command(SendEventRegistrationRemindersCommand::class)->weekly()->mondays()->at('08:00');

Schedule::command(SendParticipantSlotRemindersCommand::class)->dailyAt('09:00');
