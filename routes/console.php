<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command(\App\Console\Commands\SyncPastEventsCommand::class)->dailyAt('01:00');

Schedule::command(\App\Console\Commands\SendEventRegistrationRemindersCommand::class)->weekly()->mondays()->at('08:00');

Schedule::command(\App\Console\Commands\SendPaymentReminderNotificationsCommand::class)->dailyAt('09:00');

Schedule::command(\App\Console\Commands\SendParticipantSlotRemindersCommand::class)->dailyAt('09:00');
