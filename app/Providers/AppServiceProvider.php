<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Events\MessageSending; // ADDED
use Illuminate\Support\Facades\Event; // ADDED
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Prevent N+1 query lazy loading in development and testing
        Model::preventLazyLoading(! $this->app->isProduction());

        // ADDED: every email built on the shared mail template shows the ISU seal from cid:isu-logo
        // (resources/views/vendor/mail/html/header.blade.php), so the seal is attached once here for all of them.
        Event::listen(function (MessageSending $event) {
            if (str_contains((string) $event->message->getHtmlBody(), 'cid:isu-logo')) {
                $event->message->embedFromPath(public_path('assets/images/isu_logo_email.png'), 'isu-logo');
            }
        });
    }
}
