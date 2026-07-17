<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- Tambahkan ini di atas

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Paksa Laravel menggunakan HTTPS jika diakses lewat Ngrok/Production
        if (env('APP_ENV') !== 'local' || request()->header('x-forwarded-proto') === 'https') {
            URL::forceScheme('https');
        }

        // Cara paling aman untuk skripsi (paksa 100% HTTPS)
        URL::forceScheme('https');
    }
}
