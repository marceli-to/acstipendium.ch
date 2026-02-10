<?php

namespace App\Providers;

use Illuminate\Support\Facades\Mail;
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
