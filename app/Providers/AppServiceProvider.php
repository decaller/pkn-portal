<?php

namespace App\Providers;

use App\Models\Document;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Organization;
use App\Models\RegistrationParticipant;
use App\Models\User;
use App\Observers\DocumentObserver;
use App\Observers\EventObserver;
use App\Observers\EventRegistrationObserver;
use App\Policies\EventRegistrationPolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\RegistrationParticipantPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            $appUrl = rtrim((string) config('app.url'), '/');

            if ($appUrl !== '') {
                // Force canonical host/port from APP_URL to avoid wrong redirects like https://host:80.
                URL::forceRootUrl($appUrl);
            }

            if (str_starts_with($appUrl, 'https://')) {
                URL::forceScheme('https');
            }
        }

        Event::observe(EventObserver::class);
        Document::observe(DocumentObserver::class);
        EventRegistration::observe(EventRegistrationObserver::class);

        Gate::policy(Organization::class, OrganizationPolicy::class);
        Gate::policy(EventRegistration::class, EventRegistrationPolicy::class);
        Gate::policy(
            RegistrationParticipant::class,
            RegistrationParticipantPolicy::class,
        );
        Gate::policy(User::class, UserPolicy::class);
    }
}
