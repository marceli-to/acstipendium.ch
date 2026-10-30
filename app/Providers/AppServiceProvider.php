<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Statamic\Statamic;

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
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Statamic::vite('app', [
        //     'resources/js/cp.js',
        //     'resources/css/cp.css',
        // ]);

        // Redirect all outgoing mail to MAIL_TO on local/staging environments
        if ($this->app->environment('local', 'staging')) {
            $alwaysTo = config('mail.always_to');

            if ($alwaysTo) {
                Mail::alwaysTo($alwaysTo);
            } else {
                throw new \RuntimeException('MAIL_TO must be set on local/staging to prevent real emails from being sent.');
            }
        }
    }
}
