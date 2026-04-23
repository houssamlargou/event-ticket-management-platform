<?php

namespace App\Providers;

use App\Repositories\AuthRepository;
use App\Repositories\AuthRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\EventRepository;
use App\Repositories\EventRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(EventRepositoryInterface::class, EventRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
