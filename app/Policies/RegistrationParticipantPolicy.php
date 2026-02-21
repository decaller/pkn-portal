<?php

namespace App\Policies;

use App\Models\RegistrationParticipant;
use App\Models\User;

class RegistrationParticipantPolicy
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

    public function view(
        User $user,
        RegistrationParticipant $registrationParticipant,
    ): bool {
        return $user->can("view", $registrationParticipant->registration);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(
        User $user,
        RegistrationParticipant $registrationParticipant,
    ): bool {
        return $user->can("update", $registrationParticipant->registration);
    }

    public function delete(
        User $user,
        RegistrationParticipant $registrationParticipant,
    ): bool {
        if (!$user->can("update", $registrationParticipant->registration)) {
            return false;
        }

        return $registrationParticipant->registration->canRemoveParticipants();
    }

    public function restore(
        User $user,
        RegistrationParticipant $registrationParticipant,
    ): bool {
        return false;
    }

    public function forceDelete(
        User $user,
        RegistrationParticipant $registrationParticipant,
    ): bool {
        return false;
    }
}
