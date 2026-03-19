<?php

namespace App\Filament\User\Resources\Users;

use App\Filament\User\Resources\Users\Pages\CreateUser;
use App\Filament\User\Resources\Users\Pages\EditUser;
use App\Filament\User\Resources\Users\Pages\ListUserActivities;
use App\Filament\User\Resources\Users\Pages\ListUsers;
use App\Filament\User\Resources\Users\Pages\ViewUser;
use App\Filament\User\Resources\Users\Schemas\UserForm;
use App\Filament\User\Resources\Users\Schemas\UserInfolist;
use App\Filament\User\Resources\Users\Tables\UsersTable;
use App\Models\Organization;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    public static function getNavigationGroup(): ?string
    {
        return __('Membership');
    }

    public static function getNavigationLabel(): string
    {
        return __('Organization users');
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $authUser = auth()->user();

        if (! $authUser) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        if ($authUser->isMainAdmin()) {
            return parent::getEloquentQuery()->with('organizations');
        }

        $organizationIds = $authUser->organizations()->pluck('organizations.id');

        return parent::getEloquentQuery()
            ->whereHas('organizations', fn (Builder $query) => $query->whereIn('organizations.id', $organizationIds))
            ->with('organizations');
    }

    public static function canCreate(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->isMainAdmin()) {
            return true;
        }

        $tenant = Filament::getTenant();

        if ($tenant instanceof Organization && $user->isOrganizationAdmin($tenant)) {
            return true;
        }

        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'activities' => ListUserActivities::route('/{record}/activities'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    /**
     * Determines if the current user can view the activity log of a given user.
     *
     * - Super admins can see everyone.
     * - Org admins can see members of their organizations.
     * - Regular users can only see their own activity.
     */
    public static function canViewActivities(User $target): bool
    {
        /** @var User|null $auth */
        $auth = auth()->user();

        if (! $auth) {
            return false;
        }

        if ($auth->isMainAdmin()) {
            return true;
        }

        if ($auth->getKey() === $target->getKey()) {
            return true;
        }

        $authOrgIds = $auth->organizations()->wherePivot('role', 'admin')->pluck('organizations.id');

        return $target->organizations()->whereIn('organizations.id', $authOrgIds)->exists();
    }
}
