<?php

namespace App\Services\External;

use App\Contracts\Clients\HttpClientInterface;
use App\Contracts\Services\MovieApiServiceInterface;
use App\Exceptions\MovieApiException;
use Illuminate\Support\Facades\Log;

class TmdbService implements MovieApiServiceInterface
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct(
        private readonly HttpClientInterface $httpClient
    ) {
        $this->baseUrl = config('tmdb.base_url');
        $this->apiKey = config('tmdb.api_key');

        if (empty($this->apiKey)) {
            throw new \RuntimeException('TMDB API Key is not configured');
        }
    }

    public function searchMovies(string $query, int $page = 1): array
    {
        try {
            $response = $this->httpClient->get("{$this->baseUrl}/search/movie", [
                'api_key' => $this->apiKey,
                'query' => $query,
                'page' => $page,
                'language' => 'pt-BR',
            ]);

            return [
                'results' => $response['results'] ?? [],
                'total_results' => $response['total_results'] ?? 0,
                'total_pages' => $response['total_pages'] ?? 0,
                'page' => $response['page'] ?? 1,
            ];
        } catch (\Exception $e) {
            Log::error('Error searching movies in TMDB', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            throw new MovieApiException('Failed to search movies: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getMovieDetails(int $movieId): array
    {
        try {
            $response = $this->httpClient->get("{$this->baseUrl}/movie/{$movieId}", [
                'api_key' => $this->apiKey,
                'language' => 'pt-BR',
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error('Error getting movie details from TMDB', [
                'movie_id' => $movieId,
                'error' => $e->getMessage(),
            ]);

            throw new MovieApiException('Failed to get movie details: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getGenres(): array
    {
        try {
            $response = $this->httpClient->get("{$this->baseUrl}/genre/movie/list", [
                'api_key' => $this->apiKey,
                'language' => 'pt-BR',
            ]);

            return $response['genres'] ?? [];
        } catch (\Exception $e) {
            Log::error('Error getting genres from TMDB', [
                'error' => $e->getMessage(),
            ]);

            throw new MovieApiException('Failed to get genres: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getPopularMovies(int $page = 1): array
    {
        try {
            $response = $this->httpClient->get("{$this->baseUrl}/movie/popular", [
                'api_key' => $this->apiKey,
                'page' => $page,
                'language' => 'pt-BR',
            ]);

            return [
                'results' => $response['results'] ?? [],
                'total_results' => $response['total_results'] ?? 0,
                'total_pages' => $response['total_pages'] ?? 0,
                'page' => $response['page'] ?? 1,
            ];
        } catch (\Exception $e) {
            Log::error('Error getting popular movies from TMDB', [
                'error' => $e->getMessage(),
            ]);

            throw new MovieApiException('Failed to get popular movies: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getNowPlayingMovies(int $page = 1): array
    {
        try {
            $response = $this->httpClient->get("{$this->baseUrl}/movie/now_playing", [
                'api_key' => $this->apiKey,
                'page' => $page,
                'language' => 'pt-BR',
            ]);

            return [
                'results' => $response['results'] ?? [],
                'total_results' => $response['total_results'] ?? 0,
                'total_pages' => $response['total_pages'] ?? 0,
                'page' => $response['page'] ?? 1,
            ];
        } catch (\Exception $e) {
            Log::error('Error getting now playing movies from TMDB', [
                'error' => $e->getMessage(),
            ]);

            throw new MovieApiException('Failed to get now playing movies: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getUpcomingMovies(int $page = 1): array
    {
        try {
            $response = $this->httpClient->get("{$this->baseUrl}/movie/upcoming", [
                'api_key' => $this->apiKey,
                'page' => $page,
                'language' => 'pt-BR',
            ]);

            return [
                'results' => $response['results'] ?? [],
                'total_results' => $response['total_results'] ?? 0,
                'total_pages' => $response['total_pages'] ?? 0,
                'page' => $response['page'] ?? 1,
            ];
        } catch (\Exception $e) {
            Log::error('Error getting upcoming movies from TMDB', [
                'error' => $e->getMessage(),
            ]);

            throw new MovieApiException('Failed to get upcoming movies: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getTopRatedMovies(int $page = 1): array
    {
        try {
            $response = $this->httpClient->get("{$this->baseUrl}/movie/top_rated", [
                'api_key' => $this->apiKey,
                'page' => $page,
                'language' => 'pt-BR',
            ]);

            return [
                'results' => $response['results'] ?? [],
                'total_results' => $response['total_results'] ?? 0,
                'total_pages' => $response['total_pages'] ?? 0,
                'page' => $response['page'] ?? 1,
            ];
        } catch (\Exception $e) {
            Log::error('Error getting top rated movies from TMDB', [
                'error' => $e->getMessage(),
            ]);

            throw new MovieApiException('Failed to get top rated movies: ' . $e->getMessage(), 0, $e);
        }
    }
}
