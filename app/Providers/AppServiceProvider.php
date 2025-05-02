<?php

namespace App\Providers;

use App\Infrastructure\Contracts\CartStorageInterface;
use App\Repository\RedisCartStorage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartStorageInterface::class, function($app) {
            $config = config('database.redis.default');
            $sessionId = Session::getId();
            return new RedisCartStorage(
                $config['host'],
                $config['port'] ?? 6379,
                $config['password'] ?? null,
                $sessionId
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
