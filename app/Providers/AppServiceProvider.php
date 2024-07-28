<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\JobRepositoryContract;
use App\Contracts\ScrapperContract;
use App\Services\RedisService;
use App\Services\SpatieScrapperService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->app->bind(JobRepositoryContract::class, RedisService::class);
        $this->app->bind(ScrapperContract::class, SpatieScrapperService::class);
    }
}
