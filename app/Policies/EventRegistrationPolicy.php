<?php

namespace App\Policies;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\EventRegistration;
use App\Models\User;

class EventRegistrationPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isMainAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, EventRegistration $eventRegistration): bool
    {
        if ($eventRegistration->booker_user_id === $user->getKey()) {
            return true;
        }

        // Allow participants to view the registration
        if ($eventRegistration->participants()->where("user_id", $user->getKey())->exists()) {
            return true;
        }

        if (!$eventRegistration->organization_id) {
            return false;
        }

        return $user
            ->organizations()
            ->whereKey($eventRegistration->organization_id)
            ->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(
        User $user,
        EventRegistration $eventRegistration,
    ): bool {
        if (
            $eventRegistration->status === RegistrationStatus::Paid ||
            $eventRegistration->payment_status === PaymentStatus::Verified
        ) {
            return false;
        }

        // Only the booker can edit, not participants
        return $eventRegistration->booker_user_id === $user->getKey();
    }

    public function delete(
        User $user,
        EventRegistration $eventRegistration,
    ): bool {
        return $eventRegistration->booker_user_id === $user->getKey();
    }

    public function restore(
        User $user,
        EventRegistration $eventRegistration,
    ): bool {
        return false;
    }

    public function forceDelete(
        User $user,
        EventRegistration $eventRegistration,
    ): bool {
        return false;
    }

    public function verifyPayment(
        User $user,
        EventRegistration $eventRegistration,
    ): bool {
        return $user->isMainAdmin();
    }

    public function viewInvoice(
        User $user,
        EventRegistration $eventRegistration,
    ): bool {
        // Only the booker can view invoices, not participants
        return $eventRegistration->booker_user_id === $user->getKey();
    }

    public function updatePayment(
        User $user,
        EventRegistration $eventRegistration,
    ): bool {
        // Only the booker can update payment, not participants
        return $eventRegistration->booker_user_id === $user->getKey();
    }

    public function manageParticipants(
        User $user,
        EventRegistration $eventRegistration,
    ): bool {
        // Only the booker can manage participants, not participants
        return $eventRegistration->booker_user_id === $user->getKey();
    }
}
