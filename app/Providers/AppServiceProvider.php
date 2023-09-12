<?php

namespace App\Providers;

use App\Services\CalendarEvent;
use Illuminate\Support\ServiceProvider;
use App\Libs\GoogleCalendar;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
