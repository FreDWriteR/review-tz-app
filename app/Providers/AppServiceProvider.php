<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repository\CartManager;
use Psr\Log\LoggerInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartManager::class, function($app) {
            $redis = $app['config']['database.redis.default'];
            return new CartManager(
                $redis['host'],
                (int)$redis['port'] ?? 6379,
                $redis['password'] ?? null,
                $app->make(LoggerInterface::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
