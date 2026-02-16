<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Event;
use App\Observers\EventObserver;
use Filament\Auth\Pages\Login;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;


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


        // Login::tokenizeNameFieldUsing(fn () => 'phone_number');
    
        // // This tells the UI to say "Phone Number" instead of "Email"
        // Login::resolveConfirmationLabelUsing(fn () => 'Phone Number');
    }
}
