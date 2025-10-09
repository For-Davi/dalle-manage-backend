<?php

namespace App\Providers;

use App\Models\ProductVariant;
use App\Models\User;
use App\Observers\StockCriticalProductObserver;
use Illuminate\Support\ServiceProvider;
use Spatie\Prometheus\Facades\Prometheus;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        ProductVariant::observe(StockCriticalProductObserver::class);
        Prometheus::addGauge('user_count')->value(fn () => User::count());
    }
}
