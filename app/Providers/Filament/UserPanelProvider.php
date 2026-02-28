<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Tenancy\EditOrganizationProfile;
use App\Filament\Pages\Tenancy\RegisterOrganization;
use App\Filament\User\Auth\Login;
use App\Filament\User\Auth\RegisterEvent;
use App\Filament\User\Widgets\AvailableRegistrationEventsWidget;
use App\Filament\User\Widgets\EventRegistrationsTableWidget;
use App\Filament\User\Widgets\LatestNewsWidget;
use App\Filament\User\Widgets\WelcomeWidget;
use App\Models\Organization;
use CraftForge\FilamentLanguageSwitcher\FilamentLanguageSwitcherPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('user')
            ->path('user')
            ->brandName('PKN User Panel')
            ->login(Login::class)
            ->registration(RegisterEvent::class)
            ->tenant(Organization::class, 'slug', 'organization')
            ->tenantRegistration(RegisterOrganization::class)
            ->tenantProfile(EditOrganizationProfile::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(
                in: app_path('Filament/User/Resources'),
                for: "App\Filament\User\Resources",
            )
            ->discoverPages(
                in: app_path('Filament/User/Pages'),
                for: "App\Filament\User\Pages",
            )
            ->pages([Dashboard::class])
            ->discoverWidgets(
                in: app_path('Filament/User/Widgets'),
                for: "App\Filament\User\Widgets",
            )
            ->widgets([
                WelcomeWidget::class,
                AvailableRegistrationEventsWidget::class,
                EventRegistrationsTableWidget::class,
                LatestNewsWidget::class,
            ])
            ->renderHook(
                'panels::scripts.after',
                fn () => view('filament.user.log-listener'),
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([Authenticate::class])
            ->plugins([
                FilamentLanguageSwitcherPlugin::make()
                    ->locales(['en', 'id'])
                    ->rememberLocale()
                    ->showOnAuthPages(),
            ]);
    }
}
