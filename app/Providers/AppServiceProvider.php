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
        // URL::forceScheme('https') dihapus karena server tidak menggunakan SSL.
        // Aktifkan kembali jika server sudah dikonfigurasi dengan HTTPS.
    }
}
