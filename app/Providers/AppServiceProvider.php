<?php

namespace App\Providers;

use App\Models\EventRegistration;
use Illuminate\Support\ServiceProvider;
use App\Models\Event;
use App\Models\Organization;
use App\Models\RegistrationParticipant;
use App\Observers\EventObserver;
use App\Observers\EventRegistrationObserver;
use App\Policies\EventRegistrationPolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\RegistrationParticipantPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;

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
        Event::observe(EventObserver::class);
        EventRegistration::observe(EventRegistrationObserver::class);

        Gate::policy(Organization::class, OrganizationPolicy::class);
        Gate::policy(EventRegistration::class, EventRegistrationPolicy::class);
        Gate::policy(
            RegistrationParticipant::class,
            RegistrationParticipantPolicy::class,
        );
        Gate::policy(\App\Models\User::class, UserPolicy::class);
    }
}
