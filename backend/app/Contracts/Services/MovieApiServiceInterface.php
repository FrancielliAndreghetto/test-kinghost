<?php

namespace App\Contracts\Services;

interface MovieApiServiceInterface
{
    public function searchMovies(string $query, int $page = 1): array;

    public function getMovieDetails(int $movieId): array;

    public function getGenres(): array;

    public function getPopularMovies(int $page = 1): array;

    public function getNowPlayingMovies(int $page = 1): array;

    public function getUpcomingMovies(int $page = 1): array;

    public function getTopRatedMovies(int $page = 1): array;
}
