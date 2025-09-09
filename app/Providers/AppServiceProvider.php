<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ResultRepositoryInterface;
use App\Repositories\ResultRepository;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
         $this->app->bind(ResultRepositoryInterface::class, ResultRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
