<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\FavoriteServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function __construct(
        private readonly FavoriteServiceInterface $favoriteService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $favorites = $this->favoriteService->getUserFavorites($request->user());

        return response()->json([
            'success' => true,
            'favorites' => $favorites,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'movie_id' => 'required|integer',
            'movie_title' => 'required|string',
            'poster_path' => 'nullable|string',
            'overview' => 'nullable|string',
            'vote_average' => 'nullable|numeric',
            'release_date' => 'nullable|date',
            'genre_ids' => 'nullable|array',
        ]);

        try {
            $favorite = $this->favoriteService->addFavorite($request->user(), $validated);

            return response()->json([
                'success' => true,
                'favorite' => $favorite,
                'message' => 'Movie added to favorites',
            ], 201);
        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 409);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(Request $request, int $movieId): JsonResponse
    {
        try {
            $this->favoriteService->removeFavorite($request->user(), $movieId);

            return response()->json([
                'success' => true,
                'message' => 'Movie removed from favorites',
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function check(Request $request, int $movieId): JsonResponse
    {
        $isFavorite = $this->favoriteService->isFavorite($request->user(), $movieId);

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite,
        ]);
    }
}