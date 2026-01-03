<?php

namespace App\Providers;

use App\Clients\GuzzleHttpClient;
use App\Contracts\Clients\HttpClientInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\MovieApiServiceInterface;
use App\Repositories\EloquentUserRepository;
use App\Services\AuthService;
use App\Services\External\TmdbService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // HTTP Client
        $this->app->singleton(HttpClientInterface::class, GuzzleHttpClient::class);

        // Movie API Service
        $this->app->singleton(MovieApiServiceInterface::class, TmdbService::class);

        // User Repository
        $this->app->singleton(UserRepositoryInterface::class, EloquentUserRepository::class);

        // Auth Service
        $this->app->singleton(AuthServiceInterface::class, AuthService::class);
    }

    public function boot(): void {}
}
