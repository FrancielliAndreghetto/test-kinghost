<?php

namespace App\Clients;

use App\Contracts\Clients\HttpClientInterface;
use Illuminate\Support\Facades\Http;

class GuzzleHttpClient implements HttpClientInterface
{
    private int $timeout;

    public function __construct()
    {
        $this->timeout = config('tmdb.timeout', 30);
    }

    public function get(string $url, array $params = []): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::timeout($this->timeout)
            ->get($url, $params);

        if ($response->failed()) {
            throw new \RuntimeException(
                "HTTP request failed with status {$response->status()}: {$response->body()}"
            );
        }

        return $response->json();
    }

    public function post(string $url, array $data = []): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::timeout($this->timeout)
            ->post($url, $data);

        if ($response->failed()) {
            throw new \RuntimeException(
                "HTTP request failed with status {$response->status()}: {$response->body()}"
            );
        }

        return $response->json();
    }
}
