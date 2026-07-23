<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Helpers\ActivityLogger;

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
        if (env('APP_ENV') !== 'local') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        Event::listen(function (Login $event) {
            ActivityLogger::log(
                module: 'Authentication',
                action: 'Login',
                description: $event->user->name . ' logged in.',
                subject: $event->user
            );
        });

        Event::listen(function (Logout $event) {
            if ($event->user) {
                ActivityLogger::log(
                    module: 'Authentication',
                    action: 'Logout',
                    description: $event->user->name . ' logged out.',
                    subject: $event->user
                );
            }
        });
    }
}
