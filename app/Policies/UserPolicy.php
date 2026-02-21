<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function before(User $user, string $ability): bool | null
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

    public function view(User $user, User $target): bool
    {
        if ($user->is($target)) {
            return true;
        }

        return $user->organizations()
            ->whereIn('organizations.id', $target->organizations()->pluck('organizations.id'))
            ->exists();
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, User $target): bool
    {
        $adminOrgIds = $user->administeredOrganizations()->pluck('id');

        if ($adminOrgIds->isEmpty()) {
            return false;
        }

        return $target->organizations()->whereIn('organizations.id', $adminOrgIds)->exists();
    }

    public function delete(User $user, User $target): bool
    {
        return false;
    }

    public function restore(User $user, User $target): bool
    {
        return false;
    }

    public function forceDelete(User $user, User $target): bool
    {
        return false;
    }
}
