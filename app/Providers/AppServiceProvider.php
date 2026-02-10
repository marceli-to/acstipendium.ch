<?php

namespace App\Providers;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
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

        // Intercept all outgoing mail and redirect to MAIL_TO when not in production.
        // Prevents accidentally emailing real users during development/staging.
        if (! $this->app->environment('production')) {
            $interceptTo = config('mail.to.address', env('MAIL_TO'));

            if ($interceptTo) {
                Event::listen(MessageSending::class, function (MessageSending $event) use ($interceptTo) {
                    $message = $event->message;
                    $message->to($interceptTo);
                    $message->cc();
                    $message->bcc();
                });
            }
        }
    }
}
