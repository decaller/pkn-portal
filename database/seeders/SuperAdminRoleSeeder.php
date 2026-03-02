<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SuperAdminRoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => config('auth.defaults.guard', 'web'),
        ]);

        User::query()
            ->where('is_super_admin', true)
            ->each(function (User $user) use ($role): void {
                if (! $user->hasRole($role->name)) {
                    $user->assignRole($role);
                }
            });
    }
}
