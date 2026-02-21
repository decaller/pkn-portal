<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
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

    public function view(User $user, Organization $organization): bool
    {
        return $user->organizations()->whereKey($organization)->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Organization $organization): bool
    {
        return $user->isOrganizationAdmin($organization);
    }

    public function delete(User $user, Organization $organization): bool
    {
        return $organization->admin_user_id === $user->getKey();
    }

    public function restore(User $user, Organization $organization): bool
    {
        return $organization->admin_user_id === $user->getKey();
    }

    public function forceDelete(User $user, Organization $organization): bool
    {
        return false;
    }
}
