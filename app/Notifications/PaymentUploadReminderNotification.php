<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\EventRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentUploadReminderNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly EventRegistration $registration) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => NotificationType::PaymentUploadReminder->value,
            'registration_id' => $this->registration->id,
            'event_id' => $this->registration->event_id,
            'event_title' => $this->registration->event?->title,
            'event_slug' => $this->registration->event?->slug,
            'message' => __('Please complete your Midtrans payment for event :title.', ['title' => $this->registration->event?->title]),
        ];
    }
}
