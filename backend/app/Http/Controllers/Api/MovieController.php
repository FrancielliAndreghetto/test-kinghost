<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\MovieApiServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function __construct(
        private readonly MovieApiServiceInterface $movieApiService
    ) {}

    /**
     * Search movies by title
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:1',
            'page' => 'nullable|integer|min:1',
        ]);

        try {
            $movies = $this->movieApiService->searchMovies(
                $request->input('query'),
                $request->input('page', 1)
            );

            return response()->json($movies);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to search movies',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get movie details
     */
    public function show(int $id): JsonResponse
    {
        try {
            $movie = $this->movieApiService->getMovieDetails($id);

            return response()->json($movie);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get movie details',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get movie genres
     */
    public function genres(): JsonResponse
    {
        try {
            $genres = $this->movieApiService->getGenres();

            return response()->json(['genres' => $genres]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get genres',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get popular movies
     */
    public function popular(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'nullable|integer|min:1',
        ]);

        try {
            $movies = $this->movieApiService->getPopularMovies(
                $request->input('page', 1)
            );

            return response()->json($movies);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get popular movies',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get now playing movies
     */
    public function nowPlaying(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'nullable|integer|min:1',
        ]);

        try {
            $movies = $this->movieApiService->getNowPlayingMovies(
                $request->input('page', 1)
            );

            return response()->json($movies);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get now playing movies',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get upcoming movies
     */
    public function upcoming(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'nullable|integer|min:1',
        ]);

        try {
            $movies = $this->movieApiService->getUpcomingMovies(
                $request->input('page', 1)
            );

            return response()->json($movies);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get upcoming movies',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get top rated movies
     */
    public function topRated(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'nullable|integer|min:1',
        ]);

        try {
            $movies = $this->movieApiService->getTopRatedMovies(
                $request->input('page', 1)
            );

            return response()->json($movies);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get top rated movies',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
