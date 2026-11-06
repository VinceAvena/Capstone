<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    /**
     * Runs once on every request, after Laravel has started.
     */
    public function boot(): void
    {
        // The market is in the Philippines, so "today" must follow Manila time.
        // Daily price posting depends on this: a price posted at 8 AM Manila
        // time must count as "today", not as yesterday in UTC.
        $timezone = config('tindapresyo.timezone', 'Asia/Manila');
        config(['app.timezone' => $timezone]);
        date_default_timezone_set($timezone);

        // Use our own simple pagination design (resources/views/pagination/tp.blade.php).
        Paginator::defaultView('pagination.tp');
        Paginator::defaultSimpleView('pagination.tp');
    }
}
