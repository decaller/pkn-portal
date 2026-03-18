<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendPaymentReminderNotificationsCommand extends Command
{
    protected $signature = 'notifications:send-payment-reminders';

    protected $description = 'Legacy command retained for compatibility. Midtrans payments no longer use upload reminders.';

    public function handle(): int
    {
        $this->info('Payment upload reminders are retired. Midtrans Snap is now the active payment flow.');

        return self::SUCCESS;
    }
}
