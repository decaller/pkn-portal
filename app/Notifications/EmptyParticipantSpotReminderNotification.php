<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\EventRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EmptyParticipantSpotReminderNotification extends Notification
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
            'type' => NotificationType::EmptyParticipantSpotReminder->value,
            'registration_id' => $this->registration->id,
            'event_id' => $this->registration->event_id,
            'event_title' => $this->registration->event?->title,
            'event_slug' => $this->registration->event?->slug,
            'message' => __('Your registration for event :title still has empty participant slots. Please fill them in.', ['title' => $this->registration->event?->title]),
        ];
    }
}
