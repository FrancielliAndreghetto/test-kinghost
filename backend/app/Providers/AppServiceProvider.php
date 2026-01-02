<?php

namespace App\Providers;

use App\Clients\GuzzleHttpClient;
use App\Contracts\Clients\HttpClientInterface;
use App\Contracts\Services\MovieApiServiceInterface;
use App\Services\External\TmdbService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {

        $this->app->singleton(HttpClientInterface::class, GuzzleHttpClient::class);

        $this->app->singleton(MovieApiServiceInterface::class, TmdbService::class);
    }

    public function boot(): void {}
}
