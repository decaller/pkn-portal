<?php

namespace App\Enums;

enum NotificationType: string
{
    case NewEventOpenForRegistration = 'new_event_open_for_registration';
    case PastEventPostedOrUpdated = 'past_event_posted_or_updated';
    case PaymentUploadReminder = 'payment_upload_reminder';
    case PaymentApproved = 'payment_approved';
    case EmptyParticipantSpotReminder = 'empty_participant_spot_reminder';
}
