<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Pelanggaran;
use App\Models\PelaksanaanSanksi;
use App\Observers\PelanggaranObserver;
use App\Observers\PelaksanaanSanksiObserver;

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
        Pelanggaran::observe(PelanggaranObserver::class);
        PelaksanaanSanksi::observe(PelaksanaanSanksiObserver::class);
    }
}
