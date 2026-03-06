<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PastEventPostedOrUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Event $event) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => NotificationType::PastEventPostedOrUpdated->value,
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'event_slug' => $this->event->slug,
            'message' => __('Past event :title has been posted or updated.', ['title' => $this->event->title]),
        ];
    }
}
