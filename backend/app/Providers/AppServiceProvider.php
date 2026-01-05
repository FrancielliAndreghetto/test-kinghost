<?php

namespace App\Providers;

use App\Clients\GuzzleHttpClient;
use App\Contracts\Clients\HttpClientInterface;
use App\Contracts\Repositories\FavoriteRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\FavoriteServiceInterface;
use App\Contracts\Services\MovieApiServiceInterface;
use App\Repositories\EloquentFavoriteRepository;
use App\Repositories\EloquentUserRepository;
use App\Services\AuthService;
use App\Services\External\TmdbService;
use App\Services\FavoriteService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // HTTP Client
        $this->app->singleton(HttpClientInterface::class, GuzzleHttpClient::class);

        // Movie API Service
        $this->app->singleton(MovieApiServiceInterface::class, TmdbService::class);

        // Repositories
        $this->app->singleton(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->singleton(FavoriteRepositoryInterface::class, EloquentFavoriteRepository::class);

        // Services
        $this->app->singleton(AuthServiceInterface::class, AuthService::class);
        $this->app->singleton(FavoriteServiceInterface::class, FavoriteService::class);
    }

    public function boot(): void {}
}
