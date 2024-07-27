<?php

namespace App\Providers;

use App\Interfaces\DataManagementInterface;
use App\Services\RedisService;
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
        $this->app->bind(DataManagementInterface::class, RedisService::class);
    }
}
