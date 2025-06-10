<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        if (app()->environment('local')) {
            // Gunakan APP_URL dari .env (Ngrok)
            URL::forceRootUrl(config('app.url'));

            // Paksa semua URL gunakan https
            URL::forceScheme('https');
        }
    }
}