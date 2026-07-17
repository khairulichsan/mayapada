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

    // public function boot(): void
    // {
    //     // Paksa Laravel menggunakan HTTPS jika diakses lewat Ngrok/Production
    //     if (env('APP_ENV') !== 'local' || request()->header('x-forwarded-proto') === 'https') {
    //         URL::forceScheme('https');
    //     }

    //     // Cara paling aman untuk skripsi (paksa 100% HTTPS)
    //     URL::forceScheme('https');
    // }
    public function boot()
{
    if (config('app.env') === 'production') {
        \Illuminate\Support\Facades\URL::forceScheme('https');
    }
}
}
