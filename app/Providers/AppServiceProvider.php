<?php

namespace App\Providers;

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
        try {
            $appTitle = \App\Models\AppSetting::get('app_title', 'Sistem Monitoring Jaringan');
        } catch (\Throwable $e) {
            $appTitle = 'Sistem Monitoring Jaringan';
        }

        \Illuminate\Support\Facades\View::share('appTitle', $appTitle);
    }
}
