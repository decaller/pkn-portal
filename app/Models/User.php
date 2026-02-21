<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\HasTenants;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        "name",
        "email",
        "phone_number",
        "password",
        "is_super_admin",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ["password", "remember_token"];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "password" => "hashed",
            "is_super_admin" => "boolean",
        ];
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->withPivot("role")
            ->withTimestamps();
    }

    public function administeredOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class, "admin_user_id");
    }

    public function bookedRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class, "booker_user_id");
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if (!$tenant instanceof Organization) {
            return false;
        }

        return $this->isMainAdmin() ||
            $this->organizations()->whereKey($tenant)->exists();
    }

    public function getTenants(Panel $panel): array|Collection
    {
        return $this->organizations()->get();
    }

    public function isOrganizationAdmin(Organization $organization): bool
    {
        if ($organization->admin_user_id === $this->getKey()) {
            return true;
        }

        return $this->organizations()
            ->whereKey($organization)
            ->wherePivot("role", "admin")
            ->exists();
    }

    public function isMainAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            "admin" => $this->isMainAdmin(),
            "user" => true,
            default => false,
        };
    }
}
